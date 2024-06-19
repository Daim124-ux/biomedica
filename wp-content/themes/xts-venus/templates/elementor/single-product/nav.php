<?php
/**
 * Product nav function
 *
 * @package xts
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

if ( ! function_exists( 'xts_single_product_nav_template' ) ) {
	/**
	 * Product nav template
	 *
	 * @since 1.0.0
	 *
	 * @param array $element_args Associative array of arguments.
	 */
	function xts_single_product_nav_template( $element_args ) {
		if ( ! xts_is_woocommerce_installed() ) {
			return;
		}

		$default_args = array(
			'style'        => 'default',
			'button_align' => '',
		);

		$element_args = wp_parse_args( $element_args, $default_args );

		$element_args['style'] = 'default' === $element_args['style'] ? '' : '-' . $element_args['style'];

		xts_get_template_part( 'woocommerce/single-product-navigation' . $element_args['style'] );
	}
}

