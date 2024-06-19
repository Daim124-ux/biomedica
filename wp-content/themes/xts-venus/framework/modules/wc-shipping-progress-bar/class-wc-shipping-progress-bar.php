<?php
/**
 * Shipping progress bar.
 *
 * @package xts
 */

namespace XTS\Modules;

if ( ! defined( 'ABSPATH' ) ) {
	exit( 'No direct script access allowed' );
}

use XTS\Framework\Module;
use XTS\Framework\Options;

/**
 * Shipping progress bar.
 *
 * @since 1.0.0
 */
class WC_Shipping_Progress_Bar extends Module {
	/**
	 * Basic initialization class required for Module class.
	 *
	 * @since 1.0.0
	 */
	public function init() {
		add_action( 'init', array( $this, 'add_options' ), 10 );

		add_action( 'wp', array( $this, 'output_shipping_progress_bar' ), 100 );
		add_action( 'init', array( $this, 'output_shipping_progress_bar_in_mini_cart' ), 100 );
	}

	/**
	 * Add options in theme settings.
	 */
	public function add_options() {
		Options::add_section(
			array(
				'id'       => 'shipping_progress_bar',
				'parent'   => 'shop_section',
				'name'     => esc_html__( 'Free shipping bar', 'xts-theme' ),
				'priority' => 100,
				'icon'     => 'xf-shop',
			)
		);

		Options::add_field(
			array(
				'id'          => 'shipping_progress_bar_enabled',
				'name'        => esc_html__( 'Free shipping bar', 'xts-theme' ),
				'description' => esc_html__( 'Display a free shipping progress bar on the website.', 'xts-theme' ),
				'type'        => 'switcher',
				'section'     => 'shipping_progress_bar',
				'default'     => '0',
				'priority'    => 10,
			)
		);

		Options::add_field(
			array(
				'id'       => 'shipping_progress_bar_calculation',
				'name'     => esc_html__( 'Calculation', 'xts-theme' ),
				'type'     => 'buttons',
				'section'  => 'shipping_progress_bar',
				'options'  => array(
					'custom' => array(
						'name'  => esc_html__( 'Custom', 'xts-theme' ),
						'value' => 'custom',
					),
					'wc'     => array(
						'name'  => esc_html__( 'Based on WooCommerce zones', 'xts-theme' ),
						'value' => 'wc',
					),
				),
				'default'  => 'custom',
				'priority' => 20,
			)
		);

		Options::add_field(
			array(
				'id'          => 'shipping_progress_bar_amount',
				'name'        => esc_html__( 'Goal amount', 'xts-theme' ),
				'description' => esc_html__( 'Amount to reach 100% defined in your currency absolute value. For example: 300', 'xts-theme' ),
				'type'        => 'text_input',
				'section'     => 'shipping_progress_bar',
				'requires'    => array(
					array(
						'key'     => 'shipping_progress_bar_calculation',
						'compare' => 'equals',
						'value'   => 'custom',
					),
				),
				'default'     => '100',
				'priority'    => 30,
			)
		);

		Options::add_field(
			array(
				'id'       => 'shipping_progress_bar_include_coupon',
				'name'     => esc_html__( 'Coupon discount', 'xts-theme' ),
				'type'     => 'buttons',
				'section'  => 'shipping_progress_bar',
				'options'  => array(
					'include' => array(
						'name'  => esc_html__( 'Include', 'xts-theme' ),
						'value' => 'include',
					),
					'exclude' => array(
						'name'  => esc_html__( 'Exclude', 'xts-theme' ),
						'value' => 'exclude',
					),
				),
				'default'  => 'include',
				'priority' => 40,
			)
		);

		Options::add_field(
			array(
				'id'       => 'shipping_progress_bar_location_card_page',
				'name'     => esc_html__( 'Cart page', 'xts-theme' ),
				'type'     => 'switcher',
				'section'  => 'shipping_progress_bar',
				'group'    => esc_html__( 'Locations', 'xts-theme' ),
				'default'  => '1',
				'priority' => 50,
				'class'    => 'xts-col-6',
			)
		);

		Options::add_field(
			array(
				'id'       => 'shipping_progress_bar_location_mini_cart',
				'name'     => esc_html__( 'Mini cart', 'xts-theme' ),
				'type'     => 'switcher',
				'section'  => 'shipping_progress_bar',
				'group'    => esc_html__( 'Locations', 'xts-theme' ),
				'default'  => '1',
				'priority' => 60,
				'class'    => 'xts-col-6',
			)
		);

		Options::add_field(
			array(
				'id'       => 'shipping_progress_bar_location_checkout',
				'name'     => esc_html__( 'Checkout page', 'xts-theme' ),
				'type'     => 'switcher',
				'section'  => 'shipping_progress_bar',
				'group'    => esc_html__( 'Locations', 'xts-theme' ),
				'default'  => '0',
				'priority' => 70,
				'class'    => 'xts-col-6',
			)
		);

		Options::add_field(
			array(
				'id'       => 'shipping_progress_bar_location_single_product',
				'name'     => esc_html__( 'Single product', 'xts-theme' ),
				'type'     => 'switcher',
				'section'  => 'shipping_progress_bar',
				'group'    => esc_html__( 'Locations', 'xts-theme' ),
				'default'  => '0',
				'priority' => 80,
				'class'    => 'xts-col-6',
			)
		);

		Options::add_field(
			array(
				'id'          => 'shipping_progress_bar_message_initial',
				'name'        => esc_html__( 'Initial message', 'xts-theme' ),
				'description' => esc_html__( 'Message to show before reaching the goal. Use shortcode [remainder] to display the amount left to reach the minimum.', 'xts-theme' ),
				'type'        => 'textarea',
				'wysiwyg'     => true,
				'section'     => 'shipping_progress_bar',
				'group'       => esc_html__( 'Message', 'xts-theme' ),
				'default'     => 'Add [remainder] to cart and get free shipping!',
				'priority'    => 90,
			)
		);

		Options::add_field(
			array(
				'id'          => 'shipping_progress_bar_message_success',
				'name'        => esc_html__( 'Success message', 'xts-theme' ),
				'description' => esc_html__( 'Message to show after reaching 100%.', 'xts-theme' ),
				'type'        => 'textarea',
				'wysiwyg'     => true,
				'section'     => 'shipping_progress_bar',
				'group'       => esc_html__( 'Message', 'xts-theme' ),
				'default'     => 'Your order qualifies for free shipping!',
				'priority'    => 100,
			)
		);
	}

	/**
	 * Output shipping progress bar.
	 */
	public function output_shipping_progress_bar() {
		if ( ! xts_get_opt( 'shipping_progress_bar_enabled' ) ) {
			return;
		}

		if ( xts_get_opt( 'shipping_progress_bar_location_card_page' ) ) {
			add_action( 'woocommerce_before_cart_table', array( $this, 'render_shipping_progress_bar_with_wrapper' ) );
		}

		if ( xts_get_opt( 'shipping_progress_bar_location_single_product' ) ) {
			add_action( 'woocommerce_single_product_summary', array( $this, 'render_shipping_progress_bar_with_wrapper' ), 29 );
		}

		if ( xts_get_opt( 'shipping_progress_bar_location_checkout' ) ) {
			add_action( 'woocommerce_checkout_billing', array( $this, 'render_shipping_progress_bar_with_wrapper' ), 5 );
		}
	}

	/**
	 * Update fragments shipping progress bar.
	 *
	 * @return void
	 */
	public function output_shipping_progress_bar_in_mini_cart() {
		if ( ! xts_get_opt( 'shipping_progress_bar_enabled' ) ) {
			return;
		}

		if ( xts_get_opt( 'shipping_progress_bar_location_mini_cart' ) ) {
			add_action( 'woocommerce_widget_shopping_cart_before_buttons', array( $this, 'render_shipping_progress_bar' ) );
		}

		add_filter( 'woocommerce_add_to_cart_fragments', array( $this, 'get_shipping_progress_bar_fragments' ), 40 );
		add_filter( 'woocommerce_update_order_review_fragments', array( $this, 'get_shipping_progress_bar_checkout_fragments' ), 10 );
	}

	/**
	 * Get shipping progress bar content.
	 *
	 * @return void
	 */
	public function render_shipping_progress_bar_with_wrapper() {
		?>
		<div class="xts-shipping-progress-bar xts-style-bordered">
			<?php $this->render_shipping_progress_bar(); ?>
		</div>
		<?php
	}

	/**
	 * Add shipping progress bar fragment.
	 *
	 * @param array $array Fragments.
	 *
	 * @return array
	 */
	public function get_shipping_progress_bar_checkout_fragments( $array ) {
		ob_start();

		$this->render_shipping_progress_bar();

		$content = ob_get_clean();

		$array['div.xts-free-progress-bar'] = $content;

		return $array;
	}

	/**
	 * Add shipping progress bar fragment.
	 *
	 * @param array $array Fragments.
	 *
	 * @return array
	 */
	public function get_shipping_progress_bar_fragments( $array ) {
		ob_start();

		$this->render_shipping_progress_bar();

		$content = ob_get_clean();

		$array['div.xts-free-progress-bar'] = $content;

		return $array;
	}

	/**
	 * Render free shipping progress bar.
	 */
	public function render_shipping_progress_bar() {
		if ( ! xts_get_opt( 'shipping_progress_bar_enabled' ) ) {
			return;
		}

		if ( ! is_object( WC() ) || ! property_exists( WC(), 'cart' ) || ! is_object( WC()->cart ) || ! method_exists( WC()->cart, 'get_displayed_subtotal' ) ) {
			$total = 0;
		} else {
			$total = WC()->cart->get_displayed_subtotal();
		}

		$calculation     = xts_get_opt( 'shipping_progress_bar_calculation', 'custom' );
		$wrapper_classes = '';
		$percent         = 100;
		$limit           = 0;
		$free_shipping   = false;

		if ( 'wc' === $calculation ) {
			$packages = WC()->cart->get_shipping_packages();
			$package  = reset( $packages );
			$zone     = wc_get_shipping_zone( $package );

			foreach ( $zone->get_shipping_methods( true ) as $method ) {
				if ( 'free_shipping' === $method->id ) {
					$limit = $method->get_option( 'min_amount' );
				}
			}
		} elseif ( 'custom' === $calculation ) {
			$limit = xts_get_opt( 'shipping_progress_bar_amount' );
		}

		$limit = apply_filters( 'xts_shipping_progress_bar_amount', $limit );

		if ( $total && 'exclude' === xts_get_opt( 'shipping_progress_bar_include_coupon' ) && WC()->cart->get_coupons() ) {
			foreach ( WC()->cart->get_coupons() as $coupon ) {
				$total -= WC()->cart->get_coupon_discount_amount( $coupon->get_code(), WC()->cart->display_cart_ex_tax );

				if ( $coupon->get_free_shipping() ) {
					$free_shipping = true;
					break;
				}
			}
		}

		if ( $total < $limit && ! $free_shipping ) {
			$percent = floor( ( $total / $limit ) * 100 );
			$message = str_replace( '[remainder]', wc_price( $limit - $total ), xts_get_opt( 'shipping_progress_bar_message_initial' ) );
		} else {
			$message = xts_get_opt( 'shipping_progress_bar_message_success' );
		}

		if ( 0 === (int) $total || $percent < 0 ) {
			$wrapper_classes .= ' xts-progress-hide';
		}

		if ( ! $limit ) {
			return;
		}

		?>
		<div class="xts-progress-bar xts-free-progress-bar<?php echo esc_attr( $wrapper_classes ); ?>">
			<div class="xts-progress-info">
				<?php echo wp_kses( $message, 'post' ); ?>
			</div>
			<div class="xts-progress-line">
				<div class="xts-progress-track" style="width: <?php echo esc_attr( $percent ); ?>%"></div>
			</div>
		</div>
		<?php
	}
}
