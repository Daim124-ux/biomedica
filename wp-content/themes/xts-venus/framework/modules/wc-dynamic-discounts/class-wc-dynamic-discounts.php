<?php
/**
 * Frequently bought together class.
 *
 * @package xts
 */

namespace XTS\Modules;

use XTS\Framework\Module;
use XTS\Framework\Options;
use XTS\Modules\Dynamic_Discounts\Manager;
use WC_Cart;

/**
 * Frequently bought together class.
 */
class WC_Dynamic_Discounts extends Module {
	/**
	 * Make sure that the same discount is not applied twice for the same product.
	 *
	 * @var array A list of product IDs for which a discount has already been applied.
	 */
	public $applied;

	/**
	 * Init.
	 */
	public function init() {
		$this->applied = array();
		add_action( 'init', array( $this, 'add_options' ) );
		add_action( 'init', array( $this, 'include_files' ) );
		add_action( 'woocommerce_before_calculate_totals', array( $this, 'calculate_discounts' ), 10, 1 );
	}

	/**
	 * Include files.
	 *
	 * @return void
	 */
	public function include_files() {
		if ( ! xts_get_opt( 'dynamic_discounts_enabled' ) ) {
			return;
		}

		$files = array(
			'class-manager',
			'class-admin',
			'class-frontend',
		);

		foreach ( $files as $file ) {
			require_once XTS_FRAMEWORK_ABSPATH . '/modules/wc-dynamic-discounts/' . $file . '.php';
		}
	}

	/**
	 * Add options in theme settings.
	 */
	public function add_options() {
		Options::add_field(
			array(
				'id'          => 'dynamic_discounts_enabled',
				'name'        => esc_html__( 'Enable "Dynamic discounts"', 'xts-theme' ),
				'description' => esc_html__( 'You can configure your discounts in Dashboard -> Products -> Dynamic Discounts.', 'xts-theme' ),
				'group'       => esc_html__( 'Dynamic discounts', 'xts-theme' ),
				'type'        => 'switcher',
				'section'     => 'general_shop_section',
				'default'     => false,
				'on-text'     => esc_html__( 'Yes', 'xts-theme' ),
				'off-text'    => esc_html__( 'No', 'xts-theme' ),
				'priority'    => 160,
			)
		);

		Options::add_field(
			array(
				'id'          => 'show_dynamic_discounts_table',
				'name'        => esc_html__( 'Show discounts table', 'xts-theme' ),
				'description' => esc_html__( 'Dynamic pricing table on the single product page.', 'xts-theme' ),
				'group'       => esc_html__( 'Dynamic discounts', 'xts-theme' ),
				'type'        => 'switcher',
				'section'     => 'general_shop_section',
				'default'     => false,
				'on-text'     => esc_html__( 'Yes', 'xts-theme' ),
				'off-text'    => esc_html__( 'No', 'xts-theme' ),
				'priority'    => 170,
			)
		);
	}

	/**
	 *  Custom post type.
	 *
	 * @return array
	 */
	public function get_post_type_args() {
		return array(
			'label'              => esc_html__( 'Dynamic Discounts', 'xts-theme' ),
			'labels'             => array(
				'name'          => esc_html__( 'Dynamic Discounts', 'xts-theme' ),
				'singular_name' => esc_html__( 'Dynamic Discount', 'xts-theme' ),
				'menu_name'     => esc_html__( 'Dynamic Discounts', 'xts-theme' ),
				'add_new_item'  => esc_html__( 'Add New', 'xts-theme' ),
			),
			'supports'           => array( 'title' ),
			'hierarchical'       => false,
			'public'             => true,
			'show_in_menu'       => 'edit.php?post_type=product',
			'publicly_queryable' => false,
			'show_in_rest'       => true,
		);
	}

	/**
	 * Calculate price with discounts.
	 *
	 * @param WC_Cart $cart WC_Cart class.
	 *
	 * @return void
	 */
	public function calculate_discounts( $cart ) {
		if ( ! xts_get_opt( 'dynamic_discounts_enabled' ) ) {
			return;
		}
		// Woocommerce wpml compatibility. Make sure that the discount is calculated only once.
		if ( class_exists( 'woocommerce_wpml' ) && doing_action( 'woocommerce_cart_loaded_from_session' ) ) {
			return;
		}

		$variations_quantity = array();

		foreach ( $cart->get_cart() as $cart_item ) {
			if ( 'variation' !== $cart_item['data']->get_type() ) {
				continue;
			}

			if ( ! isset( $variations_quantity[ $cart_item['product_id'] ] ) ) {
				$variations_quantity[ $cart_item['product_id'] ] = 0;
			}

			$variations_quantity[ $cart_item['product_id'] ] += (int) $cart_item['quantity'];
		}

		foreach ( $cart->get_cart() as $cart_item ) {
			$product       = $cart_item['data'];
			$item_quantity = $cart_item['quantity'];
			$product_price = apply_filters( 'xts_pricing_before_calculate_discounts', (float) $product->get_price(), $cart_item );
			$discount      = Manager::get_instance()->get_discount_rules( $product );

			if ( empty( $discount ) || ( ! empty( $this->applied ) && in_array( $product->get_id(), $this->applied, true ) ) ) {
				continue;
			}

			$product->set_regular_price( $product_price );

			if ( ! empty( $variations_quantity ) && 'individual_product' === $discount['discount_quantities'] && in_array( $product->get_parent_id(), array_keys( $variations_quantity ), true ) ) {
				$item_quantity = $variations_quantity[ $product->get_parent_id() ];
			}

			switch ( $discount['xts_rule_type'] ) {
				case 'bulk':
					foreach ( $discount['discount_rules'] as $key => $discount_rule ) {
						if ( $discount_rule['xts_discount_rules_from'] <= $item_quantity && ( $item_quantity <= $discount_rule['xts_discount_rules_to'] || ( array_key_last( $discount['discount_rules'] ) === $key && empty( $discount_rule['xts_discount_rules_to'] ) ) ) ) {
							$discount_type  = $discount_rule['xts_discount_type'];
							$discount_value = $discount_rule[ 'xts_discount_' . $discount_type . '_value' ];

							// WPML woocommerce-multilingual compatibility.
							if ( function_exists( 'xts_wpml_shipping_progress_bar_amount' ) && 'amount' === $discount_type ) {
								$discount_value = xts_wpml_shipping_progress_bar_amount( $discount_value );
							}

							$product_price = $this->get_product_price(
								$product_price,
								array(
									'type'  => $discount_type,
									'value' => $discount_value,
								)
							);
						}
					}
					break;
			}

			$product_price = apply_filters( 'xts_pricing_after_calculate_discounts', $product_price, $cart_item );

			if ( $product_price < 0 ) {
				$product_price = 0;
			}

			$product->set_price( $product_price );
			$product->set_sale_price( $product_price );

			$this->applied[] = $product->get_id();
		}
	}

	/**
	 * Get product price after applying discount.
	 *
	 * @param float $product_price Price before applying discount.
	 * @param array $discount Array with 2 args('type', 'value') for calculate new price.
	 *
	 * @return float
	 */
	public function get_product_price( $product_price, $discount ) {
		if ( empty( $discount['type'] ) || empty( $discount['value'] ) || ! $product_price ) {
			return $product_price;
		}

		switch ( $discount['type'] ) {
			case 'amount':
				$product_price -= $discount['value'];
				break;
			case 'percentage':
				$product_price -= $product_price * ( $discount['value'] / 100 );
				break;
			default:
				break;
		}

		return (float) $product_price;
	}
}
