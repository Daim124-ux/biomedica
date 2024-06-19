<?php
/**
 * Theme functions
 *
 * @package xts
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

use Elementor\Controls_Manager;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Image_Size;

if ( ! function_exists( 'xts_get_banner_gradient_control' ) ) {
	/**
	 * Add gradient control to banner element
	 *
	 * @since 1.0.0
	 *
	 * @param object $element Element object.
	 */
	function xts_get_banner_gradient_control( $element ) {
		$element->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'mask_gradient',
				'label'     => esc_html__( 'Background', 'xts-theme' ),
				'types'     => [ 'gradient' ],
				'selector'  => '{{WRAPPER}} .xts-iimage-overlay',
				'condition' => [
					'banner_style' => [ 'mask-gradient' ],
				],
			]
		);
	}

	add_action( 'xts_banner_style_general_after_banner_style', 'xts_get_banner_gradient_control' );
}

if ( ! function_exists( 'xts_get_banner_mask_bg_background_color_control' ) ) {
	/**
	 * Add bg control to banner element
	 *
	 * @since 1.0.0
	 *
	 * @param object $element Element object.
	 */
	function xts_get_banner_mask_bg_background_color_control( $element ) {
		$element->add_control(
			'mask_background',
			[
				'label'     => esc_html__( 'Background color', 'xts-theme' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .xts-iimage-overlay' => 'background-color: {{VALUE}};',
				],
				'condition' => [
					'banner_style' => [ 'mask-bg' ],
				],
			]
		);
	}

	add_action( 'xts_banner_style_general_after_banner_style', 'xts_get_banner_mask_bg_background_color_control' );
}

if ( ! function_exists( 'xts_get_banner_subtitle_color_preset_image_control' ) ) {
	/**
	 * Add image control to banner element
	 *
	 * @since 1.0.0
	 *
	 * @param object $element Element object.
	 */
	function xts_get_banner_subtitle_color_preset_image_control( $element ) {
		$element->add_control(
			'subtitle_image_color',
			[
				'label'     => esc_html__( 'Choose image', 'xts-theme' ),
				'type'      => Controls_Manager::MEDIA,
				'condition' => [
					'subtitle_color_presets' => [ 'image' ],
				],
			]
		);

		$element->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name'      => 'subtitle_image_color',
				'default'   => 'thumbnail',
				'separator' => 'none',
				'condition' => [
					'subtitle_color_presets' => [ 'image' ],
				],
			]
		);
	}

	add_action( 'xts_banner_style_subtitle_after_typography', 'xts_get_banner_subtitle_color_preset_image_control' );
}
