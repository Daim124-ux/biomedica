/* global xts_settings */
(function($) {
	XTSThemeModule.preloader = function() {
		$('.xts-preloader').delay(parseInt(xts_settings.preloader_delay)).addClass('xts-preloader-hide');
		$('.xts-preloader-style').remove();
		setTimeout(function() {
			$('.xts-preloader').remove();
		}, 200);
	};

	$(window).on('load', function() {
		XTSThemeModule.preloader();
	});
})(jQuery);