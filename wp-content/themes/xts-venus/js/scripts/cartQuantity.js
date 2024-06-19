(function($) {
	XTSThemeModule.cartQuantity = function() {
		var timeout;

		XTSThemeModule.$document.on('change input', '.woocommerce-cart-form__cart-item .quantity .qty', function(e) {
			var $input = $(this);
			console.log($input)
			clearTimeout(timeout);

			timeout = setTimeout(function() {
				$input.parents('.woocommerce-cart-form').find('button[name=update_cart]').trigger('click');
			}, 500);
		});
	};

	$(document).ready(function() {
		XTSThemeModule.cartQuantity();
	});
})(jQuery);