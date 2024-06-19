<?php
/**
 * Quick buy.
 *
 * @package XTS
 */

namespace XTS\Modules;

use XTS\Framework\Module;
use XTS\Framework\Options;

/**
 * Quick buy.
 */
class WC_Quick_Buy extends Module {
	/**
	 * Constructor.
	 */
	public function init() {
		$this->include_files();

		add_action( 'init', array( $this, 'add_options' ), 10 );

		add_action( 'woocommerce_after_add_to_cart_button', array( $this, 'output_quick_buy_button' ), 1 );
	}

	/**
	 * Include files.
	 */
	public function include_files() {
		require_once XTS_FRAMEWORK_ABSPATH . '/modules/wc-quick-buy/class-redirect.php';
	}

	/**
	 * Add options in theme settings.
	 */
	public function add_options() {
		Options::add_section(
			array(
				'id'       => 'single_product_buy_now',
				'parent'   => 'single_product_section',
				'name'     => esc_html__( 'Buy now', 'xts-theme' ),
				'priority' => 35,
				'icon'     => 'xf-single-product',
			)
		);

		Options::add_field(
			array(
				'id'          => 'buy_now_enabled',
				'name'        => esc_html__( 'Buy now button', 'xts-theme' ),
				'description' => esc_html__( 'Add an extra button next to the “Add to cart” that will add the product to the cart and redirect it to the cart or checkout.', 'xts-theme' ),
				'type'        => 'switcher',
				'section'     => 'single_product_buy_now',
				'default'     => false,
				'priority'    => 10,
			)
		);

		Options::add_field(
			array(
				'id'       => 'buy_now_redirect',
				'name'     => esc_html__( 'Redirect location', 'xts-theme' ),
				'type'     => 'select',
				'section'  => 'single_product_buy_now',
				'default'  => 'checkout',
				'options'  => array(
					'checkout' => array(
						'name'  => esc_html__( 'Checkout page', 'xts-theme' ),
						'value' => 'checkout',
					),
					'cart'     => array(
						'name'  => esc_html__( 'Cart page', 'xts-theme' ),
						'value' => 'cart',
					),
				),
				'requires' => array(
					array(
						'key'     => 'quick_buy_enabled',
						'compare' => 'equals',
						'value'   => true,
					),
				),
				'priority' => 20,
			)
		);
	}

	/**
	 * Output quick buy button.
	 */
	public function output_quick_buy_button() {
		if ( ! is_singular( 'product' ) && ! xts_get_loop_prop( 'is_quick_view' ) || ! xts_get_opt( 'buy_now_enabled' ) ) {
			return;
		}
		?>
		<button id="xts-add-to-cart" type="submit" name="xts-add-to-cart" value="<?php echo get_the_ID(); ?>" class="xts-buy-now-btn button alt">
			<?php esc_html_e( 'Buy now', 'xts-theme' ); ?>
		</button>
		<?php
	}
}
