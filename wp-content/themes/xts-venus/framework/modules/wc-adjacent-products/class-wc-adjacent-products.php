<?php
/**
 * WooCommerce Adjacent Products Class
 *
 * @since    2.4.3
 * @package  xts
 */

namespace XTS\Modules;

use WC_Product;
use WP_Post;
use XTS\Framework\Module;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The WooCommerce Adjacent Products Class
 */
class WC_Adjacent_Products extends Module {

	/**
	 * The current product ID.
	 *
	 * @var int|null
	 */
	private $current_product = null;

	/**
	 * Whether post should be in a same taxonomy term.
	 *
	 * @var bool
	 */
	private $in_same_term = false;

	/**
	 * List of excluded term IDs.
	 *
	 * @var string
	 */
	private $excluded_terms = '';

	/**
	 * Whether to retrieve previous product.
	 *
	 * @var bool
	 */
	private $previous = false;

	/**
	 * Base initialization class required for Module class.
	 *
	 * @since 1.0.0
	 */
	public function init() {}

	/**
	 * Get adjacent product or circle back to the first/last valid product.
	 *
	 * @since 2.4.3
	 *
	 * @param bool         $previous Optional. Whether to retrieve previous product. Default false.
	 * @param bool         $in_same_term Optional. Whether post should be in a same taxonomy term. Default false.
	 * @param array|string $excluded_terms Optional. Comma-separated list of excluded term IDs. Default empty.
	 *
	 * @return WC_Product|false Product object if successful. False if no valid product is found.
	 */
	public function get_product( $previous = false, $in_same_term = false, $excluded_terms = '' ) {
		global $post;

		$this->previous       = $previous;
		$this->in_same_term   = $in_same_term;
		$this->excluded_terms = $excluded_terms;

		$product = false;

		$this->current_product = $post->ID;

		// Try to get a valid product via `get_adjacent_post()`.
		while ( $adjacent = $this->get_adjacent() ) {
			$product = wc_get_product( $adjacent->ID );

			if ( $product && $product->is_visible() ) {
				break;
			}

			$product               = false;
			$this->current_product = $adjacent->ID;
		}

		if ( $product ) {
			return $product;
		}

		// No valid product found; Query WC for first/last product.
		$product = $this->query_wc();

		if ( $product ) {
			return $product;
		}

		return false;
	}

	/**
	 * Get adjacent post.
	 *
	 * @since 2.4.3
	 *
	 * @return WP_POST|false Post object if successful. False if no valid post is found.
	 */
	private function get_adjacent() {
		$direction = $this->previous ? 'previous' : 'next';

		add_filter( 'get_' . $direction . '_post_where', array( $this, 'filter_post_where' ) );

		$adjacent = get_adjacent_post( $this->in_same_term, $this->excluded_terms, $this->previous, 'product_cat' );

		remove_action( 'get_' . $direction . '_post_where', array( $this, 'filter_post_where' ) );

		return $adjacent;
	}

	/**
	 * Filters the WHERE clause in the SQL for an adjacent post query, replacing the
	 * date with date of the next post to consider.
	 *
	 * @since 2.4.3
	 *
	 * @param string $where The `WHERE` clause in the SQL.
	 *
	 * @return WP_POST|false Post object if successful. False if no valid post is found.
	 */
	public function filter_post_where( $where ) {
		global $post;

		$new = get_post( $this->current_product );

		$where = str_replace( $post->post_date, $new->post_date, $where );

		return $where;
	}

	/**
	 * Query WooCommerce for either the first or last products.
	 *
	 * @since 2.4.3
	 *
	 * @return WC_Product|false Post object if successful. False if no valid post is found.
	 */
	private function query_wc() {
		global $post;

		$args = array(
			'limit'      => 2,
			'visibility' => 'catalog',
			'exclude'    => array( $post->ID ),
			'orderby'    => 'date',
			'status'     => 'publish',
		);

		if ( ! $this->previous ) {
			$args['order'] = 'ASC';
		}

		if ( $this->in_same_term ) {
			$terms = get_the_terms( $post->ID, 'product_cat' );

			if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
				$args['category'] = wp_list_pluck( $terms, 'slug' );
			}
		}

		$products = wc_get_products( apply_filters( 'xts_woocommerce_adjacent_query_args', $args ) );

		// At least 2 results are required, otherwise previous/next will be the same.
		if ( ! empty( $products ) && count( $products ) >= 2 ) {
			return $products[0];
		}

		return false;
	}
}
