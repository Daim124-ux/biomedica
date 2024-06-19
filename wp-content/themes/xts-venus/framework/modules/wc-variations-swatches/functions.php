<?php
/**
 * Swatches helper functions.
 *
 * @since 1.0
 *
 * @package xts
 */

use XTS\Framework\Modules;

if ( ! function_exists( 'xts_grid_swatches_template' ) ) {
	/**
	 * Grid swatches.
	 *
	 * @since 1.0
	 */
	function xts_grid_swatches_template() {
		$swatches = Modules::get( 'wc-variations-swatches' );
		$swatches->grid_swatches_template();
	}
}

if ( ! function_exists( 'xts_grid_variations_template' ) ) {
	/**
	 * Grid _variations.
	 *
	 * @since 1.0
	 */
	function xts_grid_variations_template() {
		$swatches = Modules::get( 'wc-variations-swatches' );
		$swatches->grid_variations_template();
	}
}

if ( ! function_exists( 'xts_show_out_of_stock_variation_products' ) ) {
	/**
	 * Show out of stock items.
	 *
	 * @since 1.6.0
	 */
	function xts_show_out_of_stock_variation_products() {
		if ( 'yes' === get_option( 'woocommerce_hide_out_of_stock_items' ) ) {
			xts_set_loop_prop( 'fbt_hide_out_of_stock_product', 'yes' );
			add_filter( 'option_woocommerce_hide_out_of_stock_items', '__return_false' );
			add_action( 'woocommerce_variable_add_to_cart', 'xts_hide_out_of_stock_variation_products', 40 );
		}
	}

	add_action( 'woocommerce_variable_add_to_cart', 'xts_show_out_of_stock_variation_products', 20 );
}

if ( ! function_exists( 'xts_hide_out_of_stock_variation_products' ) ) {
	/**
	 * Hide out of stock items.
	 *
	 * @since 1.6.0
	 */
	function xts_hide_out_of_stock_variation_products() {
		if ( '1' === xts_get_loop_prop( 'fbt_hide_out_of_stock_product' ) ) {
			remove_filter( 'option_woocommerce_hide_out_of_stock_items', '__return_false' );
		}
	}
}
