<?php
/**
 * Options for theme settings and elements.
 *
 * @version 1.0
 * @package xts
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

return apply_filters(
	'xts_theme_available_options_array',
	array(
		'items_gap_elementor'                     => array(
			40 => esc_html__( '40 px', 'xts-theme' ),
		),

		'items_gap'                               => array(
			40 => array(
				'name'  => 40,
				'value' => 40,
			),
		),

		'slide_change_animation'                  => array(
			'distortion' => array(
				'name'  => esc_html__( 'Distortion', 'xts-theme' ),
				'value' => 'distortion',
			),
		),

		'footer_layout'                           => array(
			17 => array(
				'name'  => esc_html__( 'Five columns with last wide', 'xts-theme' ),
				'value' => 17,
				'image' => XTS_ASSETS_IMAGES_URL . '/options/footer/layout/footer-17.svg',
			),
		),

		'blog_single_design'                      => array(
			'mask' => array(
				'name'  => esc_html__( 'Mask', 'xts-theme' ),
				'value' => 'mask',
			),
		),

		'title_element_design_elementor'          => array(
			'bordered-text' => esc_html__( 'Bordered text', 'xts-theme' ),
		),

		'banner_design_elementor'                 => array(
			'mask-gradient' => esc_html__( 'Mask with gradient', 'xts-theme' ),
			'mask-bg'       => esc_html__( 'Mask with background', 'xts-theme' ),
		),

		'banner_subtitle_color_presets_elementor' => array(
			'image' => esc_html__( 'Image', 'xts-theme' ),
		),

		'search_icon_style_header_builder'        => array(
			'icon-bg' => array(
				'value' => 'icon-bg',
				'label' => esc_html__( 'Icon only with background', 'xts-theme' ),
			),
		),
	)
);
