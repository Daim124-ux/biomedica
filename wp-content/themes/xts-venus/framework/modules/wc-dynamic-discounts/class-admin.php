<?php
/**
 * Add Dynamic discounts settings on wp admin page.
 *
 * @package xts
 */

namespace XTS\Modules\Dynamic_Discounts;

use WP_Error;
use XTS\Singleton;

/**
 * Add Dynamic discounts settings on wp admin page.
 */
class Admin extends Singleton {
	/**
	 * Init.
	 */
	public function init() {

		$this->set_default_meta_boxes_fields();

		add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );

		add_action( 'new_to_publish', array( $this, 'save' ) );
		add_action( 'save_post', array( $this, 'save' ) );
		add_action( 'edit_post', array( $this, 'clear_transients' ) );
		add_action( 'deleted_post', array( $this, 'clear_transients' ) );

		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

		// Select2 values for Discount Condition options.
		add_action( 'wp_ajax_xts_discount_conditions_query', array( $this, 'conditions_query' ) );

		// Status switcher column in Dynamic Pricing & Discounts post type page.
		add_action( 'manage_edit-xts_woo_discounts_columns', array( $this, 'admin_columns_titles' ) );
		add_action( 'manage_xts_woo_discounts_posts_custom_column', array( $this, 'admin_columns_content' ), 10, 2 );
		add_action( 'wp_ajax_xts_woo_discounts_change_status', array( $this, 'change_status_action' ) );

		// Errors messages.
		add_filter( 'xts_admin_localized_string_array', array( $this, 'add_localized_settings' ) );
	}


	/**
	 * Set default list of meta box arguments for rendering template.
	 */
	public function set_default_meta_boxes_fields() {
		Manager::get_instance()->set_default_meta_boxes_fields(
			array(
				'xts_discount_priority' => '',
				'xts_rule_type'         => array(
					'bulk' => esc_html__( 'Bulk pricing', 'xts-theme' ),
				),
				'discount_quantities'   => array(
					'individual_variation' => esc_html__( 'Individual variation', 'xts-theme' ),
					'individual_product'   => esc_html__( 'Individual product', 'xts-theme' ),
				),
				'discount_rules'        => array(
					array(
						'xts_discount_rules_from'       => '',
						'xts_discount_rules_to'         => '',
						'xts_discount_type'             => array(
							'amount'     => esc_html__( 'Fixed discount', 'xts-theme' ),
							'percentage' => esc_html__( 'Percentage discount', 'xts-theme' ),
						),
						'xts_discount_amount_value'     => '',
						'xts_discount_percentage_value' => '',
					),
				),
				'discount_condition'    => array(
					array(
						'comparison'         => array(
							'include' => esc_html__( 'Include', 'xts-theme' ),
							'exclude' => esc_html__( 'Exclude', 'xts-theme' ),
						),
						'type'               => array(
							'all'                  => esc_html__( 'All products', 'xts-theme' ),
							'product'              => esc_html__( 'Single product id', 'xts-theme' ),
							'product_cat'          => esc_html__( 'Product category', 'xts-theme' ),
							'product_cat_children' => esc_html__( 'Child product categories', 'xts-theme' ),
							'product_tag'          => esc_html__( 'Product tag', 'xts-theme' ),
							'product_attr_term'    => esc_html__( 'Product attribute', 'xts-theme' ),
							'product_type'         => esc_html__( 'Product type', 'xts-theme' ),
						),
						'query'              => array(),
						'product-type-query' => array(
							'simple'   => esc_html__( 'Simple product', 'xts-theme' ),
							'variable' => esc_html__( 'Variable product', 'xts-theme' ),
						),
					),
				),
			)
		);
	}

	/**
	 * Add custom meta boxes.
	 *
	 * @return void
	 */
	public function add_meta_boxes() {
		xts_add_box(
			'xts_woo_discounts_meta_boxes',
			esc_html__( 'Settings', 'xts-theme' ),
			array( $this, 'render' ),
			'xts_woo_discounts',
			'normal',
			'high'
		);
	}

	/**
	 * Save post with custom meta boxes.
	 *
	 * @param int $post_id Post ID.
	 * @return string|void
	 */
	public function save( $post_id ) {
		if ( ! isset( $_POST['xts_woo_discounts_meta_boxes_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['xts_woo_discounts_meta_boxes_nonce'] ) ), 'save_xts_woo_discounts' ) ) {
			return 'nonce not verified';
		}

		if ( wp_is_post_autosave( $post_id ) ) {
			return 'autosave';
		}

		if ( wp_is_post_revision( $post_id ) ) {
			return 'revision';
		}

		if ( isset( $_POST['post_type'] ) && 'xts_woo_discounts' === $_POST['post_type'] ) {
			if ( ! current_user_can( 'edit_page', $post_id ) ) {
				return 'cannot edit page';
			}
		} elseif ( ! current_user_can( 'edit_post', $post_id ) ) {
			return 'cannot edit post';
		}

		$this->clear_transients();

		$meta_boxes_fields = Manager::get_instance()->get_meta_boxes_fields();

		if ( ! empty( $meta_boxes_fields ) ) {
			foreach ( array_keys( $meta_boxes_fields ) as $meta_box_id ) {
				$meta_box_data = isset( $_POST[ $meta_box_id ] ) ? wp_unslash( $_POST[ $meta_box_id ] ) : ''; // phpcs:ignore;

				if ( is_array( $meta_box_data ) ) {
					array_walk_recursive( $meta_box_data, 'sanitize_text_field' );
				} else {
					$meta_box_data = sanitize_text_field( $meta_box_data );

				}

				update_post_meta( $post_id, $meta_box_id, maybe_serialize( $meta_box_data ) );
			}
		}
	}

	/**
	 * Clear transients.
	 *
	 * @return void
	 */
	public function clear_transients() {
		delete_transient( Manager::get_instance()->xts_transient_discounts_ids );
	}

	/**
	 * Enqueue admin scripts.
	 *
	 * @return void
	 */
	public function enqueue_scripts() {
		if ( 'xts_woo_discounts' !== get_post_type() ) {
			return;
		}

		wp_enqueue_script( 'select2', XTS_ASSETS_URL . '/js-libs/select2.full.min.js', array(), XTS_VERSION, true );
		wp_enqueue_script( 'xts-dynamic-discounts-table', XTS_ASSETS_URL . '/js/dynamicDiscountsTable.js', array( 'jquery' ), XTS_VERSION, true );
	}

	/**
	 * Get data from db for render select2 options for Discount Condition options in admin page.
	 */
	public function conditions_query() {
		check_ajax_referer( 'xts-get-theme-settings-data-nonce', 'security' );

		$query_type = sanitize_text_field( wp_unslash( $_POST['query_type'] ) ); // phpcs:ignore
		$search     = isset( $_POST['search'] ) ? sanitize_text_field( wp_unslash( $_POST['search'] ) ) : false; // phpcs:ignore

		$items = array();

		switch ( $query_type ) {
			case 'product_cat':
			case 'product_cat_children':
			case 'product_tag':
			case 'product_attr_term':
				$taxonomy = array();

				if ( 'product_cat' === $query_type || 'product_cat_children' === $query_type ) {
					$taxonomy[] = 'product_cat';
				}
				if ( 'product_tag' === $query_type ) {
					$taxonomy[] = 'product_tag';
				}
				if ( 'product_attr_term' === $query_type ) {
					foreach ( wc_get_attribute_taxonomies() as $attribute ) {
						$taxonomy[] = 'pa_' . $attribute->attribute_name;
					}
				}

				$terms = get_terms(
					array(
						'hide_empty' => false,
						'fields'     => 'all',
						'taxonomy'   => $taxonomy,
						'search'     => $search,
					)
				);

				if ( count( $terms ) > 0 ) {
					foreach ( $terms as $term ) {
						$items[] = array(
							'id'   => $term->term_id,
							'text' => $term->name . ' (ID: ' . $term->term_id . ') (Tax: ' . $term->taxonomy . ')',
						);
					}
				}
				break;
			case 'product_type':
				$product_types = wc_get_product_types();

				unset( $product_types['grouped'], $product_types['external'] );

				foreach ( $product_types as $type => $title ) {
					$items[] = array(
						'id'   => $type,
						'text' => $title,
					);
				}
				break;
			case 'product':
				$posts = get_posts(
					array(
						's'              => $search,
						'post_type'      => 'product',
						'posts_per_page' => 100,
					)
				);

				if ( count( $posts ) > 0 ) {
					foreach ( $posts as $post ) {
						$items[] = array(
							'id'   => $post->ID,
							'text' => $post->post_title . ' (ID: ' . $post->ID . ')',
						);
					}
				}
				break;
		}

		wp_send_json(
			array(
				'results' => $items,
			)
		);
	}

	/**
	 * Columns header.
	 *
	 * @param array $posts_columns Columns.
	 *
	 * @return array
	 */
	public function admin_columns_titles( $posts_columns ) {
		$offset = 2;
		return array_slice( $posts_columns, 0, $offset, true ) + array(
			'xts_discounts_status'  => esc_html__( 'Active', 'xts-theme' ),
			'xts_discount_priority' => esc_html__( 'Priority', 'xts-theme' ),
		) + array_slice( $posts_columns, $offset, null, true );
	}

	/**
	 * Columns content.
	 *
	 * @param string $column_name Column name.
	 * @param int    $post_id     Post id.
	 */
	public function admin_columns_content( $column_name, $post_id ) {
		if ( 'xts_discounts_status' === $column_name ) {
			$this->get_status_button_template( $post_id );
		} elseif ( 'xts_discount_priority' === $column_name ) {
			echo esc_html( get_post_meta( $post_id, 'xts_discount_priority', true ) );
		}
	}

	/**
	 * Change status action.
	 */
	public function change_status_action() {
		check_ajax_referer( 'xts-get-theme-settings-data-nonce', 'security' );

		$post_id = sanitize_text_field( wp_unslash( $_POST['id'] ) ); // phpcs:ignore
		$status  = sanitize_text_field( wp_unslash( $_POST['status'] ) ); // phpcs:ignore

		wp_update_post(
			array(
				'ID'          => $post_id,
				'post_status' => $status,
			)
		);

		wp_send_json(
			array(
				'new_html' => $this->get_status_button_template( $post_id ),
			)
		);
	}

	/**
	 * Get edit conditions template.
	 *
	 * @param int $post_id Post id.
	 *
	 * @return string|void
	 */
	private function get_status_button_template( $post_id ) {
		if ( is_ajax() ) {
			ob_start();
		}

		Manager::get_instance()->get_template(
			'status-button',
			array(
				'post_id' => $post_id,
				'status'  => get_post_status( $post_id ),
			)
		);

		if ( is_ajax() ) {
			return ob_get_clean();
		}
	}

	/**
	 * Render meta boxes fields.
	 *
	 * @codeCoverageIgnore
	 * @return void
	 */
	public function render() {
		$manager = Manager::get_instance();

		$manager->get_template(
			'admin',
			array(
				'args'         => $manager->get_default_meta_boxes_fields(),
				'current_args' => $manager->get_meta_boxes_fields(),
				'max_priority' => ! empty( $manager->get_all_meta_boxes_fields() ) ? max( array_column( $manager->get_all_meta_boxes_fields(), 'xts_discount_priority' ) ) : '0',
			)
		);
	}

	/**
	 * Get saved data from db for render selected select2 option for Discount Condition options in admin page.
	 *
	 * @param string|int $id Search for this term value.
	 * @param string     $query_type Query type.
	 *
	 * @return array
	 */
	public function get_saved_conditions_query( $id, $query_type ) {
		$item = array();

		switch ( $query_type ) {
			case 'product_cat':
			case 'product_cat_children':
			case 'product_tag':
			case 'product_attr_term':
				$taxonomy = '';

				if ( 'product_cat' === $query_type || 'product_cat_children' === $query_type ) {
					$taxonomy = 'product_cat';
				}

				if ( 'product_tag' === $query_type ) {
					$taxonomy = 'product_tag';
				}

				if ( 'product_attr_term' === $query_type ) {
					foreach ( wc_get_attribute_taxonomies() as $attribute ) {
						$term = get_term_by(
							'id',
							$id,
							'pa_' . $attribute->attribute_name,
						);

						if ( ! $term || $term instanceof WP_Error ) {
							continue;
						} else {
							break;
						}
					}
				} else {
					$term = get_term_by(
						'id',
						$id,
						$taxonomy,
					);
				}

				if ( ! isset( $term ) ) {
					break;
				}

				$item['id']   = $term->term_id;
				$item['text'] = $term->name . ' (ID: ' . $term->term_id . ') (Tax: ' . $term->taxonomy . ')';
				break;
			case 'product_type':
				foreach ( wc_get_product_types() as $type => $title ) {
					if ( $type === $id ) {
						$item['id']   = $type;
						$item['text'] = $title;
						break;
					}
				}
				break;
			case 'product':
				$post = get_post( $id );

				$item['id']   = $post->ID;
				$item['text'] = $post->post_title . ' (ID: ' . $post->ID . ')';
				break;
		}
		return $item;
	}

	/**
	 * Add localized settings.
	 *
	 * @param array $localize_data Data to localize.
	 * @return array
	 */
	public function add_localized_settings( $localize_data ) {
		return array_merge(
			$localize_data,
			array(
				'ddiscounts_quantity_range_start'  => esc_html__( 'Quantity range must start with a higher value than previous quantity range.', 'xts-theme' ),
				'ddiscounts_closing_quantity'      => esc_html__( 'Closing quantity must not be lower than opening quantity.', 'xts-theme' ),
				'ddiscounts_no_quantity_range'     => esc_html__( 'At least one quantity range is required for this pricing rule.', 'xts-theme' ),
				'ddiscounts_no_discount_condition' => esc_html__( 'At least one discount condition is required for this pricing rule.', 'xts-theme' ),
				'ddiscounts_dismiss_text'          => esc_html__( 'Dismiss this notice.', 'xts-theme' ),
				'ddiscounts_max_value'             => esc_html__( 'Discount cannot exceed 100%.', 'xts-theme' ),
			)
		);
	}
}

Admin::get_instance();
