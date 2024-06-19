<?php
/**
 * Unit of measure.
 *
 * @package XTS
 */

namespace XTS\Modules;

use XTS\Framework\Module;

/**
 * Unit of measure.
 */
class WC_Unit_Of_Measure extends Module {
	/**
	 * Constructor.
	 */
	public function init() {
		add_action( 'woocommerce_product_options_inventory_product_data', array( $this, 'output_control' ), 30 );
		add_action( 'woocommerce_product_bulk_edit_end', array( $this, 'output_control_in_bulk_edit' ) );

		add_action( 'woocommerce_process_product_meta', array( $this, 'save_control_product_meta' ) );
		add_action( 'save_post_product', array( $this, 'save_control_in_bulk_edit' ) );

		add_filter( 'woocommerce_get_price_html', array( $this, 'output_unit_of_measure' ), 10, 2 );
		add_filter( 'woocommerce_cart_product_price', array( $this, 'output_unit_of_measure' ), 10, 2 );
	}

	/**
	 * Output control in WooCommerce meta box.
	 *
	 * @return void
	 */
	public function output_control() {
		echo '<div class="options_group">';
		woocommerce_wp_text_input(
			array(
				'id'          => 'xts_price_unit_of_measure',
				'label'       => esc_html__( 'Unit of measurement', 'xts-theme' ),
				'desc_tip'    => 'true',
				'description' => esc_html__( 'Enter your unit of measurement for this product here.', 'xts-theme' ),
				'type'        => 'text',
			)
		);
		echo '</div>';
	}

	/**
	 * Output control in bulk edit.
	 *
	 * @return void
	 */
	public function output_control_in_bulk_edit() {
		?>
		<div class="inline-edit-group dimensions">
			<label class="alignleft">
				<span class="title">
					<?php esc_html_e( 'Unit of measurement', 'xts-theme' ); ?>
				</span>
				<span class="input-text-wrap">
					<select class="change_dimensions change_to" name="change_unit_of_measure">
						<?php
						$options = array(
							''  => __( '— No change —', 'woocommerce' ),
							'1' => __( 'Change to:', 'woocommerce' ),
						);
						foreach ( $options as $key => $value ) {
							echo '<option value="' . esc_attr( $key ) . '">' . esc_html( $value ) . '</option>';
						}
						?>
					</select>
				</span>
			</label>
			<label class="change-input">
				<input type="text" name="xts_price_unit_of_measure" class="text stock xts_price_unit_of_measure" placeholder="<?php esc_attr_e( 'Unit of measurement', 'xts-theme' ); ?>" value="">
			</label>
		</div>
		<?php
	}

	/**
	 * Save control in WooCommerce meta box.
	 *
	 * @return void
	 */
	public function save_control_product_meta() {
		if ( ! isset( $_POST['xts_price_unit_of_measure'] ) ) { //phpcs:ignore
			return;
		}

		$this->save_option_for_product( get_the_ID(), wp_kses( $_POST['xts_price_unit_of_measure'], true ) ); //phpcs:ignore
	}

	/**
	 * Save controls in bulk edit.
	 *
	 * @return void
	 */
	public function save_control_in_bulk_edit() {
		if ( ! isset( $_GET['post'] ) || ! isset( $_GET['change_unit_of_measure'] ) || ! $_GET['change_unit_of_measure'] || ! isset( $_GET['xts_price_unit_of_measure'] ) ) { //phpcs:ignore
			return;
		}

		$posts_id = wc_clean( $_GET['post'] ); //phpcs:ignore
		$option   = wp_kses( $_GET['xts_price_unit_of_measure'], true ); //phpcs:ignore

		if ( $posts_id ) {
			foreach ( $posts_id as $id ) {
				$this->save_option_for_product( $id, $option );
			}
		}
	}

	/**
	 * Save option in Database.
	 *
	 * @param integer $product_id Product ID.
	 * @param string  $option Option.
	 *
	 * @return void
	 */
	public function save_option_for_product( $product_id, $option ) {
		update_post_meta( $product_id, 'xts_price_unit_of_measure', $option );
	}

	/**
	 * Output price content with unit of measurement.
	 *
	 * @param string     $price  Price content.
	 * @param WC_Product $wc_product Product object.
	 *
	 * @return string
	 */
	public function output_unit_of_measure( $price, $wc_product ) {
		$unit_of_measure = $this->get_unit_of_measure_db( $wc_product );

		if ( ! $price || ! $unit_of_measure ) {
			return $price;
		}

		$price = str_replace( $wc_product->get_price_suffix(), '', $price );

		return $price . '<span class="xts-price-unit">' . wp_kses_post( $unit_of_measure ) . '</span>' . $wc_product->get_price_suffix();
	}

	/**
	 * Get unit of measurement in database.
	 *
	 * @param WC_Product $wc_product Product object.
	 *
	 * @return mixed
	 */
	public function get_unit_of_measure_db( $wc_product ) {
		$id = $wc_product->get_parent_id();

		if ( ! $id ) {
			$id = $wc_product->get_id();
		}

		return get_post_meta( $id, 'xts_price_unit_of_measure', true );
	}
}