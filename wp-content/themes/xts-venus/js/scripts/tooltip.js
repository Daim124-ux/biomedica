/* global xts_settings */
(function($) {
	XTSThemeModule.$document.on('xtsWishlistRemoveSuccess xtsProductTabLoaded xtsElementorProductTabsReady xtsProductLoadMoreReInit xtsPortfolioPjaxComplete xtsProductQuickViewOpen xtsPjaxComplete updated_checkout updated_cart_totals xtsUpdateMiniCartItem', function () {
		XTSThemeModule.tooltip();
	});

	XTSThemeModule.$document.on('xtsPjaxStart xtsPortfolioPjaxStart', function () {
		XTSThemeModule.hideTooltip();
	});

	XTSThemeModule.$document.on('xtsUpdateTooltip', function (e, $this) {
		XTSThemeModule.updateTooltip($this);
	});

	$.each([
		'frontend/element_ready/xts_products.default',
		'frontend/element_ready/xts_single_product_tabs.default'
	], function(index, value) {
		XTSThemeModule.xtsElementorAddAction(value, function() {
			XTSThemeModule.tooltip();
		});
	});

	var tooltipConfig = {
		left : {
			selectors: xts_settings.tooltip_left_selector
		},
		top  : {
			selectors: xts_settings.tooltip_top_selector
		},
		right: {
			selectors: ''
		}
	};

	XTSThemeModule.tooltip = function() {
		if (XTSThemeModule.isTabletSize || 'undefined' === typeof ($.fn.tooltip)) {
			return;
		}

		var findTitle = function($el) {
			var text = $el.text();

			if ($el.find('a').length > 0) {
				text = $el.find('a').text();
			}

			if ($el.data('xts-tooltip')) {
				text = $el.data('xts-tooltip');
			}

			if ($el.find('.added_to_cart').length > 0) {
				text = $el.find('.add_to_cart_button').text();
			}

			return text;
		};

		var rtlPlacement = function(placement) {
			if ('left' === placement && XTSThemeModule.$body.hasClass('rtl')) {
				return 'right';
			}

			if ('right' === placement && XTSThemeModule.$body.hasClass('rtl')) {
				return 'left';
			}

			return placement;
		};

		$.each(tooltipConfig, function(key, value) {
			$(value.selectors).on('mouseenter touchstart', function() {
				var $this = $(this);

				if ( $this.hasClass('xts-tooltip-inited') ) {
					return;
				}

				$this.tooltip({
					animation: false,
					container: 'body',
					trigger: 'hover',
					boundary: 'window',
					placement: rtlPlacement(key),
					title: function() {
						return findTitle($this);
					},
				});

				$this.tooltip('show');

				$this.addClass('xts-tooltip-inited');
			});
		});
	};

	XTSThemeModule.hideTooltip = function() {
		if (XTSThemeModule.isTabletSize || 'undefined' === typeof ($.fn.tooltip)) {
			return;
		}

		$.each(tooltipConfig, function(key, value) {
			$(value.selectors).tooltip('hide');
		});
	};

	XTSThemeModule.updateTooltip = function($this) {
		if (XTSThemeModule.isTabletSize || !$($this).hasClass('xts-tooltip-inited') || 'undefined' === typeof ($.fn.tooltip) || !$($this).is(':hover')) {
			return;
		}

		$($this).tooltip('update').tooltip('show');
	};

	$(document).ready(function() {
		XTSThemeModule.tooltip();
	});
})(jQuery);