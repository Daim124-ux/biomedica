<?php
/**
 * The framework's functions and definitions
 *
 * @package xts
 */

/**
 * Define constants.
 */
$slug = str_replace( 'xts-', '', get_template() );
update_option( 'xts_all_themes_license', $slug );
update_option( 'xts_' . $slug . '_license_active', '1' );
update_option( 'xts_' . $slug . '_license_key', '**********' );
update_option( 'xts_' . $slug . '_license_data', [
	'type' => ucwords( $slug . ' single license' ),
	'next_payment_date' => '01 Jan 2030',
] );

if ( ! defined( 'XTS_THEME_FILE' ) ) {
	define( 'XTS_THEME_FILE', __FILE__ );
}

if ( ! defined( 'XTS_ABSPATH' ) ) {
	define( 'XTS_ABSPATH', dirname( XTS_THEME_FILE ) . '/' );
}

define( 'XTS_THEME_SLUG', 'venus' );
define( 'XTS_BUILD_TYPE', 'space' );

require_once apply_filters( 'xts_framework_path', XTS_ABSPATH . 'framework/class-framework.php' );

require_once XTS_ABSPATH . 'theme/class-theme.php';

define( 'XTS_VERSION', xts_get_theme_info( 'Version' ) );
