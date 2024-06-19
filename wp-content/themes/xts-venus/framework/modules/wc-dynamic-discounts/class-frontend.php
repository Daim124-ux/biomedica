<?php
/**
 * Render dynamic discounts on frontend.
 *
 * @package xts
 */

namespace XTS\Modules\Dynamic_Discounts;

use WC_Product;
use XTS\Framework\Modules;
use XTS\Singleton;
use XTS\Modules\WC_Dynamic_Discounts;

/**
 * Dynamic discounts class.
 */
class Frontend extends Singleton {
	/**
	 * Init.
	 */
	public function init() {
		add_action( 'init', array( $this, 'hooks' ), 100 );
	}

	/**
	 * Init hooks.
	 */
	public function hooks() {
		if ( ! xts_get_opt( 'show_dynamic_discounts_table' ) ) {
			return;
		}
		add_filter( 'woocommerce_cart_item_price', array( $this, 'cart_item_price' ), 10, 2 );
		add_filter( 'woocommerce_before_mini_cart_contents', array( $this, 'cart_item_price_on_ajax' ), 10, 2 );
		add_action( 'woocommerce_single_product_summary', array( $this, 'render_dynamic_discounts_table' ), 25 );
		add_action( 'wp_ajax_xts_update_discount_dynamic_discounts_table', array( $this, 'update_dynamic_discounts_table' ) );
		add_action( 'wp_ajax_nopriv_xts_update_discount_dynamic_discounts_table', array( $this, 'update_dynamic_discounts_table' ) );
	}



	/**
	 * Update price in mini cart on get_refreshed_fragments action.
	 *
	 * @codeCoverageIgnore
	 * @return void
	 */
	public function cart_item_price_on_ajax() {
		if ( defined( 'WOOCS_VERSION' ) ) {
			return;
		}

		if ( wp_doing_ajax() && ! empty( $_GET['wc-ajax'] ) && 'get_refreshed_fragments' === $_GET['wc-ajax'] ) { // phpcs:ignore.
			WC()->cart->calculate_totals();
			WC()->cart->set_session();
			WC()->cart->maybe_set_cart_cookies();
		}
	}

	/**
	 * Get unit of measure.
	 *
	 * @param object $product Product object.
	 * @return string
	 */
	public function get_unit_of_measure( $product ) {
		$unit = Modules::get( 'wc-unit-of-measure' );
		return $unit->get_unit_of_measure_db( $product );
	}

	/**
	 * Update price in cart.
	 *
	 * @param string $price_html Product price.
	 * @param array  $cart_item Product data.
	 * @return string
	 */
	public function cart_item_price( $price_html, $cart_item ) {
		$product       = $cart_item['data'];
		$regular_price = $product->get_regular_price();
		$sale_price    = $product->get_price();

		if ( $regular_price === $sale_price ) {
			return $price_html;
		}

		if ( wc_tax_enabled() ) {
			if ( 'incl' === get_option( 'woocommerce_tax_display_cart' ) ) {
				$sale_price = wc_get_price_including_tax( $product, array( 'price' => $sale_price ) );
			} else {
				$sale_price = wc_get_price_excluding_tax( $product, array( 'price' => $sale_price ) );
			}
		}
		$unit_of_measure = $this->get_unit_of_measure( $product );

		ob_start();

		?>
							 
			<?php echo wc_price( $sale_price ); // phpcs:ignore. ?>

			<?php if ( ! empty( $unit_of_measure ) ) : ?>
				<span class="xts-price-unit">
					 <?php echo wp_kses_post( $unit_of_measure  ); //phpcs:ignore. ?>
				</span>
			<?php endif; ?>
		<?php

		return ob_get_clean();
	}

	/**
	 * Render dynamic discounts table.
	 *
	 * @codeCoverageIgnore
	 * @param false|int|string $product_id The product id for which you want to generate the dynamic discounts table. Default is equal false.
	 * @param string           $wrapper_classes Wrapper classes string.
	 * @return false|string
	 */
	public function render_dynamic_discounts_table( $product_id = false, $wrapper_classes = '' ) {
		if ( ! $product_id ) {
			$product_id = is_ajax() && ! empty( wp_unslash( $_GET['variation_id'] ) ) ? wp_unslash( $_GET['variation_id'] ) : false; // phpcs:ignore.
		}

		$product = wc_get_product( $product_id );

		if ( ! $product || empty( $product->get_price() ) ) {
			return false;
		}

		$product_type = $product->get_type();

		if ( 'grouped' === $product_type || 'external' === $product_type ) {
			return false;
		}

		$discount = Manager::get_instance()->get_discount_rules( $product );
		$data     = array();

		if ( ! Manager::get_instance()->check_discount_exist( $product ) || 'bulk' !== $discount['xts_rule_type'] ) {
			return false;
		}

		// Add last rule for render table.
		$last_rules = end( $discount['discount_rules'] );

		if ( ! empty( $last_rules['xts_discount_rules_to'] ) ) {
			$discount['discount_rules']['last'] = array(
				'xts_discount_rules_from'       => $last_rules['xts_discount_rules_to'] + 1,
				'xts_discount_rules_to'         => '',
				'xts_discount_type'             => 'amount',
				'xts_discount_amount_value'     => 0,
				'xts_discount_percentage_value' => '',
			);
		}

		xts_enqueue_js_script( 'dynamic-discounts' );

		foreach ( $discount['discount_rules'] as $id => $rules ) {
			// Quantity min.
			$data[ $id ]['min'] = $rules['xts_discount_rules_from'];

			// Quantity max.
			$data[ $id ]['max'] = $rules['xts_discount_rules_to'];

			// Quantity column.
			if ( $rules['xts_discount_rules_from'] === $rules['xts_discount_rules_to'] ) {
				$data[ $id ]['quantity'] = $rules['xts_discount_rules_from'];
			} else {
				$data[ $id ]['quantity'] = sprintf(
					'%s%s%s',
					$data[ $id ]['min'],
					! empty( $rules['xts_discount_rules_to'] ) ? '-' : '',
					! empty( $rules['xts_discount_rules_to'] ) ? $rules['xts_discount_rules_to'] : '+'
				);
			}

			// Discount column.
			$data[ $id ]['discount'] = sprintf(
				'%s%s',
				'amount' === $rules['xts_discount_type'] ? apply_filters( 'xts_pricing_amount_discounts_value', $rules['xts_discount_amount_value'] ) : $rules['xts_discount_percentage_value'],
				'amount' === $rules['xts_discount_type'] ? get_woocommerce_currency_symbol() : '%'
			);

			// Price column.
			$discount      = new WC_Dynamic_Discounts();
			$product_price = $discount->get_product_price(
				$product->get_price(),
				array(
					'type'  => $rules['xts_discount_type'],
					'value' => 'amount' === $rules['xts_discount_type'] ? apply_filters( 'xts_pricing_amount_discounts_value', $rules['xts_discount_amount_value'] ) : $rules['xts_discount_percentage_value'],
				)
			);

			if ( wc_tax_enabled() ) {
				if ( 'incl' === get_option( 'woocommerce_tax_display_shop' ) ) {
					$product_price = wc_get_price_including_tax( $product, array( 'price' => $product_price ) );
				} else {
					$product_price = wc_get_price_excluding_tax( $product, array( 'price' => $product_price ) );
				}
			}

			if ( $product_price < 0 ) {
				$product_price = 0;
			}

			$data[ $id ]['price'] = wc_price( $product_price );

			$data[ $id ]['unit_of_measure'] = $this->get_unit_of_measure( $product );
		}

		if ( empty( $data ) ) {
			return false;
		}

		if ( is_ajax() ) {
			ob_start();
		}

		xts_enqueue_js_script( 'dynamic-discounts-table' );

		if ( \Elementor\Plugin::$instance->editor->is_edit_mode() || ! is_ajax() || ( is_product() && ( ! isset( $_REQUEST['action'] ) || 'xts_update_discount_dynamic_discounts_table' !== $_REQUEST['action'] ) ) ) : // phpcs:ignore ?>
			<div class="xts-dynamic-discounts <?php echo esc_attr( $wrapper_classes ); ?>">
		<?php endif; ?>
		
		<?php
			wc_get_template(
				'single-product/price-table.php',
				array(
					'data' => $data,
				)
			);

		?>
		
		<div class="xts-loader-overlay xts-fill"></div>

		<?php if ( ! is_ajax() ) : ?>
			</div>
		<?php endif; ?>
		<?php
		if ( is_ajax() ) {
			return ob_get_clean();
		}
	}

	/**
	 * Send new price table html for current variation product.
	 *
	 * @codeCoverageIgnore
	 * @return void
	 */
	public function update_dynamic_discounts_table() {
		$variation_id = wp_unslash( $_GET['variation_id'] ); // phpcs:ignore.

		if ( empty( $variation_id ) || ! wc_get_product( $variation_id ) instanceof WC_Product ) {
			return;
		}

		wp_send_json(
			apply_filters( 'xts_variation_dynamic_discounts_table', $this->render_dynamic_discounts_table( $variation_id ) )
		);
	}
}

Frontend::get_instance();
