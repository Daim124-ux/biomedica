<?php
/**
 * The main patcher class.
 *
 * @package XTS
 */

namespace XTS\Modules;

use XTS\Framework\Module;
use XTS\Modules\Patcher\Patch;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

/**
 * The main patcher class.
 */
class Patcher extends Module {
	/**
	 * Register hooks.
	 */
	public function init() {
		$this->include_files();
		add_filter( 'xts_admin_localized_string_array', array( $this, 'localize_patcher_messages' ) );
		add_action( 'wp_ajax_xts_patch_action', array( $this, 'patch_process' ) );
	}

	/**
	 * Include files.
	 */
	public function include_files() {
		require_once XTS_THEME_DIR . '/framework/modules/patcher/class-client.php';
		require_once XTS_THEME_DIR . '/framework/modules/patcher/class-patch.php';
	}

	/**
	 * Localize messages.
	 *
	 * @since 1.6.0
	 * @param array $localize_data Data to localize.
	 * @return array
	 */
	public function localize_patcher_messages( $localize_data ) {
		$localize_data['all_patches_confirm']  = esc_html__( 'Are you sure you want to download all patches?', 'xts-theme' );
		$localize_data['all_patches_applied']  = esc_html__( 'All patches are applied.', 'xts-theme' );
		$localize_data['patcher_confirmation'] = esc_html__( 'These files will be updated:', 'xts-theme' );
		$localize_data['patcher_nonce']        = wp_create_nonce( 'patcher_nonce' );
		return $localize_data;
	}

	/**
	 * Patch process.
	 */
	public function patch_process() {
		check_ajax_referer( 'patcher_nonce', 'security' );

		if ( empty( $_GET['id'] ) ) {
			wp_send_json(
				array(
					'message' => esc_html__( 'Empty path ID, please, try again.', 'xts-theme' ),
					'status'  => 'error',
				)
			);
		}

		$patch_id          = sanitize_text_field( $_GET['id'] ); //phpcs:ignore
		$patches_installed = get_option( 'xts_successfully_installed_patches' );
		$theme_version     = XTS_VERSION;

		if ( isset( $patches_installed[ $theme_version ][ $patch_id ] ) ) {
			wp_send_json(
				array(
					'message' => esc_html__( 'The patch is already applied.', 'xts-theme' ),
					'status'  => 'success',
				)
			);
		}

		new Patch( $patch_id );
	}
}
