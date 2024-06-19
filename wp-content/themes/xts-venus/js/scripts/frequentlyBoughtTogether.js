/* global xts_settings */
(function($) {
	XTSThemeModule.frequentlyBoughtTogether = function () {
		$('form.xts-fbt-form').each( function () {
			var timeout = '';
			var $form = $(this);

			$form.on('change', '.xts-fbt-product input, .xts-fbt-product select', function () {
				var $this = $(this);
				var productsID = getProductsId($form);
				var mainProduct = $form.find('input[name=xts-fbt-main-product]').val();
				var btn = $form.find('.xts-fbt-purchase-btn');

				if ( ! productsID || 'undefined' === typeof productsID[mainProduct] ) {
					return;
				}

				if ( 2 > Object.keys(productsID).length ) {
					btn.addClass('xts-disabled');
				} else {
					btn.removeClass('xts-disabled');
				}

				var $carousel = $form.parents('.xts-fbt').find('.xts-carousel');

				if ($carousel.hasClass('xts-loaded')) {
					var swiper = $carousel.find('.swiper-container')[0].swiper;
					var index = $this.parents('.xts-fbt-product').index();

					if (1 === index && 'undefined' !== typeof swiper.params.slidesPerView && 1 < swiper.params.slidesPerView) {
						index = 0;
					}

					swiper.slideTo(index, 300);
				}

				clearTimeout(timeout);

				timeout = setTimeout(function () {
					updatePrice($form, productsID);
				}, 1000);
			});

			$form.on('click', '.xts-fbt-purchase-btn', function (e) {
				e.preventDefault();

				var $this       = $(this);

				if ( $this.hasClass('xts-disabled') ) {
					return;
				}

				var productsID  = getProductsId($form);
				var mainProduct = $form.find('input[name=xts-fbt-main-product]').val();
				var bundlesId   = $form.find('input[name=xts-fbt-bundle-id]').val();

				if ( ! productsID || 'undefined' === typeof productsID[mainProduct] ) {
					return;
				}

				clearTimeout(timeout);

				$this.addClass('loading');

				$.ajax({
					url     : xts_settings.ajaxurl,
					data    : {
						action        : 'xts_purchasable_fbt_products',
						products_id   : productsID,
						main_product  : mainProduct,
						bundle_id     : bundlesId,
						key           : xts_settings.frequently_bought,
					},
					method  : 'POST',
					success : function(response) {
						var $noticeWrapper = $('.woocommerce-notices-wrapper');
						$noticeWrapper.empty();

						if (response.notices && response.notices.indexOf('error') > 0) {
							$noticeWrapper.append(response.notices);

							var scrollTo = $noticeWrapper.offset().top - xts_settings.single_product_sticky_offset;

							$('html, body').stop().animate({
								scrollTop: scrollTo
							}, 400);

							return;
						}

						if ('undefined' !== typeof response.fragments) {
							if ('widget' === xts_settings.action_after_add_to_cart) {
								$.magnificPopup.close();
							}

							$this.addClass('added');

							XTSThemeModule.$body.trigger('added_to_cart', [
								response.fragments,
								response.cart_hash,
								''
							]);
						}
					},
					error   : function() {
						console.log('ajax error');
					},
					complete: function() {
						$this.removeClass('loading');
					}
				});
			});
		});

		function getProductsId($form) {
			var productsID = {};

			$form.find('.xts-fbt-product').each( function () {
				var $this = $(this);
				var $input = $(this).find('input');
				var productId = $this.data('id');
				var productWrapper = $form.parents('.xts-fbt');

				if ( $input.length ) {
					if ( $input.is(':checked') ) {
						if ( $this.find('.xts-fbt-product-variation').length ) {
							productsID[productId] = $this.find('.xts-fbt-product-variation select').val();
						} else {
							productsID[productId] = '';
						}

						productWrapper.find('.product.post-' + productId ).removeClass('xts-disabled-fbt');
					} else if ( ! $input.parents('.xts-fbt-form').hasClass('xts-checkbox-uncheck') ) {
						productWrapper.find('.product.post-' + productId).addClass('xts-disabled-fbt');
					} 
				} else {
					if ( $this.find('.xts-fbt-product-variation').length ) {
						productsID[productId] = $this.find('.xts-fbt-product-variation select').val();
					} else {
						productsID[productId] = '';
					}
				}
			});

			return productsID;
		}

		function updatePrice( $wrapper, productsID ) {
			var mainProduct = $wrapper.find('input[name=xts-fbt-main-product]').val();
			var bundleId    = $wrapper.find('input[name=xts-fbt-bundle-id]').val();

			$wrapper.find('.xts-loader-overlay').addClass( 'xts-loading' );

			$.ajax({
				url     : xts_settings.ajaxurl,
				data    : {
					action      : 'xts_update_frequently_bought_price',
					products_id : productsID,
					main_product: mainProduct,
					bundle_id   : bundleId,
					key         : xts_settings.frequently_bought,
				},
				method  : 'POST',
				success : function(response) {
					if (response.fragments) {
						$.each( response.fragments, function( key, value ) {
							$( key ).replaceWith(value);
						});
					}
				},
				error   : function() {
					console.log('ajax error');
				},
				complete: function() {
					$wrapper.find('.xts-loader-overlay').removeClass('xts-loading');
				}
			});
		}
	}

	$(document).ready(function() {
		XTSThemeModule.frequentlyBoughtTogether();
	});
})(jQuery);
