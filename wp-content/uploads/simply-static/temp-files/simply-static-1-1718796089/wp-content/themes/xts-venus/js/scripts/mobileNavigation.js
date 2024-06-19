/* global xts_settings */
(function ($) {
	XTSThemeModule.mobileNavigation = function () {
		var $body = XTSThemeModule.$body;
		var $mobileNav = $('.xts-side-mobile');
		var $dropDownCat = $('.xts-nav-mobile .menu-item-has-children');
		var $closeSide = $('.xts-close-side');
		var $search = $mobileNav.find('.searchform input[type=text]');
		var time = 200;

		$dropDownCat.append('<span class="xts-submenu-opener"></span>');

		$mobileNav.on('click', '.xts-submenu-opener', function (e) {
			e.preventDefault();
			var $this = $(this);

			if ($this.hasClass('xts-opened')) {
				$this
					.removeClass('xts-opened')
					.siblings('ul, div.xts-sub-menu')
					.slideUp(time);
			} else {
				$this
					.addClass('xts-opened')
					.siblings('ul, div.xts-sub-menu')
					.slideDown(time);
			}
		});

		$body.on(
			'click',
			'.xts-header-mobile-burger > a, .xts-navbar-burger',
			function (e) {
				e.preventDefault();

				if ($mobileNav.hasClass('xts-opened')) {
					closeMenu();
				} else {
					openMenu();
				}
			}
		);

		$mobileNav.on('click', '.xts-nav-mobile-tab a', function (e) {
			e.preventDefault();

			var $this = $(this).parent();
			var menuName = $this.data('menu');

			if ($this.hasClass('xts-active')) {
				return;
			}

			XTSThemeModule.$document.trigger('xtsImagesLoaded');

			$this.parent().find('.xts-active').removeClass('xts-active');
			$this.addClass('xts-active');
			$('.xts-nav-mobile').removeClass('xts-active');
			$('.xts-mobile-' + menuName).addClass('xts-active');
		});

		$body.on('click touchstart', '.xts-close-side', function () {
			closeMenu();
		});

		$body.on('click', '.xts-menu-item-account.xts-opener', function () {
			closeMenu();
		});

		XTSThemeModule.$document.on('keyup', function (e) {
			if (27 === e.keyCode) {
				closeMenu();
			}
		});

		function openMenu() {
			$mobileNav.addClass('xts-opened');
			$closeSide.addClass('xts-opened');
		}

		function closeMenu() {
			$mobileNav.removeClass('xts-opened');
			$closeSide.removeClass('xts-opened');
			$search.blur();
		}

		$('.xts-header-mobile-search').on('click', function (e) {
			e.preventDefault();

			if (XTSThemeModule.isDesktop) {
				return;
			}

			if (!$mobileNav.hasClass('xts-opened')) {
				openMenu();
				setTimeout(function () {
					$search.focus();
				}, 600);
			}
		});
	};

	$(document).ready(function () {
		XTSThemeModule.mobileNavigation();
	});
})(jQuery);
