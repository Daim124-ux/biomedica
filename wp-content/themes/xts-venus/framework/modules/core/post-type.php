<?php
/**
 * Add post type for template.
 *
 * @package xts.
 */

use XTS\Framework\Modules;

if ( ! function_exists( 'xts_register_post_type' ) ) {
	/**
	 * Register post type.
	 */
	function xts_register_post_type() {
		register_post_type( 'xts-html-block', xts_get_html_block_post_type_args() );
		register_taxonomy( 'xts-html-block-cat', array( 'xts-html-block' ), xts_get_html_block_taxonomy_args() );

		register_post_type( 'xts-slide', xts_get_slider_post_type_args() );
		register_taxonomy( 'xts_slider', array( 'xts-slide' ), xts_get_slider_taxonomy_args() );

		register_post_type( 'xts-sidebar', xts_get_sidebar_post_type_args() );

		if ( xts_get_opt( 'portfolio', true ) ) {
			register_post_type( 'xts-portfolio', xts_get_portfolio_post_type_args() );
			register_taxonomy( 'xts-portfolio-cat', array( 'xts-portfolio' ), xts_get_portfolio_taxonomy_args() );
		}

		if ( xts_is_woocommerce_installed() ) {
			register_post_type( 'xts-template', Modules::get( 'wc-builder' )->get_template_post_type_args() );
		}

		if ( xts_get_opt( 'single_product_size_guide' ) ) {
			register_post_type( 'xts-size-guide', Modules::get( 'wc-size-guide' )->get_size_guide_post_type_args() );
		}

		if ( xts_is_woocommerce_installed() && xts_get_opt( 'bought_together_enabled', 1 ) ) {
			register_post_type( 'xts_woo_fbt', Modules::get( 'wc-frequently-bought-together' )->get_fbt_post_type_args() );
		}

		if ( xts_is_woocommerce_installed() && xts_get_opt( 'dynamic_discounts_enabled' ) ) {
			register_post_type( 'xts_woo_discounts', Modules::get( 'wc-dynamic-discounts' )->get_post_type_args() );
		}

		if ( xts_is_woocommerce_installed() && xts_get_opt( 'linked_variations', 1 ) ) {
			register_post_type( 'xts_linked_variation', Modules::get( 'wc-linked-variation' )->get_post_type_args() );
		}
	}

	add_action( 'init', 'xts_register_post_type' );
}
