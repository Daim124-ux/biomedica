/* global xtsAdminConfig */

(function ($) {
	'use strict';

	$(document).on('click', '.xts-patch-apply', function (e) {
		e.preventDefault();

		var $this = $(this);
		var patchesMap = $this.data('patches-map');
		var themeSlug = 'xts-' + xtsAdminConfig.theme_slug.toLowerCase();
		var fileMap = [];

		for (var i = 0; i < patchesMap.length; i++) {
			fileMap[i] = themeSlug + '/' + patchesMap[i];
		}

		var confirmation = confirm(
			xtsAdminConfig.patcher_confirmation + '\r\r\n' + fileMap.join('\r\n')
		);

		if (!confirmation) {
			return;
		}
		addLoading();

		cleanNotice();

		$.ajax({
			url: xtsAdminConfig.ajaxUrl,
			data: {
				action: 'xts_patch_action',
				security: xtsAdminConfig.patcher_nonce,
				id: $this.data('id'),
			},
			timeout: 1000000,
			error: function () {
				printNotice(
					'error',
					'Something wrong with removing data. Please, try to remove data manually or contact our support center for further assistance.'
				);
			},
			success: function (response) {
				if ('undefined' !== typeof response.message) {
					printNotice(response.status, response.message);
				}

				if (
					'undefined' !== typeof response.status &&
					'success' === response.status
				) {
					$this.parents('.xts-patch-item').addClass('xts-applied');
					updatePatcherCounter();
				}

				removeLoading();
			},
		});
	});

	$(document).on('click', '.xts-patch-apply-all', function (e) {
		e.preventDefault();

		var $patches = $(
			'.xts-patch-item:not(.xts-patch-title-wrapper):not(.xts-applied)'
		).get();

		cleanNotice();

		if (0 === $patches.length) {
			printNotice('success', xtsAdminConfig.all_patches_applied);
			return;
		}

		if (!confirm(xtsAdminConfig.all_patches_confirm)) {
			return;
		}

		addLoading();
		recursiveApply($patches);
	});

	// Helpers.
	function recursiveApply($patches) {
		var $applyAllBtn = $('.xts-patch-apply-all');

		if (0 === $patches.length) {
			$applyAllBtn.parent().addClass('xts-applied');
			return;
		}

		var $patch = $($patches.pop());
		var id = $patch.find('.xts-patch-apply').data('id');

		$.ajax({
			url: xtsAdminConfig.ajaxUrl,
			data: {
				action: 'xts_patch_action',
				security: xtsAdminConfig.patcher_nonce,
				id,
			},
			timeout: 1000000,
			error: function () {
				printNotice('error', xtsAdminConfig.ajax_error);
				removeLoading();
			},
			success: function (response) {
				if (
					'undefined' !== typeof response.message &&
					'error' === response.status
				) {
					printNotice(response.status, response.message);
					$('xts-patcher-content').removeClass('xts-loading');
					removeLoading();
				}

				if (0 === $patches.length) {
					printNotice('success', xtsAdminConfig.all_patches_applied);
					$('xts-patcher-content').removeClass('xts-loading');
					
					removeLoading();
				}

				if (
					'undefined' !== typeof response.status &&
					'success' === response.status
				) {
					$patch.addClass('xts-applied');
					
					$applyAllBtn.parent().addClass('xts-applied');
					
					updatePatcherCounter();
					
					recursiveApply($patches);
					
				} else {
					removeLoading();
				}
			},
		});
	}
	
	function printNotice(type, message) {
		$('.xts-notices-wrapper').append(`
			<div class="xts-notice xts-${type}">
				${message}
			</div>
		`);

		$('.xts-notice:not(.xts-info)').on('click', function () {
			$(this).addClass('xts-hidden');
		});

		setTimeout(function () {
			$('.xts-notice.xts-success').addClass('xts-hidden');
		}, 10000);
	}

	function cleanNotice() {
		$('.xts-notices-wrapper').text('');
	}
	
	function addLoading() {
		$('.xts-patcher-content').addClass('xts-loading');
		$('.xts-patch-apply-all').addClass('xts-disabled');
	}

	function removeLoading() {
		$('.xts-patcher-content').removeClass('xts-loading');
		$('.xts-patch-apply-all').removeClass('xts-disabled');
	}

	function updatePatcherCounter() {
		var $counter = $('.xts-patcher-counter');

		if ($counter.length) {
			var $count = parseInt($counter.find('.patcher-count').text());

			if (1 === $count) {
				$counter.hide();
			} else {
				$counter.find('.patcher-count').text(--$count);
			}
		}
	}
})(jQuery);
