<?php
/**
 * Frequently bought together class.
 *
 * @package xts
 */

namespace XTS\Modules\Frequently_Bought_Together;

use WP_Query;
use XTS\Singleton;

/**
 * Frontend class.
 */
class Frontend extends Singleton {

	/**
	 * Frequently bought together products.
	 *
	 * @var array
	 */
	protected $wfbt_products = array();

	/**
	 * Frequently bought together main product id.
	 *
	 * @var string
	 */
	protected $main_product_id = '';

	/**
	 * Bundle ID.
	 *
	 * @var string
	 */
	protected $bundle_id = '';

	/**
	 * Subtotal bundle products price.
	 *
	 * @var array
	 */
	protected $subtotal_products_price = array();

	/**
	 * Init.
	 */
	public function init() {
		add_action( 'woocommerce_after_single_product_summary', array( $this, 'get_bought_together_products' ), 12 );

		add_action( 'wp_ajax_xts_update_frequently_bought_price', array( $this, 'update_frequently_bought_price' ) );
		add_action( 'wp_ajax_nopriv_xts_update_frequently_bought_price', array( $this, 'update_frequently_bought_price' ) );

		add_filter( 'xts_localized_string_array', array( $this, 'update_localized_string' ) );
	}

	/**
	 * Update localized settings
	 *
	 * @param array $settings Settings.
	 * @return array
	 */
	public function update_localized_string( $settings ) {
		$settings['frequently_bought'] = wp_create_nonce( 'xts-frequently-bought-together' );

		return $settings;
	}

	/**
	 * Update ajax frequently bought price.
	 *
	 * @return void
	 */
	public function update_frequently_bought_price() {
		check_ajax_referer( 'xts-frequently-bought-together', 'key' );

		if ( empty( $_POST['main_product'] ) || empty( $_POST['products_id'] ) || empty( $_POST['bundle_id'] ) ) {
			return;
		}

		$bundle_id    = sanitize_text_field( wp_unslash( $_POST['bundle_id'] ) );
		$main_product = sanitize_text_field( wp_unslash( $_POST['main_product'] ) );
		$products_id  = wc_clean( $_POST['products_id'] ); //phpcs:ignore
		$fbt_products = get_post_meta( $bundle_id, '_xts_fbt_products', true );
		$fragments    = array();

		$this->subtotal_products_price = array();

		if ( ! $fbt_products ) {
			return;
		}

		foreach ( $fbt_products as $fbt_product ) {
			$product_id = apply_filters( 'wpml_object_id', $fbt_product['id'], 'product', true );

			$this->wfbt_products[ $product_id ] = array_merge( $fbt_product, array( 'id' => $product_id ) );
		}

		$this->main_product_id = (int) $main_product;
		$this->bundle_id       = $bundle_id;

		if ( $products_id ) {
			foreach ( $products_id as $id => $variation_id ) {
				if ( ! isset( $this->wfbt_products[ $id ] ) && $id !== (int) $main_product && $variation_id !== (int) $main_product ) {
					continue;
				}

				if ( $variation_id ) {
					$variation_product = wc_get_product( $variation_id );

					$fragments[ 'div.xts-fbt-bundle-' . $this->bundle_id . ' .xts-product-' . $id . ' .price' ] = '<span class="price">' . $this->update_product_price( $variation_product->get_price_html(), $variation_product ) . '</span>';
				} else {
					$current_product = wc_get_product( $id );
					$this->update_product_price( $current_product->get_price_html(), $current_product );
				}
			}
		}

		$fbt_count = count( $this->subtotal_products_price );

		$fragments[ 'div.xts-fbt-bundle-' . $this->bundle_id . ' .xts-fbt-purchase .price' ]        = '<span class="price">' . $this->get_subtotal_bundle_price() . '</span>';
		$fragments[ 'div.xts-fbt-bundle-' . $this->bundle_id . ' .xts-fbt-purchase .xts-fbt-desc' ] = '<div class="xts-fbt-desc">' . sprintf( _n( 'For %s item', 'For %s items', $fbt_count, 'xts-theme' ), $fbt_count ) . '</div>';

		wp_send_json(
			array(
				'fragments' => $fragments,
			)
		);
	}

	/**
	 * Get bought together products content.
	 *
	 * @param array $element_settings Settings.
	 *
	 * @return void
	 */
	public function get_bought_together_products( $element_settings = array() ) {
		global $product;

		$settings = array(
			'title'                      => '',
			'carousel_items_view'        => array( 'size' => xts_get_opt( 'bought_together_column', 3 ) ),
			'carousel_items_view_tablet' => array( 'size' => xts_get_opt( 'bought_together_column_tablet' ) ),
			'carousel_items_view_mobile' => array( 'size' => xts_get_opt( 'bought_together_column_mobile' ) ),
			'dots'                       => 'no',
			'arrows'                     => 'yes',
			'form_width'                 => xts_get_opt( 'bought_together_form_width' ),
			'is_builder'                 => false,
		);

		if ( $element_settings ) {
			$settings = array_merge( $settings, $element_settings );
		}

		$main_product          = $product->get_id();
		$this->main_product_id = $main_product;
		$bundles_data          = array();

		$bundles_id = get_post_meta( $main_product, '_xts_fbt_bundles_id', true );

		if ( ! $bundles_id ) {
			return;
		}

		foreach ( $bundles_id as $bundle_id ) {
			if ( 'publish' !== get_post_status( $bundle_id ) ) {
				continue;
			}

			$bundle        = get_post( $bundle_id );
			$wfbt_products = get_post_meta( $bundle->ID, '_xts_fbt_products', true );

			if ( ! $wfbt_products ) {
				continue;
			}

			foreach ( $wfbt_products as $key => $wfbt_product ) {
				if ( ! empty( $wfbt_product['id'] ) ) {
					$wfbt_products[ $key ]['id'] = apply_filters( 'wpml_object_id', $wfbt_product['id'], 'product', true );
				}

				$current_product = wc_get_product( $wfbt_product['id'] );
				$product_id      = 'variation' === $current_product->get_type() ? $current_product->get_parent_id() : $current_product->get_id();
				$post_status     = get_post_status( $product_id );

				if ( 'publish' !== $post_status ) {
					unset( $wfbt_products[ $key ] );
				}
			}

			$bundles_data[ $bundle->ID ] = $wfbt_products;
		}

		if ( ! $bundles_data ) {
			return;
		}

		xts_enqueue_js_script( 'frequently-bought-together' );

		add_filter( 'woocommerce_get_price_html', array( $this, 'update_product_price' ), 10, 2 );
		add_filter( 'xts_product_label_output', array( $this, 'added_sale_label' ) );
		remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10 );

		xts_set_loop_prop( 'show_quick_shop', false );
		if ( ! $settings['is_builder'] ) {
			echo '<div class="xts-fbt-wrap">';
		}

		if ( ! $settings['is_builder'] || $settings['title'] ) {
			$this->get_heading( $settings['title'], $settings['is_builder'] );
		}

		foreach ( $bundles_data as $bundle_id => $wfbt_products ) {
			$this->bundle_id               = $bundle_id;
			$this->wfbt_products           = array();
			$this->subtotal_products_price = array();

			foreach ( $wfbt_products as $wfbt_product ) {
				if ( $this->main_product_id === (int) $wfbt_product['id'] ) {
					continue;
				}

				$current_product = wc_get_product( $wfbt_product['id'] );

				if ( 'variation' === $current_product->get_type() && $current_product->get_parent_id() && $this->main_product_id === $current_product->get_parent_id() ) {
					continue;
				}

				if ( '0' !== get_post_meta( $bundle_id, '_xts_allow_customize', true ) && '0' !== get_post_meta( $bundle_id, '_xts_fbt_hide_out_of_stock_product', true ) && ! $current_product->is_in_stock() ) {
					continue;
				}

				$this->wfbt_products[ $wfbt_product['id'] ] = $wfbt_product;
			}

			$this->get_form_content( $settings );
		}

		if ( ! $settings['is_builder'] ) {
			echo '</div>';
		}

		remove_filter( 'woocommerce_get_price_html', array( $this, 'update_product_price' ) );
		remove_filter( 'xts_product_label_output', array( $this, 'added_sale_label' ) );

		if ( xts_get_opt( 'catalog_mode' ) || ! is_user_logged_in() && xts_get_opt( 'login_to_see_price' ) ) {
			return;
		}

		add_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10 );

		xts_set_loop_prop( 'show_quick_shop', true );
	}

	/**
	 * Get heading content.
	 *
	 * @param string $title Title.
	 * @param bool   $is_builder Is builder.
	 *
	 * @return void
	 */
	protected function get_heading( $title = '', $is_builder = false ) {
		$class = '';

		if ( $is_builder ) {
			$class .= ' element-title';
		} else {
			$class .= ' predefined-title';
		}

		?>
		<h4 class="title<?php echo esc_attr( $class ); ?>">
			<?php if ( $title ) : ?>
				<?php echo esc_html( $title ); ?>
			<?php else : ?>
				<?php esc_html_e( 'Frequently bought together', 'xts-theme' ); ?>
			<?php endif; ?>
		</h4>
		<?php
	}

	/**
	 * Get form content.
	 *
	 * @param array $settings Settings.
	 *
	 * @return void
	 */
	public function get_form_content( $settings ) {
		global $product;

		$element_args = array(
			'query_post_type'       => array( 'product', 'product_variation' ),
			'product_source'        => 'list_of_products',
			'include'               => array_column( $this->wfbt_products, 'id' ),
			'design'                => xts_get_opt( 'product_loop_design' ),
			'view'                  => 'carousel',
			'orderby'               => 'post__in',
			'carousel_items'        => $settings['carousel_items_view'],
			'carousel_items_tablet' => $settings['carousel_items_view_tablet'],
			'carousel_items_mobile' => $settings['carousel_items_view_mobile'],
			'carousel_spacing'      => 30,
			'image_size'            => xts_get_opt( 'product_loop_image_size' ),
			'image_custom'          => xts_get_opt( 'product_loop_image_size_custom' ),
			'dots'                  => $settings['dots'],
			'arrows'                => $settings['arrows'],
		);

		array_unshift( $element_args['include'], $product->get_id() );

		?>
			<div class="xts-fbt xts-design-side xts-fbt-bundle-<?php echo esc_attr( $this->bundle_id ); ?>">
				<?php xts_products_template( $element_args ); //phpcs:ignore ?>

				<?php $this->get_products_purchase(); ?>
			</div>
		<?php
	}

	/**
	 * Get purchase content.
	 *
	 * @return void
	 */
	protected function get_products_purchase() {
		global $product;

		if ( ! $product ) {
			$product = wc_get_product( $this->main_product_id );
		}

		$fbt_count      = count( $this->subtotal_products_price );
		$fbt_products   = array_column( $this->wfbt_products, 'id' );
		$show_checkbox  = get_post_meta( $this->bundle_id, '_xts_allow_customize', true );
		$state_checkbox = get_post_meta( $this->bundle_id, '_xts_fbt_default_checkbox_state', true );
		$button_classes = '';
		$classes        = '';

		array_unshift( $fbt_products, $product->get_id() );

		if ( ! empty( $show_checkbox ) && 'uncheck' === $state_checkbox ) {
			$classes        .= ' xts-checkbox-uncheck';
			$button_classes .= 'xts-disabled';
			$fbt_count       = 1;
		}

		if ( ! empty( $show_checkbox ) ) {
			$classes .= ' xts-checkbox-on';
		}

		?>
		<form class="xts-fbt-form<?php echo esc_attr( $classes ); ?>" method="post">
			<input type="hidden" name="xts-fbt-bundle-id" value="<?php echo esc_attr( $this->bundle_id ); ?>">
			<input type="hidden" name="xts-fbt-main-product" value="<?php echo esc_attr( $product->get_id() ); ?>">

			<div class="xts-fbt-products">
				<?php foreach ( $fbt_products as $id ) : ?>
					<?php
					$current_product = wc_get_product( $id );
					$checkbox_attr   = '';
					$product_id      = $current_product->get_id();
					$variation       = '';

					if ( 'variable' === $current_product->get_type() && $current_product->get_children() ) {
						$variation = wc_get_product( $this->get_default_variation_product_id( $current_product ) );
					}

					if ( $product_id === $product->get_id() || ! $state_checkbox || 'check' === $state_checkbox ) {
						$checkbox_attr .= 'checked';
					}
					if ( $product_id === $product->get_id() ) {
						$checkbox_attr .= ' disabled';
					}
					?>
					<div class="xts-fbt-product xts-product-<?php echo esc_attr( $product_id ); ?>" data-id="<?php echo esc_attr( $product_id ); ?>">
						<div class="xts-fbt-product-heading" for="xts-fbt-product-<?php echo esc_attr( $product_id ); ?>">
							<?php if ( ! empty( $show_checkbox ) ) : ?>
								<input class="<?php echo esc_attr( $button_classes ); ?>" type="checkbox" id="xts-fbt-product-<?php echo esc_attr( $product_id ); ?>" data-id="<?php echo esc_attr( $product_id ); ?>" <?php echo esc_attr( $checkbox_attr ); ?>>
							<?php endif; ?>
							<label for="xts-fbt-product-<?php echo esc_attr( $product_id ); ?>">
								<span class="xts-entities-title title">
									<?php echo esc_html( $current_product->get_name() ); ?>
								</span>
							</label>
							<span class="price">
								<?php if ( $variation ) : ?>
									<?php echo wp_kses( $variation->get_price_html(), true ); ?>
								<?php else : ?>
									<?php echo wp_kses( $current_product->get_price_html(), true ); ?>
								<?php endif; ?>
							</span>
						</div>
						<?php if ( $variation ) : ?>
							<div class="xts-fbt-product-variation">
								<select>
									<?php foreach ( $current_product->get_children() as $variation_id ) : ?>
										<?php $variation_product = wc_get_product( $variation_id ); ?>
										<option value="<?php echo esc_attr( $variation_product->get_id() ); ?>"<?php echo esc_attr( $variation->get_id() === $variation_product->get_id() ? ' selected="selected"' : '' ); ?>>
											<?php echo esc_html( wc_get_formatted_variation( $variation_product, true, false, false ) ); ?>
										</option>
									<?php endforeach; ?>
								</select>
							</div>
						<?php endif; ?>
					</div>
				<?php endforeach; ?>
			</div>
			<div class="xts-fbt-purchase">
				<div class="price">
					<?php echo wp_kses( $this->get_subtotal_bundle_price(), true ); ?>
				</div>
				<div class="xts-fbt-desc">
					<?php
					echo wp_kses(
						sprintf( _n( 'For %s item', 'For %s items', $fbt_count, 'xts-theme' ), $fbt_count ),
						true
					);
					?>
				</div>
				<button class="xts-fbt-purchase-btn single_add_to_cart_button button" type="submit">
					<?php esc_html_e( 'Add to cart', 'xts-theme' ); ?>
				</button>
			</div>
			<div class="xts-loader-overlay xts-fill"></div>
		</form>
		<?php
	}

	/**
	 * Get subtotal products price in bundle.
	 *
	 * @return string
	 */
	private function get_subtotal_bundle_price() {
		global $product;

		if ( ! $product ) {
			$product = wc_get_product( $this->main_product_id );
		}

		$old_price = array_sum( array_column( $this->subtotal_products_price, 'old' ) );
		$new_price = array_sum( array_column( $this->subtotal_products_price, 'new' ) );

		if ( $old_price <= $new_price ) {
			return wc_price( $new_price ) . $this->get_product_price_suffix();
		}

		return wc_format_sale_price( $old_price, $new_price ) . $this->get_product_price_suffix();
	}

	/**
	 * Get products price suffix.
	 *
	 * @return mixed|null
	 */
	private function get_product_price_suffix() {
		global $product;

		if ( ! $product ) {
			$product = wc_get_product( $this->main_product_id );
		}
		$html              = '';
		$suffix            = get_option( 'woocommerce_price_display_suffix' );
		$sum_including_tax = 0;
		$sum_excluding_tax = 0;
		$products          = $this->wfbt_products;

		$products[ $this->main_product_id ] = array();

		if ( $suffix && wc_tax_enabled() ) {
			foreach ( $products as $product_id => $product_settings ) {
				$current_product = wc_get_product( $product_id );

				if ( 'taxable' !== $current_product->get_tax_status() ) {
					continue;
				}

				$discount  = $this->get_discount_product_bundle( $product_id );
				$old_price = (float) wc_get_price_to_display( $current_product, array( 'price' => $current_product->get_price() ) );

				$new_price          = $old_price - ( ( $old_price / 100 ) * $discount );
				$sum_including_tax += (float) wc_get_price_including_tax( $current_product, array( 'price' => $new_price ) );
				$sum_excluding_tax += (float) wc_get_price_excluding_tax( $current_product, array( 'price' => $new_price ) );
			}

			if ( $sum_including_tax || $sum_excluding_tax ) {
				$replacements = array(
					'{price_including_tax}' => wc_price( $sum_including_tax ),
					'{price_excluding_tax}' => wc_price( $sum_excluding_tax ),
				);

				$html = str_replace( array_keys( $replacements ), array_values( $replacements ), ' <small class="woocommerce-price-suffix">' . wp_kses_post( $suffix ) . '</small>' );
			}
		}

		return apply_filters( 'woocommerce_get_price_suffix', $html, $product, array_sum( array_column( $this->subtotal_products_price, 'new' ) ), 1 );
	}

	/**
	 * Update product price.
	 *
	 * @param string $price Product price HTML.
	 * @param object $product Product data.
	 *
	 * @return string
	 */
	public function update_product_price( $price, $product ) {
		$product_id = $product->get_ID();

		if ( 'variation' === $product->get_type() && ! isset( $this->wfbt_products[ $product_id ] ) ) {
			$product_parent = wc_get_product( $product->get_parent_id() );
			$product_id     = $product_parent->get_ID();
		}

		$discount = $this->get_discount_product_bundle( $product_id );

		$old_price = (float) $product->get_price();

		$old_price_with_tax = (float) wc_get_price_to_display( $product, array( 'price' => $old_price ) );

		$this->subtotal_products_price[ $product_id ]['old'] = $old_price_with_tax;

		if ( ! $discount || 100 < $discount ) {
			$this->subtotal_products_price[ $product_id ]['new'] = $old_price_with_tax;

			return $price;
		}

		$new_price = $old_price - ( ( $old_price / 100 ) * $discount );
		$new_price = wc_get_price_to_display( $product, array( 'price' => $new_price ) );

		$this->subtotal_products_price[ $product_id ]['new'] = $new_price;

		if ( 'variable' === $product->get_type() ) {
			$prices = $product->get_variation_prices( true );

			if ( empty( $prices['price'] ) ) {
				return $price;
			} else {
				$min_reg_price = (float) current( $prices['price'] );
				$max_reg_price = (float) end( $prices['price'] );

				$min_reg_price = $min_reg_price - ( ( $min_reg_price / 100 ) * $discount );
				$max_reg_price = $max_reg_price - ( ( $max_reg_price / 100 ) * $discount );

				if ( $min_reg_price !== $max_reg_price ) {
					$price = wc_format_price_range( $min_reg_price, $max_reg_price );
				} else {
					$price = wc_format_sale_price( wc_price( end( $prices['regular_price'] ) ), wc_price( $min_reg_price ) );
				}

				return $price . $product->get_price_suffix();
			}
		}

			return wc_format_sale_price( wc_get_price_to_display( $product, array( 'price' => $product->get_regular_price() ) ), $new_price ) . $product->get_price_suffix();
	}

	/**
	 * Added product sale label.
	 *
	 * @param array $content Labels.
	 *
	 * @return array
	 */
	public function added_sale_label( $content ) {
		if ( 'small' === xts_get_loop_prop( 'product_design' ) || 'small-bg' === xts_get_loop_prop( 'product_design' ) ) {
			return $content;
		}

		global $product;

		$product_id = $product->get_ID();

		if ( 'variation' === $product->get_type() && ! isset( $this->wfbt_products[ $product_id ] ) ) {
			$product    = wc_get_product( $product->get_parent_id() );
			$product_id = $product->get_ID();
		}

		$discount = (int) $this->get_discount_product_bundle( $product_id );

		if ( ! $discount || 100 < $discount ) {
			return $content;
		}

		if ( $product->is_on_sale() ) {
			$regular_price = (float) $product->get_regular_price();
			$sale_price    = (float) $product->get_sale_price();

			if ( 'variable' === $product->get_type() ) {
				$regular_price = (float) $product->get_variation_regular_price();
				$sale_price    = (float) $product->get_variation_sale_price();
			}

			$discount += round( ( ( $regular_price - $sale_price ) / $regular_price ) * 100 );
		}

		$label = '<span class="xts-onsale xts-product-label xts-fbt-sale-label">-' . $discount . '%</span>';

		array_unshift( $content, $label );

		return $content;
	}

	/**
	 * Get discount product price.
	 *
	 * @param integer $product_id Product ID.
	 *
	 * @return false|float
	 */
	private function get_discount_product_bundle( $product_id ) {
		if ( $this->main_product_id === $product_id ) {
			$discount = (float) get_post_meta( $this->bundle_id, '_xts_main_products_discount', true );
		} elseif ( isset( $this->wfbt_products[ $product_id ] ) ) {
			$discount = (float) $this->wfbt_products[ $product_id ]['discount'];
		} else {
			return false;
		}

		return $discount;
	}

	/**
	 * Get default variation product id.
	 *
	 * @param object $product Product data.
	 *
	 * @return false|mixed
	 */
	private function get_default_variation_product_id( $product ) {
		if ( $product->get_default_attributes() ) {
			$is_default_variation = false;

			foreach ( $product->get_available_variations() as $variation_values ) {
				foreach ( $variation_values['attributes'] as $key => $attribute_value ) {
					$attribute_name = str_replace( 'attribute_', '', $key );
					$default_value  = $product->get_variation_default_attribute( $attribute_name );

					if ( $default_value === $attribute_value ) {
						$is_default_variation = true;
					} else {
						$is_default_variation = false;
					}
				}

				if ( $is_default_variation ) {
					return $variation_values['variation_id'];
				}
			}
		}

		return current( $product->get_children() );
	}
}

Frontend::get_instance();
