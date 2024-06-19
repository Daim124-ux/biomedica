<?php
/**
 * JS scripts.
 *
 * @version 1.0
 * @package xts
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

return array(
	'slider-distortion' => array(
		array(
			'title'     => esc_html__( 'Slider distortion script', 'xts-theme' ),
			'name'      => 'slider-distortion-method',
			'file'      => '/js/scripts/sliderDistortion',
			'in_footer' => true,
		),
		array(
			'title'     => esc_html__( 'Shaders script', 'xts-theme' ),
			'name'      => 'shaders-method',
			'file'      => '/js/scripts/shaders',
			'in_footer' => true,
		),
		array(
			'title'     => esc_html__( 'ShaderX script', 'xts-theme' ),
			'name'      => 'shaderX-method',
			'file'      => '/js/scripts/shaderX',
			'in_footer' => true,
		),
	),
);
