/* global xts_settings */
(function($) {
	XTSThemeModule.countProductVisits = function () {
		var live_duration = 10000;

		if ( 'undefined' !== typeof xts_settings.counter_visitor_live_duration ) {
			live_duration = xts_settings.counter_visitor_live_duration;
		}

		if ('yes' === xts_settings.counter_visitor_ajax_update) {
			XTSThemeModule.updateCountProductVisits();
		} else {
			setTimeout(function() {
				XTSThemeModule.updateCountProductVisits();
			}, live_duration);
		}
	}

	XTSThemeModule.updateCountProductVisits = function() {
		$('.xts-visits-count').each( function () {
			var $this = $(this);
			var productId = $this.data('product-id');
			var count = $this.find('.xts-visits-count-number').text();

			if ( ! productId ) {
				return;
			}

			$.ajax({
				url     : xts_settings.ajaxurl,
				data    : {
					action    : 'xts_update_count_product_visits',
					product_id: productId,
					count     : count,
				},
				method  : 'POST',
				success : function(response) {
					if (response) {
						$this.find('.xts-visits-count-number').html(response.count);

						if (!response.count) {
							$this.addClass('xts-hide');
						} else {
							$this.removeClass('xts-hide');
						}

						if ('yes' === response.live_mode) {
							setTimeout(function() { XTSThemeModule.countProductVisits() }, xts_settings.counter_visitor_live_duration);
						}
					}
				},
				error   : function() {
					console.log('ajax error');
				},
				complete: function() { }
			});
		});
	};

	$(document).ready(function() {
		XTSThemeModule.countProductVisits();
	});
})(jQuery);
