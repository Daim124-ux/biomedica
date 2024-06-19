<?php
/**
 * Linked Variation.
 *
 * @package XTS
 */

namespace XTS\Modules;

use XTS\Framework\Module;
use XTS\Framework\Options;
use XTS\Options\Metaboxes;

/**
 * Linked Variation.
 */
class WC_Linked_Variation extends Module {
	/**
	 * Constructor.
	 */
	public function init() {
		$this->enqueue_files();

		add_action( 'init', array( $this, 'add_options' ) );
		add_action( 'init', array( $this, 'add_metaboxes' ) );

		add_filter( 'manage_edit-xts_linked_variation_columns', array( $this, 'edit_columns' ) );
		add_action( 'manage_xts_linked_variation_posts_custom_column', array( $this, 'manage_columns' ), 10, 2 );
	}

	/**
	 * Enqueue file.
	 */
	public function enqueue_files() {
		require_once XTS_FRAMEWORK_ABSPATH . '/modules/wc-linked-variation/class-frontend.php';
	}

	/**
	 *  Custom post type.
	 *
	 * @return array
	 */
	public function get_post_type_args() {
		return array(
			'label'              => esc_html__( 'Linked Variations', 'xts-theme' ),
			'labels'             => array(
				'name'          => esc_html__( 'Linked Variations', 'xts-theme' ),
				'singular_name' => esc_html__( 'Linked Variations', 'xts-theme' ),
				'menu_name'     => esc_html__( 'Linked Variations', 'xts-theme' ),
			),
			'supports'           => array( 'title' ),
			'hierarchical'       => false,
			'public'             => true,
			'show_in_menu'       => 'edit.php?post_type=product',
			'publicly_queryable' => false,
			'show_in_rest'       => true,
		);
	}

	/**
	 * Add options.
	 */
	public function add_options() {
		Options::add_field(
			array(
				'id'          => 'linked_variations',
				'name'        => esc_html__( 'Enable linked variations', 'xts-theme' ),
				'description' => wp_kses( __( 'This feature allows you to create a new kind of variable product based on simple products. You can create linked variations bundles via Dashboard -> Products -> Linked variations. Read more information in our <a href="https://xtemos.com/docs-topic/linked-variations/" target="_blank">documentation</a>.', 'xts-theme' ), true ),
				'group'       => esc_html__( 'Linked variations', 'xts-theme' ),
				'type'        => 'switcher',
				'section'     => 'single_product_elements_section',
				'default'     => '1',
				'on-text'     => esc_html__( 'Yes', 'xts-theme' ),
				'off-text'    => esc_html__( 'No', 'xts-theme' ),
				'priority'    => 85,
			)
		);
	}

	/**
	 * Add metaboxes.
	 */
	public function add_metaboxes() {
		$metabox = Metaboxes::add_metabox(
			array(
				'id'         => 'xts_linked_products_metaboxes',
				'title'      => esc_html__( 'Settings', 'xts-theme' ),
				'post_types' => array( 'xts_linked_variation' ),
			)
		);

		$metabox->add_section(
			array(
				'id'       => 'general',
				'name'     => esc_html__( 'General', 'xts-theme' ),
				'icon'     => 'xts-i-footer',
				'priority' => 10,
			)
		);

		$metabox->add_field(
			array(
				'id'           => 'linked_products',
				'type'         => 'select',
				'section'      => 'general',
				'name'         => esc_html__( 'Products', 'xts-theme' ),
				'description'  => esc_html__( 'Select products that will be a part of the bundle as variations.', 'xts-theme' ),
				'select2'      => true,
				'multiple'     => true,
				'empty_option' => true,
				'autocomplete' => array(
					'type'   => 'post',
					'value'  => 'product',
					'search' => 'xts_get_posts_by_query_autocomplete',
					'render' => 'xts_get_posts_by_ids_autocomplete',
				),
				'priority'     => 10,
			)
		);

		$metabox->add_field(
			array(
				'id'          => 'linked_attrs',
				'type'        => 'select',
				'section'     => 'general',
				'name'        => esc_html__( 'Attributes', 'xts-theme' ),
				'description' => esc_html__( 'These attributes will be used to connect selected products with each other.', 'xts-theme' ),
				'select2'     => true,
				'multiple'    => true,
				'options'     => $this->get_product_attributes_array(),
				'priority'    => 20,
			)
		);

		$metabox->add_field(
			array(
				'id'           => 'linked_use_product_image',
				'type'         => 'select',
				'section'      => 'general',
				'name'         => esc_html__( 'Attribute for the product image', 'xts-theme' ),
				'description'  => esc_html__( 'Select an attribute that will be shown as product images.', 'xts-theme' ),
				'select2'      => true,
				'empty_option' => true,
				'options'      => $this->get_product_attributes_array(),
				'priority'     => 30,
			)
		);
	}

	/**
	 * Get attribute taxonomies
	 *
	 * @since 1.0.0
	 */
	public function get_product_attributes_array() {
		$attributes = array();

		if ( ! xts_is_woocommerce_installed() ) {
			return $attributes;
		}

		foreach ( wc_get_attribute_taxonomies() as $attribute ) {
			$attributes[ 'pa_' . $attribute->attribute_name ] = array(
				'name'  => $attribute->attribute_label,
				'value' => 'pa_' . $attribute->attribute_name,
			);
		}

		return $attributes;
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
			'cb'       => '<input type="checkbox" />',
			'title'    => esc_html__( 'Title', 'xts-theme' ),
			'products' => esc_html__( 'Products', 'xts-theme' ),
			'date'     => esc_html__( 'Date', 'xts-theme' ),
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
		if ( 'products' === $column ) {
			$products    = array();
			$products_id = get_post_meta( $post_id, '_xts_linked_products', true );

			if ( empty( $products_id ) ) {
				return;
			}

			foreach ( $products_id as $product_id ) {
				$products[] = '<a href="' . get_permalink( $product_id ) . '">' . get_the_title( $product_id ) . '</a>';
			}

			echo wp_kses( implode( ' | ', $products ), true );
		}
	}
}
