<?php
/**
 * Default values for theme settings dashboard options.
 *
 * @version 1.0
 * @package xts
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

return apply_filters(
	'xts_theme_default_values_array',
	array(
		'button_element_shape'  => 'round',
		// Theme settings.
		'blog_columns'          => '3',
		'blog_excerpt_length'   => '90',
		'blog_spacing'          => '30',
		'copyrights_right_text' => '[xts_social_buttons type="follow" style="default" size="s" align="inherit"]',
		'content_typography'    => array(
			0 => array(
				'custom'         => '',
				'google'         => '1',
				'font-family'    => 'Karla',
				'font-weight'    => '400',
				'font-style'     => '',
				'font-subset'    => '',
				'text-transform' => '',
				'font-size'      => '15',
				'tablet'         => array(
					'font-size'   => '',
					'line-height' => '',
				),
				'mobile'         => array(
					'font-size'   => '',
					'line-height' => '',
				),
				'line-height'    => '',
				'color'          => '#777777',
			),
		),
		'entities_typography'   => array(
			0 => array(
				'custom'         => '',
				'google'         => '0',
				'font-family'    => '',
				'font-weight'    => '700',
				'font-style'     => '',
				'font-subset'    => '',
				'text-transform' => '',
				'line-height'    => '',
				'tablet'         => array(
					'line-height' => '',
				),
				'mobile'         => array(
					'line-height' => '',
				),
				'color'          => '#333333',
				'hover'          => array(
					'color' => '#00b09f',
				),
			),
		),
		'footer_bg'             => array(
			'color'      => '#ffffff',
			'url'        => '',
			'id'         => '',
			'repeat'     => '',
			'size'       => '',
			'attachment' => '',
			'position'   => '',
			'position_x' => '0',
			'position_y' => '0',
			'css_output' => '1',
		),
		'footer_color_scheme'   => 'dark',
		'header_typography'     => array(
			0 => array(
				'custom'         => '',
				'google'         => '1',
				'font-family'    => 'Montserrat',
				'font-weight'    => '600',
				'font-style'     => '',
				'font-subset'    => '',
				'text-transform' => '',
				'font-size'      => '14',
				'tablet'         => array(
					'font-size'   => '',
					'line-height' => '',
				),
				'mobile'         => array(
					'font-size'   => '',
					'line-height' => '',
				),
				'line-height'    => '',
				'color'          => '#333333',
				'hover'          => array(
					'color' => '',
				),
				'active'         => array(
					'color' => '',
				),
			),
		),
		'links_color'           => array(
			'idle'       => '#00b09f',
			'hover'      => '#00acb2',
			'css_output' => '1',
		),
		'primary_color'         => array(
			'idle'       => '#00b09f',
			'css_output' => '1',
		),
		'secondary_color'       => array(
			'idle'       => '#00b09f',
			'css_output' => '1',
		),
		'site_width'            => '1280',
		'title_typography'      => array(
			0 => array(
				'custom'         => '',
				'google'         => '1',
				'font-family'    => 'Montserrat',
				'font-weight'    => '700',
				'font-style'     => '',
				'font-subset'    => '',
				'text-transform' => '',
				'line-height'    => '',
				'tablet'         => array(
					'line-height' => '',
				),
				'mobile'         => array(
					'line-height' => '',
				),
				'color'          => '#333333',
			),
		),
	)
);
