<?php
/**
 * Core.
 *
 * @package xts
 */

namespace XTS\Modules;

if ( ! defined( 'ABSPATH' ) ) {
	exit( 'No direct script access allowed' );
}

use XTS\Framework\Module;

/**
 * Core.
 *
 * @since 1.0.0
 */
class Core extends Module {
	/**
	 * Basic initialization class required for Module class.
	 *
	 * @since 1.0.0
	 */
	public function init() {
		require_once XTS_THEME_DIR . '/framework/modules/core/hooks.php';
		require_once XTS_THEME_DIR . '/framework/modules/core/shortcodes.php';
		require_once XTS_THEME_DIR . '/framework/modules/core/widgets.php';
		require_once XTS_THEME_DIR . '/framework/modules/core/post-type.php';
		require_once XTS_THEME_DIR . '/framework/modules/core/functions.php';
		require_once XTS_THEME_DIR . '/framework/modules/core/wc-social-authentication/class-wc-social-authentication.php';
		require_once XTS_THEME_DIR . '/framework/modules/core/class-twitter-api.php';
	}
}
