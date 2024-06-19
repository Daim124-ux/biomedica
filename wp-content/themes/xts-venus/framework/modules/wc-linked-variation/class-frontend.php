<?php

namespace XTS\Modules\Linked_Variations;

use WP_Query;
use XTS\Singleton;

class Frontend extends Singleton {
	/**
	 * Data.
	 *
	 * @var array
	 */
	private $linked_data = array();

	/**
	 * Construct.
	 */
	public function init() {
		add_action( 'woocommerce_single_product_summary', array( $this, 'output' ), 28 );
	}

	/**
	 * Output.
	 */
	public function output() {
		if ( ! xts_get_opt( 'linked_variations', true ) ) {
			return;
		}

		global $product;

		$this->set_linked_data( $product->get_id() );

		if ( empty( $this->linked_data ) || ! $this->linked_data['attrs'] || 1 === count( $this->linked_data['attrs'] ) && empty( reset( $this->linked_data['attrs'] ) ) ) {
			return;
		}

		$current_attributes     = $this->get_product_attributes( $product->get_id() );
		$linked_variations_data = $this->get_linked_variations( $product->get_id() );

		?>
		<div class="cart variations_form-linked">
			<table class="variations">
				<tbody>
					<?php foreach ( $linked_variations_data as $attr_slug => $attr_data ) : ?>
						<?php $swatch_size = get_option( 'xts_' . $attr_slug . '_attribute_swatch_size' ); ?>
						<tr>
							<th class="label cell">
								<label>
									<?php echo esc_html( $current_attributes['taxonomy'][ $attr_slug ] ); ?>
								</label>
							</th>
							<td class="value cell with-swatches">
								<div class="xts-single-product-swatches xts-swatches">
									<?php foreach ( $attr_data['terms'] as $term_slug => $term_data ) : ?>
										<?php
										$term_meta = $term_data['attributes']['meta'][ $attr_slug ];
										$classes   = ' xts-swatch xts-enabled';
										$styles    = '';

										if ( $swatch_size ) {
											$classes .= ' xts-size-' . $swatch_size;
										}

										if ( $attr_slug === $this->linked_data['use_image'] && get_post_thumbnail_id( $term_data['id'] ) ) {
											xts_enqueue_js_library( 'tooltip' );
											xts_enqueue_js_script( 'tooltip' );

											$image_src = wp_get_attachment_image_src( get_post_thumbnail_id( $term_data['id'] ), 'woocommerce_thumbnail' );

											$classes .= ' xts-with-bg';
											$styles   = 'background-image: url(' . reset( $image_src ) . ');';
										} elseif ( ! empty( $term_meta['color']['idle'] ) ) {
											xts_enqueue_js_library( 'tooltip' );
											xts_enqueue_js_script( 'tooltip' );

											$classes .= ' xts-with-bg';
											$styles   = 'background-color:' . $term_meta['color']['idle'] . ';';
										} elseif ( ( ! empty( $term_meta['image'] ) && ! is_array( $term_meta['image'] ) ) || ( is_array( $term_meta['image'] ) && ! empty( $term_meta['image']['id'] ) ) ) {
											$classes .= ' xts-with-bg';

											if ( is_array( $term_meta['image'] ) ) {
												$image_src = wp_get_attachment_image_src( $term_meta['image']['id'], 'woocommerce_thumbnail' );
												$image_src = reset( $image_src );
											} else {
												$image_src = $term_meta['image'];
											}

											$styles = 'background-image: url(' . $image_src . ');';
										} else {
											$classes .= ' xts-with-text';
										}

										if ( 'outofstock' === $term_data['stock_status'] || ! $term_data['is_purchasable'] ) {
											$classes .= ' xts-disabled xts-linked';
										}

										if ( (string) $current_attributes['slugs'][ $attr_slug ] === (string) $term_slug ) {
											$classes .= ' xts-active';
										}

										?>
										<div class="xts-variation-swatch<?php echo esc_attr( $classes ); ?>" data-taxonomy="<?php echo esc_attr( $attr_slug ); ?>" data-term="<?php echo esc_attr( $term_slug ); ?>" data-xts-tooltip="<?php echo esc_html( $term_data['attributes']['labels'][ $attr_slug ] ); ?>" style="<?php echo esc_attr( $styles ); ?>">
											<span>
												<?php echo esc_html( $term_data['attributes']['labels'][ $attr_slug ] ); ?>
											</span>
											<a href="<?php echo esc_url( $term_data['permalink'] ); ?>" class="xts-fill"></a>
										</div>
									<?php endforeach; ?>
								</div>
							</td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		</div>
		<?php
	}

	/**
	 * Get linked variations data.
	 *
	 * @param int $product_id Product id.
	 *
	 * @return array
	 */
	public function get_linked_variations( $product_id ) {
		$attributes = $this->get_product_attributes( $product_id );
		$output     = array();

		foreach ( $attributes['slugs'] as $taxonomy => $attribute ) {
			$terms = get_terms(
				array(
					'taxonomy'   => $taxonomy,
					'hide_empty' => true,
				)
			);

			foreach ( $terms as $term ) {
				$data = $this->get_linked_variation_data_for_attribute( $product_id, $taxonomy, $term->slug );

				if ( ! $data ) {
					continue;
				}

				$output[ $taxonomy ]['terms'][ $term->slug ] = $data;
				$output[ $taxonomy ]['label'][ $term->slug ] = $term->name;
			}
		}

		return $output;
	}

	/**
	 * Set data.
	 *
	 * @param int $product_id Product id.
	 */
	private function set_linked_data( $product_id ) {
		$post = new WP_Query(
			array(
				'post_type'   => 'xts_linked_variation',
				'numberposts' => 1,
				'meta_query'  => [ // phpcs:ignore
					array(
						'key'     => '_xts_linked_products',
						'value'   => sprintf( '"%d"', $product_id ),
						'compare' => 'LIKE',
					),
				],
			)
		);

		if ( ! $post->posts ) {
			return;
		}

		$this->linked_data = array(
			'products'  => get_post_meta( $post->posts[0]->ID, '_xts_linked_products', true ),
			'attrs'     => get_post_meta( $post->posts[0]->ID, '_xts_linked_attrs', true ),
			'use_image' => get_post_meta( $post->posts[0]->ID, '_xts_linked_use_product_image', true ),
		);
	}

	/**
	 * Get product attributes.
	 *
	 * @param int $product_id Product id.
	 *
	 * @return array
	 */
	private function get_product_attributes( $product_id ) {
		$attributes = array();

		foreach ( $this->linked_data['attrs'] as $attribute ) {
			$terms = get_the_terms( $product_id, $attribute );

			if ( ! $terms || is_wp_error( $terms ) ) {
				continue;
			}

			$first_term = array_pop( $terms );

			$attributes[ $product_id ]['slugs'][ $attribute ]    = $first_term->slug;
			$attributes[ $product_id ]['labels'][ $attribute ]   = $first_term->name;
			$attributes[ $product_id ]['taxonomy'][ $attribute ] = get_taxonomy( $attribute )->labels->singular_name;
			$attributes[ $product_id ]['meta'][ $attribute ]     = array(
				'color' => get_term_meta( $first_term->term_id, '_xts_attribute_color', true ),
				'image' => get_term_meta( $first_term->term_id, '_xts_attribute_image', true ),
			);
		}

		return $attributes[ $product_id ];
	}

	/**
	 * Get linked variation data for attribute.
	 *
	 * @param int    $product_id Product id.
	 * @param string $taxonomy Taxonomy.
	 * @param string $term_slug Term slug.
	 *
	 * @return array
	 */
	public function get_linked_variation_data_for_attribute( $product_id, $taxonomy, $term_slug ) {
		$current_attributes = $this->get_product_attributes( $product_id );
		$linked_variations  = $this->get_linked_variations_data( $product_id );

		$current_attributes['slugs'][ $taxonomy ] = $term_slug;

		$output = array();

		foreach ( $linked_variations as $linked_variation ) {
			if ( ! empty( $linked_variation['attributes']['slugs'] ) && ! array_diff_assoc( $current_attributes['slugs'], $linked_variation['attributes']['slugs'] ) ) {
				$output = $linked_variation;
			}
		}

		return $output;
	}

	/**
	 * Get product attributes.
	 *
	 * @param int $product_id Product id.
	 *
	 * @return array
	 */
	private function get_linked_variations_data( $product_id ) {
		$linked_products = array();

		foreach ( $this->linked_data['products'] as $linked_variation_id ) {
			$linked_variation = wc_get_product( $linked_variation_id );

			if ( ! $linked_variation || $linked_variation->get_status() !== 'publish' ) {
				continue;
			}

			$linked_products[ $product_id ][ $linked_variation_id ] = array(
				'id'             => $linked_variation_id,
				'permalink'      => $linked_variation->get_permalink(),
				'image'          => $linked_variation->get_image( 'shop_thumbnail' ),
				'title'          => $linked_variation->get_title(),
				'stock_status'   => $linked_variation->get_stock_status(),
				'is_purchasable' => $linked_variation->is_purchasable(),
				'attributes'     => $this->get_product_attributes( $linked_variation_id ),
			);
		}

		return $linked_products[ $product_id ];
	}
}

Frontend::get_instance();
