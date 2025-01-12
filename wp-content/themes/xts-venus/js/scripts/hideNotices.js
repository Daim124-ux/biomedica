/* global xts_settings */
(function($) {
	XTSThemeModule.hideNotices = function() {
		var notices = '.woocommerce-error, .woocommerce-info, .woocommerce-message, .wpcf7-response-output, .mc4wp-alert, .wc-block-components-notice-banner';

		XTSThemeModule.$body.on('click', notices, function(e) {
			var noticeItem   = $(this),
			    noticeHeight = noticeItem.outerHeight();

			if ( noticeItem.parents('.woocommerce-form-login-toggle, .woocommerce-form-coupon-toggle').length ) {
				return;
			}

			if ('a' !== $(e.target).prop('tagName').toLowerCase()) {
				noticeItem.css('height', noticeHeight);

				setTimeout(function() {
					noticeItem.addClass('xts-hide');
				}, 100);
			}
		});
	};

	$(document).ready(function() {
		XTSThemeModule.hideNotices();
	});
})(jQuery);
