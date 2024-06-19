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
 * Controls class.
 */
class Controls extends Singleton {
	/**
	 * Init.
	 */
	public function init() {
		add_filter( 'woocommerce_product_data_tabs', array( $this, 'product_data_tabs' ) );
		add_action( 'woocommerce_product_data_panels', array( $this, 'product_data_panels' ) );

		add_filter( 'manage_edit-xts_woo_fbt_columns', array( $this, 'edit_columns' ) );
		add_action( 'manage_xts_woo_fbt_posts_custom_column', array( $this, 'manage_columns' ), 10, 2 );

		add_action( 'wp_ajax_xts_get_bundles_settings_content', array( $this, 'get_settings_content' ) );

		add_action( 'woocommerce_process_product_meta', array( $this, 'save_bundles' ) );

		add_action( 'restrict_manage_posts', array( $this, 'restrict_manage_posts' ), 10, 2 );
		add_action( 'parse_query', array( $this, 'request_query' ) );
	}

	/**
	 * Add custom tab in WC tabs.
	 *
	 * @param array $tabs WooCommerce tabs.
	 * @return array
	 */
	public function product_data_tabs( $tabs ) {
		$tabs['xts_bought_together'] = array(
			'label'    => esc_html__( 'Frequently Bought Together', 'xts-theme' ),
			'target'   => 'xts_bought_together',
			'priority' => 80,
		);

		return $tabs;
	}

	/**
	 * Add custom tab content in WC tabs.
	 *
	 * @return void
	 */
	public function product_data_panels() {
		$bundles_table = new Bundles_Table();
		$bundles_id    = array();

		if ( ! empty( $bundles_table->items ) ) {
			$bundles_id = array_keys( $bundles_table->items );
		}

		wp_enqueue_script( 'frequently-bought-together', XTS_ASSETS_URL . '/js/frequentlyBoughtTogether.js', array(), XTS_VERSION, true );

		?>
		<div id="xts_bought_together" class="widget-content panel woocommerce_options_panel xts-bought-together" style="display:none">
			<table class="wp-list-table widefat fixed striped table-view-list type">
				<thead>
				<tr>
					<?php $bundles_table->print_column_headers(); ?>
				</tr>
				</thead>
				<tbody id="the-list" data-wp-lists='list:$singular'>
					<?php $bundles_table->display_rows_or_placeholder(); ?>
				</tbody>
			</table>
			<div class="xts-bought-together-controls xts-active-section">
				<div class="options_group">
					<p class="form-field">
						<label><?php esc_html_e( 'Add bundles', 'xts-theme' ); ?></label>
						<select class="xts-select xts-select2 xts-autocomplete" name="xts_bundle" data-type="post" data-value="xts_woo_fbt" data-search="xts_get_posts_by_query_autocomplete">
							<option value=""><?php esc_html_e( 'Select', 'xts-theme' ); ?></option>
						</select>
						<input type="hidden" class="xts-product-bundles-id" name="xts_product_bundles_id" value="<?php echo esc_attr( implode( ',', $bundles_id ) ); ?>" data-product-id="<?php the_ID(); ?>" data-nonce="<?php echo esc_attr( wp_create_nonce( 'xts_product_bundles_settings' ) ); ?>">
					</p>
				</div>
				<a href="<?php echo esc_url( admin_url( 'edit.php?post_type=xts_woo_fbt' ) ); ?>">
					<?php esc_html_e( 'Open bundles manager', 'xts-theme' ); ?>
				</a>
			</div>
		</div>
		<?php
	}

	/**
	 * Added custom columns.
	 *
	 * @param array $columns Default columns.
	 *
	 * @return array
	 */
	public function edit_columns( $columns ) {
		return array(
			'cb'               => '<input type="checkbox" />',
			'title'            => esc_html__( 'Title', 'xts-theme' ),
			'primary_products' => esc_html__( 'Products containing this bundle', 'xts-theme' ),
			'bundle_products'  => esc_html__( 'Bundle includes', 'xts-theme' ),
			'date'             => esc_html__( 'Date', 'xts-theme' ),
		);
	}

	/**
	 * Added custom content for columns.
	 *
	 * @param string  $column Column.
	 * @param integer $post_id Post ID.
	 *
	 * @return void
	 */
	public function manage_columns( $column, $post_id ) {
		if ( 'primary_products' === $column ) {
			$query = new WP_Query(
				array(
					'post_type'  => 'product',
					'meta_query' => array(
						array(
							'key'     => '_xts_fbt_bundles_id',
							'value'   => sprintf( '"%d"', $post_id ),
							'compare' => 'LIKE',
						),
					),
				)
			);

			if ( empty( $query->posts ) ) {
				return;
			}

			foreach ( $query->posts as $post ) {
				if ( ! $post ) {
					continue;
				}

				$products[] = '<a href="' . get_permalink( $post->ID ) . '">' . $post->post_title . '</a>';
			}

			echo wp_kses( implode( ' | ', $products ), true );
		} elseif ( 'bundle_products' === $column ) {
			$primary_products = get_post_meta( $post_id, '_xts_fbt_products', true );
			$products         = array();

			if ( $primary_products ) {
				foreach ( $primary_products as $product ) {
					if ( ! empty( $product['id'] ) ) {
						$products[] = '<a href="' . get_permalink( $product['id'] ) . '">' . get_the_title( $product['id'] ) . '</a>';
					}
				}

				echo wp_kses( implode( ' | ', $products ), true );
			}
		}
	}

	/**
	 * Added bundles for product metabox.
	 *
	 * @return void
	 */
	public function get_settings_content() {
		check_ajax_referer( 'xts_product_bundles_settings', 'security' );

		if ( ! isset( $_POST['bundles_id'] ) || empty( $_POST['product_id'] ) ) {
			return;
		}

		$bundles_table = new Bundles_Table( sanitize_text_field( $_POST['product_id'] ) ); //phpcs:ignore
		$bundles_id    = explode( ',', wc_clean( $_POST['bundles_id'] ) ); //phpcs:ignore

		asort( $bundles_id );
		$bundles_table->items = array();

		if ( $bundles_id ) {
			foreach ( $bundles_id as $bundle_id ) {
				$bundle = get_post( $bundle_id );

				if ( ! $bundle ) {
					continue;
				}

				$products      = get_post_meta( $bundle->ID, '_xts_fbt_products', true );
				$products_data = array();

				if ( $products ) {
					foreach ( $products as $product ) {
						if ( ! empty( $product['id'] ) ) {
							$products_data[] = '<a href="' . get_permalink( $product['id'] ) . '">' . get_the_title( $product['id'] ) . '</a>';
						}
					}
				}

				$bundles_table->items[ $bundle->ID ] = array(
					'id'       => $bundle->ID,
					'title'    => $bundle->post_title,
					'status'   => get_post_status( $bundle->ID ),
					'products' => $products_data,
				);
			}
		}

		ob_start();

		$bundles_table->display_rows_or_placeholder();

		wp_send_json(
			array(
				'content' => ob_get_clean(),
			)
		);
	}

	/**
	 * Save bundle for main product.
	 *
	 * @param integer $post_id Post ID.
	 *
	 * @return void
	 */
	public function save_bundles( $post_id ) {
		if ( ! isset( $_REQUEST['xts_product_bundles_id'] ) ) { //phpcs:ignore
			return;
		}

		$bundles_id = explode( ',', sanitize_text_field( $_REQUEST['xts_product_bundles_id'] ) ); //phpcs:ignore

		update_post_meta( $post_id, '_xts_fbt_bundles_id', $bundles_id );
	}

	/**
	 * Added control select with products.
	 *
	 * @param string $post_type Post type.
	 * @param string $which Position.
	 *
	 * @return void
	 */
	public function restrict_manage_posts( $post_type, $which ) {
		if ( 'xts_woo_fbt' !== $post_type || 'top' !== $which || ! have_posts() ) {
			return;
		}

		?>
		<span class="xts-active-section">
			<select class="xts-select xts-select2 xts-autocomplete select2-hidden-accessible" name="xts_fbt_product" data-type="post" data-value="product" data-search="xts_get_posts_by_query_autocomplete" aria-label="xts_fbt_products" tabindex="-1" aria-hidden="true" data-placeholder="<?php esc_html_e( 'Filter by product bundles', 'xts-theme' ); ?>">
				<?php if ( ! empty( $_GET['xts_fbt_product'] ) ) : //phpcs:ignore ?>
					<?php $product_id = sanitize_text_field( $_GET['xts_fbt_product'] ); //phpcs:ignore ?>
					<option value="<?php echo esc_html( $product_id ); ?>" selected>
						<?php echo esc_html( get_the_title( $product_id ) ); ?>
					</option>
				<?php endif; ?>
			</select>
		</span>
		<?php
	}

	/**
	 * Request query.
	 *
	 * @param object $wp Request data.
	 *
	 * @return void
	 */
	public function request_query( $wp ) {
		global $pagenow;

		if ( 'edit.php' !== $pagenow || 'xts_woo_fbt' !== $wp->query_vars['post_type'] || empty( $_GET['filter_action'] ) || empty( $_GET['xts_fbt_product'] ) ) { //phpcs:ignore
			return;
		}

		$products_id = sanitize_text_field( $_GET['xts_fbt_product'] ); //phpcs:ignore
		$bundles_id  = get_post_meta( $products_id, '_xts_fbt_bundles_id', true );

		if ( ! $bundles_id ) {
			return;
		}

		$wp->query_vars['post__in'] = $bundles_id;
	}
}

Controls::get_instance();
