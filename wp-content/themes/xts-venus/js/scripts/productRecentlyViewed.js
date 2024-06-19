/* global xts_settings */
(function($) {
	XTSThemeModule.productRecentlyViewed = function() {
		$('.xts-products').each( function () {
			var $this = $(this);
			var attr = $this.data('atts');

			if ( 'undefined' === typeof attr || 'undefined' === typeof attr.product_source || 'recently_viewed' !== attr.product_source || 'undefined' === typeof attr.ajax_recently_viewed || '1' !== attr.ajax_recently_viewed ) {
				return;
			}

			$.ajax({
				url     : xts_settings.ajaxurl,
				data    : {
					attr  : attr,
					action: 'xts_get_recently_viewed_products'
				},
				dataType: 'json',
				method  : 'POST',
				success : function(data) {
					if (data.items) {
						$this.replaceWith(data.items);

						XTSThemeModule.$document.trigger('xtsProductLoadMoreReInit');
						XTSThemeModule.$document.trigger('xtsImagesLoaded');
					}
				},
				error   : function() {
					console.log('ajax error');
				},
			});
		})
	};

	$(document).ready(function() {
		XTSThemeModule.productRecentlyViewed();
	});
})(jQuery);