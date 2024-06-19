<?php
/**
 * links for theme settings and elements.
 *
 * @version 1.0
 * @package xts
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

return apply_filters(
	'xts_links_theme_settings_array',
	array(
		'welcome_all_themes'          => XTS_SPACE_URL . 'wordpress-themes',
		'welcome_forum'               => XTS_SPACE_URL . 'forums/forum/hitek-support-forum',
		'activation_find_license_key' => XTS_SPACE_URL . 'my-account',
		'activation_purchase'         => XTS_SPACE_URL . 'pricing',
		'activation_go_to_account'    => 'https://xtemos.com/my-account',
	)
);
