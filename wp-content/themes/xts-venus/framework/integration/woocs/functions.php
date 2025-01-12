<?php
/**
 * FOX — Currency Switcher Professional for WooCommerce.
 *
 * @package xts
 */

if ( ! defined( 'WOOCS_VERSION' ) ) {
	return;
}

if ( ! function_exists( 'xts_woocs_convert_product_bundle_in_cart' ) ) {
	/**
	 * Back convector bundle product price.
	 *
	 * @param float  $price Product price.
	 * @param object $cart_item Product cart data.
	 * @return mixed|string
	 */
	function xts_woocs_convert_product_bundle_in_cart( $price, $cart_item ) {
		global $WOOCS;

		return $WOOCS->woocs_back_convert_price( $price );
	}

	add_filter( 'xts_fbt_set_product_cart_price', 'xts_woocs_convert_product_bundle_in_cart', 10, 2 );
	add_filter( 'xts_pricing_before_calculate_discounts', 'xts_woocs_convert_product_bundle_in_cart', 10, 2 );
}

if ( ! function_exists( 'xts_woocs_shipping_progress_bar_amount' ) ) {
	/**
	 * Converse shipping progress bar limit.
	 *
	 * @param float $limit
	 * @return float
	 */
	function xts_woocs_shipping_progress_bar_amount( $limit ) {
		global $WOOCS;

		$limit *= $WOOCS->get_sign_rate( array( 'sign' => $WOOCS->current_currency ) );

		return $limit;
	}

	add_filter( 'xts_shipping_progress_bar_amount', 'xts_woocs_shipping_progress_bar_amount' );
}

if ( ! function_exists( 'xts_woocs_convert_price' ) ) {
	/**
	 * Convector bundle product price.
	 *
	 * @param float $price Product price.
	 * @return mixed|string
	 */
	function xts_woocs_convert_price( $price ) {
		global $WOOCS; // phpcs:ignore.

		return $WOOCS->woocs_convert_price( $price ); // phpcs:ignore.
	}

	// Discount product price table.
	add_filter( 'xts_pricing_amount_discounts_value', 'xts_woocs_convert_price', 10, 1 );
}
