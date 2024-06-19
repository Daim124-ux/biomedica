<?php
/**
 * The show single variations class.
 *
 * @package xts
 */

namespace XTS\Modules;

use XTS\Framework\Module;
use XTS\Framework\Options;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

/**
 * The show single variations class.
 *
 * @since 1.6.0
 */
class Wc_Show_Single_Variations extends Module {
	/**
	 * Register hooks.
	 */
	public function init() {
		add_action( 'init', array( $this, 'add_options' ), 10 );
		add_action( 'init', array( $this, 'hooks' ), 10 );
	}

	/**
	 * Init hooks.
	 */
	public function hooks() {
		if ( ! xts_get_opt( 'show_single_variation' ) ) {
			return;
		}
		add_action( 'woocommerce_variation_options', array( $this, 'add_exclude_variation_option' ), 1, 3 );
		add_action( 'woocommerce_variation_options', array( $this, 'get_option' ), 15, 3 );
		add_filter( 'xts_product_grid_attributes', array( $this, 'xts_filter_product_attributes' ), 10, 2 );
		add_action( 'init', array( $this, 'include_files' ), 100 );
	}

	/**
	 * Include files.
	 */
	public function include_files() {
		$files = array(
			'class-save',
			'class-query',
		);

		foreach ( $files as $file ) {
			require_once XTS_FRAMEWORK_ABSPATH . '/modules/wc-show-single-variations/' . $file . '.php';
		}
	}

	/**
	 * Add option.
	 *
	 * @return void
	 */
	public function add_options() {
		Options::add_field(
			array(
				'id'          => 'show_single_variation',
				'name'        => esc_html__( 'Show single variation', 'xts-theme' ),
				'description' => wp_kses( __( 'Enable it to show each variation as a separate product on the shop page. You need to resave variable products to make this option work. You can do this separately or using the bulk edit function. Read more information in our <a href="https://xtemos.com/docs-topic/show-single-variation/" target="_blank">documentation</a>.', 'xts-theme' ), true ),
				'group'       => esc_html__( 'Variation as product', 'xts-theme' ),
				'type'        => 'switcher',
				'section'     => 'general_shop_section',
				'default'     => false,
				'on-text'     => esc_html__( 'Yes', 'xts-theme' ),
				'off-text'    => esc_html__( 'No', 'xts-theme' ),
				'priority'    => 180,
			)
		);
		Options::add_field(
			array(
				'id'          => 'hide_variation_parent',
				'name'        => esc_html__( 'Hide variations parent', 'xts-theme' ),
				'description' => esc_html__( 'You can show only variations on the shop page excluding the main parent product.', 'xts-theme' ),
				'group'       => esc_html__( 'Variation as product', 'xts-theme' ),
				'type'        => 'switcher',
				'section'     => 'general_shop_section',
				'default'     => false,
				'on-text'     => esc_html__( 'Yes', 'xts-theme' ),
				'off-text'    => esc_html__( 'No', 'xts-theme' ),
				'priority'    => 190,
				'requires'    => array(
					array(
						'key'     => 'show_single_variation',
						'compare' => 'equals',
						'value'   => '1',
					),
				),
			)
		);
	}

	/**
	 * Output control in product variation.
	 *
	 * @param integer $loop Numbers variations.
	 * @param array   $variation_data Variation data.
	 * @param object  $variation Variation product object.
	 *
	 * @return void
	 */
	public function get_option( $loop, $variation_data, $variation ) {

		$variation_title = get_post_meta( $variation->ID, 'variation_title', true );

		?>
		<div class="form-field variation form-row variation">
			<?php
				woocommerce_wp_text_input(
					array(
						'id'    => 'variation_title[' . esc_attr( $loop ) . ']',
						'label' => esc_html__( 'Variation Title', 'xts-theme' ),
						'type'  => 'text',
						'value' => $variation_title,
					)
				);
			?>
		</div>
		<?php
	}

	/**
	 * Output control in product variation.
	 *
	 * @param integer $loop Numbers variations.
	 * @param array   $variation_data Variation data.
	 * @param object  $variation Variation product object.
	 *
	 * @return void
	 */
	public function add_exclude_variation_option( $loop, $variation_data, $variation ) {
		$enable = get_post_meta( $variation->ID, '_xts_show_variation', true );

		$enable = ( 'on' === $enable || '' === $enable );

		?>
		<label>
			<input type="checkbox" class="checkbox show_variation_product" name="xts_show_variation[<?php echo esc_attr( $loop ); ?>]" <?php checked( $enable ); ?> />
			<?php esc_html_e( 'Show variation product', 'xts-theme' ); ?>
		</label>
		<?php
	}

	/**
	 * Filters the product attributes
	 *
	 * @param  array  $attributes Attributes to be filtered.
	 * @param  object $product Product object.
	 * @return array
	 */
	public function xts_filter_product_attributes( $attributes, $product ) {
		if ( $product->is_type( 'variation' ) ) {
			$parent_id          = $product->get_parent_id();
			$parent             = wc_get_product( $parent_id );
			$handled_attributes = array();

			foreach ( $parent->get_attributes() as $key => $attribute ) {
				$handled_attributes[ $key ] = $attribute;
			}

			$varitation_attributes = array();

			foreach ( $product->get_variation_attributes() as $key => $attribute ) {
				$varitation_attributes[ $key ] = $attribute;
			}

			return array_merge( $varitation_attributes, $handled_attributes );
		}

		return $attributes;
	}
}
