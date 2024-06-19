<?php
/**
 * Grid gallery.
 *
 * @package XTS
 */

namespace XTS\Modules;

use XTS\Framework\Module;
use XTS\Framework\Options;

/**
 * Grid gallery.
 */
class WC_Grid_Gallery extends Module {
	/**
	 * Constructor.
	 */
	public function init() {
		add_action( 'init', array( $this, 'add_options' ) );

		add_action( 'xts_get_default_setup_loop_args', array( $this, 'get_default_loop_setup' ) );
		add_action( 'woocommerce_before_shop_loop_item_title', array( $this, 'output_grid_gallery' ), 5 );
	}

	/**
	 * Add options.
	 */
	public function add_options() {
		Options::add_field(
			array(
				'id'          => 'enabled_grid_gallery',
				'name'        => esc_html__( 'Product gallery', 'xts-theme' ),
				'description' => esc_html__( 'Add the ability to view the product gallery on the products loop.', 'xts-theme' ),
				'type'        => 'switcher',
				'section'     => 'product_archive_product_options_section',
				'default'     => false,
				'priority'    => 30,
			)
		);

		Options::add_field(
			array(
				'id'                  => 'grid_gallery',
				'name'                => esc_html__( 'Product gallery controls on desktop', 'xts-theme' ),
				'type'                => 'buttons',
				'section'             => 'product_archive_product_options_section',
				'responsive'          => true,
				'responsive_variants' => array( 'desktop', 'mobile' ),
				'desktop_only'        => true,
				'options'             => array(
					'off'    => array(
						'name'  => esc_html__( 'Disable', 'xts-theme' ),
						'value' => 'off',
					),
					'arrows' => array(
						'name'  => esc_html__( 'Arrows', 'xts-theme' ),
						'value' => 'arrows',
					),
				),
				'default'             => 'arrows',
				'requires'            => array(
					array(
						'key'     => 'enabled_grid_gallery',
						'compare' => 'equals',
						'value'   => true,
					),
				),
				'priority'            => 35,
			)
		);

		Options::add_field(
			array(
				'id'                  => 'grid_gallery_mobile',
				'name'                => esc_html__( 'Product gallery controls on mobile device', 'xts-theme' ),
				'type'                => 'buttons',
				'section'             => 'product_archive_product_options_section',
				'options'             => array(
					'off'    => array(
						'name'  => esc_html__( 'Disable', 'xts-theme' ),
						'value' => 'off',
					),
					'arrows' => array(
						'name'  => esc_html__( 'Arrows', 'xts-theme' ),
						'value' => 'arrows',
					),
				),
				'default'             => 'off',
				'responsive'          => true,
				'responsive_variants' => array( 'desktop', 'mobile' ),
				'mobile_only'         => true,
				'requires'            => array(
					array(
						'key'     => 'enabled_grid_gallery',
						'compare' => 'equals',
						'value'   => true,
					),
				),
				'priority'            => 36,
			)
		);
	}

	/**
	 * Update default setup loop.
	 *
	 * @param array $args Loop argument.
	 * @return array
	 */
	public function get_default_loop_setup( $args ) {
		$args['product_grid_gallery_enabled'] = xts_get_opt( 'enabled_grid_gallery' );
		$args['product_grid_gallery_desktop'] = xts_get_opt( 'grid_gallery', 'arrows' );
		$args['product_grid_gallery_mobile']  = xts_get_opt( 'grid_gallery_mobile', 'off' );

		return $args;
	}

	/**
	 * Output gallery content.
	 *
	 * @return void
	 */
	public function output_grid_gallery() {
		if ( ! xts_get_loop_prop( 'product_grid_gallery_enabled' ) || 'off' === xts_get_opt( 'product_grid_gallery_desktop', 'arrows' ) && 'off' === xts_get_opt( 'grid_gallery_mobile', 'off' ) ) {
			return;
		}

		$images_url = $this->get_all_products_thubnails_url();

		if ( ! $images_url || count( $images_url ) <= 1 ) {
			return;
		}

		xts_set_loop_prop( 'product_hover_image', false );

		$wrapper_classes  = ' xts-nav-' . xts_get_loop_prop( 'product_grid_gallery_desktop', 'arrows' );
		$wrapper_classes .= ' xts-nav-md-' . xts_get_loop_prop( 'product_grid_gallery_mobile', 'off' );

		xts_enqueue_js_script( 'product-image-gallery-in-loop' );

		?>
		<div class="xts-product-grid-slider-wrapp<?php echo esc_attr( $wrapper_classes ); ?>">
			<div class="xts-product-grid-slider">
				<?php foreach ( $images_url as $id => $image_data ) : ?>
					<?php
					$attributes  = ' data-image-id="' . esc_attr( $id ) . '"';
					$attributes .= ' data-image-src="' . esc_url( $image_data['src'] ) . '"';
					$extra_class = array_key_first( $images_url ) === $id ? ' xts-active' : '';

					if ( ! empty( $image_data['srcset'] ) ) {
						$attributes .= ' data-image-srcset="' . esc_attr( $image_data['srcset'] ) . '"';
						$attributes .= ' data-image-sizes="' . esc_attr( $image_data['sizes'] ) . '"';
					}
					?>

					<div class="xts-product-grid-slide<?php echo esc_attr( $extra_class ); ?>"<?php echo wp_kses( $attributes, true ); ?>></div>
				<?php endforeach; ?>
			</div>

			<?php if ( 'arrows' === xts_get_loop_prop( 'product_grid_gallery_desktop', 'arrows' ) || 'arrows' === xts_get_loop_prop( 'product_grid_gallery_mobile', 'off' ) ) : ?>
				<div class="xts-product-grid-slider-nav xts-fill">
					<div class="xts-prev"></div>
					<div class="xts-next"></div>
				</div>
			<?php endif; ?>
		</div>
		<?php
	}

	/**
	 * Get product thumbnails url.
	 *
	 * @return array
	 */
	protected function get_all_products_thubnails_url() {
		global $product;

		$gallery_image_ids = $product->get_gallery_image_ids();
		$images_url        = array();
		$image_size        = xts_get_loop_prop( 'product_image_size', 'woocommerce_thumbnail' );
		$custom_image_size = xts_get_loop_prop( 'product_image_custom' );
		$placeholder_image = get_option( 'woocommerce_placeholder_image', 0 );

		if ( $product->get_image_id() ) {
			array_unshift( $gallery_image_ids, $product->get_image_id() );
		} elseif ( ! empty( $placeholder_image ) ) {
			if ( is_numeric( $placeholder_image ) ) {
				array_unshift( $gallery_image_ids, $placeholder_image );
			} else {
				$images_url[] = array(
					'src' => $placeholder_image,
				);
			}
		}

		$max_number_product_thumbnails = apply_filters( 'xts_max_number_product_thumbnails', null );
		$gallery_image_ids             = array_slice( $gallery_image_ids, 0, is_null( $max_number_product_thumbnails ) ? null : intval( $max_number_product_thumbnails ) );

		foreach ( $gallery_image_ids as $attachment_id ) {
			$image_src = xts_get_image_url(
				$attachment_id,
				'image',
				array(
					'image_size'             => $image_size,
					'image_custom_dimension' => $custom_image_size,
				)
			);

			$images_url[ $attachment_id ] = array(
				'src' => $image_src,
			);

			if ( in_array( $image_size, array_keys( xts_get_all_image_sizes() ), true ) ) {
				$images_url[ $attachment_id ]['srcset'] = wp_get_attachment_image_srcset( $attachment_id, $image_size );
				$images_url[ $attachment_id ]['sizes']  = wp_get_attachment_image_sizes( $attachment_id, $image_size );
			}
		}

		return $images_url;
	}
}


