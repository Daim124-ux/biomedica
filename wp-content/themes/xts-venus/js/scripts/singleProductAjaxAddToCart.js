/* global xts_settings */
(function($) {
	XTSThemeModule.singleProductAjaxAddToCart = function() {
		if ('no' === xts_settings.single_product_ajax_add_to_cart) {
			return;
		}

		XTSThemeModule.$body.on('submit', 'form.cart', function(e) {
			var $form = $(this);

			var $productWrapper = $form.parents('.product');

			if ($productWrapper.hasClass('product-type-external') || $productWrapper.hasClass('product-type-zakeke') || 'undefined' !== typeof e.originalEvent && $(e.originalEvent.submitter).hasClass('xts-buy-now-btn')) {
				return;
			}

			e.preventDefault();

			var $button = $form.find('.single_add_to_cart_button');
			var data = $form.serialize();

			data += '&action=xts_single_product_ajax_add_to_cart';

			if ($button.val()) {
				data += '&add-to-cart=' + $button.val();
			}

			$button.removeClass('added xts-not-added').addClass('loading');

			// Trigger event
			$(document.body).trigger('adding_to_cart', [
				$button,
				data
			]);

			$.ajax({
				url    : xts_settings.ajaxurl,
				data   : data,
				method : 'POST',
				success: function(response) {
					if (!response) {
						return;
					}

					if (response.error && response.product_url) {
						window.location = response.product_url;
						return;
					}

					// Redirect to cart option
					if ('yes' === xts_settings.cart_redirect_after_add) {
						window.location = xts_settings.action_after_add_to_cart_cart_url;
					} else {
						$button.removeClass('loading');

						var fragments = response.fragments;
						var cart_hash = response.cart_hash;

						// Block fragments class
						if (fragments) {
							$.each(fragments, function(key) {
								$(key).addClass('xts-updating');
							});
						}

						// Replace fragments
						if (fragments) {
							$.each(fragments, function(key, value) {
								$(key).replaceWith(value);
							});
						}

						// Show notices
						if ($productWrapper.hasClass('xts-product') && response.notices_raw.error) {
							var error = response.notices_raw.error;
							var errorsRow = '';

							$.each(error, function () {
								var errorRow = this.notice;
								errorsRow += htmlDecode(errorRow.substring(errorRow.indexOf('</a>'), errorRow.lastIndexOf('')) + '<br>');
							})

							alert(errorsRow);
							$productWrapper.find('.add_to_cart_button').removeClass('loading');
						} else if (response.notices.indexOf('error') > 0) {
							$('.woocommerce-notices-wrapper').append(response.notices);
							$button.addClass('xts-not-added');
						} else {
							if ('widget' === xts_settings.action_after_add_to_cart) {
								$.magnificPopup.close();
							}

							// Trigger event so themes can refresh other areas
							$(document.body).trigger('added_to_cart', [
								fragments,
								cart_hash,
								$button
							]);
						}
					}
				},
				error  : function() {
					console.log('ajax adding to cart error');
				}
			});
		});

		XTSThemeModule.$body.on('click', '.xts-sticky-atc .xts-buy-now-btn', function() {
			if ($(this).parents('form.cart').length) {
				return;
			}

			$('form.cart').find('.xts-buy-now-btn').trigger('click');
		});

		XTSThemeModule.$body.on('click', '.variations_form .xts-buy-now-btn', function(e) {
			var $this = $(this);
			var $addToCartBtn = $this.siblings('.single_add_to_cart_button');

			if ( 'undefined' !== typeof wc_add_to_cart_variation_params && $addToCartBtn.hasClass('disabled') ) {
				e.preventDefault();

				if ($addToCartBtn.hasClass('wc-variation-is-unavailable') ) {
					alert( wc_add_to_cart_variation_params.i18n_unavailable_text );
				} else if ( $addToCartBtn.hasClass('wc-variation-selection-needed') ) {
					alert( wc_add_to_cart_variation_params.i18n_make_a_selection_text );
				}
			}
		});
	};

	function htmlDecode(input) {
		var doc = new DOMParser().parseFromString(input, "text/html");
		return doc.documentElement.textContent;
	}

	$(document).ready(function() {
		XTSThemeModule.singleProductAjaxAddToCart();
	});
})(jQuery);