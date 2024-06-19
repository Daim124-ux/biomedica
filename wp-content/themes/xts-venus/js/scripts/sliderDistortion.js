/* global xts_settings */
(function($) {
	XTSThemeModule.$document.on('xtsElementorSliderReady', function() {
		XTSThemeModule.sliderDistortion();
	});

	XTSThemeModule.sliderDistortion = function() {
		var $elements = $('.xts-slider.xts-anim-distortion');

		if ('undefined' === typeof ShaderX || XTSThemeModule.$body.hasClass('single-xts-slide')) {
			return;
		}

		$elements.each(function() {
			var $slider = $(this),
			    $slides = $slider.find('.xts-slide'),
			    imgSrc  = $slides.eq(0).data('image-url'),
			    imgSrc2 = $slides.eq(1).data('image-url');

			if ($slider.hasClass('webgl-inited')) {
				return;
			}

			$slider.addClass('webgl-inited');

			var shaderX = new ShaderX({
				container     : $slider.find('.flickity-viewport'),
				sizeContainer : $slider,
				vertexShader  : XTSThemeModule.shaders.matrixVertex,
				fragmentShader: XTSThemeModule.shaders[xts_settings.slider_distortion_effect] ? XTSThemeModule.shaders[xts_settings.slider_distortion_effect] : XTSThemeModule.shaders.sliderWithWave,
				width         : $slider.outerWidth(),
				height        : $slider.outerHeight(),
				distImage     : xts_settings.slider_distortion_effect === 'sliderPattern' ? xts_settings.theme_url + '/images/dist11.jpg' : false
			});

			shaderX.loadImage(imgSrc, 0, function() {
				$slider.addClass('xts-canvas-image-loaded');
			});
			shaderX.loadImage(imgSrc, 1);
			shaderX.loadImage(imgSrc2, 0, undefined, true);

			$slider.on('change.flickity', function(event, index) {
				imgSrc = $slides.eq(index).data('image-url');
				if (!imgSrc) {
					return;
				}

				shaderX.replaceImage(imgSrc);
				if ($slides.eq(index + 1).length > 0) {
					imgSrc2 = $slides.eq(index + 1).data('image-url');
					if ( imgSrc2 ) {
						shaderX.loadImage(imgSrc2, 0, undefined, true);
					}
				}
			});
		});
	};

	$(document).ready(function() {
		XTSThemeModule.sliderDistortion();
	});
})(jQuery);