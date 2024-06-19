<?php
/**
 * Frequently bought together class.
 *
 * @package xts
 */

namespace XTS\Modules;

use XTS\Framework\Module;
use XTS\Framework\Options;
use XTS\Options\Metaboxes;

/**
 * Frequently bought together class.
 */
class WC_Frequently_Bought_Together extends Module {
	/**
	 * Init.
	 */
	public function init() {
		add_action( 'init', array( $this, 'add_options' ) );
		add_action( 'init', array( $this, 'include_files' ) );
		add_action( 'init', array( $this, 'add_metaboxes' ) );
	}

	/**
	 * Include files.
	 *
	 * @return void
	 */
	public function include_files() {
		if ( ! xts_get_opt( 'bought_together_enabled', 1 ) ) {
			return;
		}

		$files = array(
			'class-controls',
			'class-table',
			'class-frontend',
			'class-render',
		);

		foreach ( $files as $file ) {
			require_once XTS_FRAMEWORK_ABSPATH . '/modules/wc-frequently-bought-together/' . $file . '.php';
		}
	}

	/**
	 * Add options in theme settings.
	 */
	public function add_options() {
		Options::add_field(
			array(
				'id'          => 'bought_together_enabled',
				'name'        => esc_html__( 'Enable "Frequently bought together"', 'xts-theme' ),
				'description' => esc_html__( 'You can configure your bundles in Dashboard -> Products -> Frequently Bought Together.', 'xts-theme' ),
				'group'       => esc_html__( 'Frequently bought together', 'xts-theme' ),
				'type'        => 'switcher',
				'section'     => 'general_shop_section',
				'default'     => '1',
				'priority'    => 130,
			)
		);

		Options::add_field(
			array(
				'id'                  => 'bought_together_column',
				'name'                => esc_html__( 'Products columns on desktop', 'xts-theme' ),
				'group'               => esc_html__( 'Frequently bought together', 'xts-theme' ),
				'type'                => 'buttons',
				'section'             => 'general_shop_section',
				'responsive'          => true,
				'responsive_variants' => array( 'desktop', 'tablet', 'mobile' ),
				'desktop_only'        => true,
				'options'             => array(
					1 => array(
						'name'  => 1,
						'value' => 1,
					),
					2 => array(
						'name'  => 2,
						'value' => 2,
					),
					3 => array(
						'name'  => 3,
						'value' => 3,
					),
					4 => array(
						'name'  => 4,
						'value' => 4,
					),
					5 => array(
						'name'  => 5,
						'value' => 5,
					),
					6 => array(
						'name'  => 6,
						'value' => 6,
					),
				),
				'default'             => 3,
				'priority'            => 140,
			)
		);

		Options::add_field(
			array(
				'id'                  => 'bought_together_column_tablet',
				'name'                => esc_html__( 'Products columns on desktop', 'xts-theme' ),
				'group'               => esc_html__( 'Frequently bought together', 'xts-theme' ),
				'type'                => 'buttons',
				'section'             => 'general_shop_section',
				'responsive'          => true,
				'responsive_variants' => array( 'desktop', 'tablet', 'mobile' ),
				'tablet_only'         => true,
				'options'             => array(
					1 => array(
						'name'  => 1,
						'value' => 1,
					),
					2 => array(
						'name'  => 2,
						'value' => 2,
					),
					3 => array(
						'name'  => 3,
						'value' => 3,
					),
					4 => array(
						'name'  => 4,
						'value' => 4,
					),
					5 => array(
						'name'  => 5,
						'value' => 5,
					),
					6 => array(
						'name'  => 6,
						'value' => 6,
					),
				),
				'priority'            => 141,
			)
		);

		Options::add_field(
			array(
				'id'                  => 'bought_together_column_mobile',
				'name'                => esc_html__( 'Products columns on desktop', 'xts-theme' ),
				'group'               => esc_html__( 'Frequently bought together', 'xts-theme' ),
				'type'                => 'buttons',
				'section'             => 'general_shop_section',
				'responsive'          => true,
				'responsive_variants' => array( 'desktop', 'tablet', 'mobile' ),
				'mobile_only'         => true,
				'options'             => array(
					1 => array(
						'name'  => 1,
						'value' => 1,
					),
					2 => array(
						'name'  => 2,
						'value' => 2,
					),
					3 => array(
						'name'  => 3,
						'value' => 3,
					),
					4 => array(
						'name'  => 4,
						'value' => 4,
					),
					5 => array(
						'name'  => 5,
						'value' => 5,
					),
					6 => array(
						'name'  => 6,
						'value' => 6,
					),
				),
				'priority'            => 142,
			)
		);

		Options::add_field(
			array(
				'id'        => 'bought_together_form_width',
				'name'      => esc_html__( 'Form width', 'xts-theme' ),
				'group'     => esc_html__( 'Frequently bought together', 'xts-theme' ),
				'type'      => 'responsive_range',
				'section'   => 'general_shop_section',
				'selectors' => array(
					'.xts-fbt.xts-design-side' => array(
						'--xts-form-width: {{VALUE}}{{UNIT}};',
					),
				),
				'devices'   => array(
					'desktop' => array(
						'value' => '',
						'unit'  => 'px',
					),
				),
				'range'     => array(
					'px' => array(
						'min'  => 250,
						'max'  => 600,
						'step' => 1,
					),
					'%'  => array(
						'min'  => 0,
						'max'  => 100,
						'step' => 1,
					),
				),
				'priority'  => 150,
			)
		);
	}

	/**
	 *  Custom post type.
	 *
	 * @return array
	 */
	public function get_fbt_post_type_args() {
		return array(
			'label'              => esc_html__( 'Frequently Bought Together', 'xts-theme' ),
			'labels'             => array(
				'name'          => esc_html__( 'Frequently Bought Together', 'xts-theme' ),
				'singular_name' => esc_html__( 'Frequently Bought Together', 'xts-theme' ),
				'menu_name'     => esc_html__( 'Frequently Bought Together', 'xts-theme' ),
				'add_new_item'  => esc_html__( 'Add New', 'xts-theme' ),
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
	 * Add metaboxes.
	 */
	public function add_metaboxes() {
		if ( ! xts_get_opt( 'bought_together_enabled', 1 ) ) {
			return;
		}

		$metabox = Metaboxes::add_metabox(
			array(
				'id'         => 'xts_woo_fbt_metaboxes',
				'title'      => esc_html__( 'Settings', 'xts-theme' ),
				'post_types' => array( 'xts_woo_fbt' ),
			)
		);

		$metabox->add_section(
			array(
				'id'       => 'general',
				'name'     => esc_html__( 'General', 'xts-theme' ),
				'priority' => 10,
				'icon'     => 'xf-general',
			)
		);

		$metabox->add_field(
			array(
				'id'          => 'main_products_discount',
				'name'        => esc_html__( 'Primary product discount', 'xts-theme' ),
				'description' => esc_html__( 'Set a discount for the primary product.', 'xts-theme' ),
				'type'        => 'text_input',
				'attributes'  => array(
					'type' => 'number',
					'min'  => '0',
					'max'  => '100',
					'step' => '0.01',
				),
				'section'     => 'general',
				'priority'    => 20,
				'class'       => 'xts-field-input-append xts-input-percent',
			)
		);

		$metabox->add_field(
			array(
				'id'           => 'fbt_products',
				'type'         => 'select_with_table',
				'section'      => 'general',
				'name'         => '',
				'group'        => esc_html__( 'Bundle products', 'xts-theme' ),
				'select2'      => true,
				'autocomplete' => array(
					'type'   => 'post',
					'value'  => '["product", "product_variation"]',
					'search' => 'xts_get_posts_by_query_autocomplete',
					'render' => 'xts_get_posts_by_ids_autocomplete',
				),
				'default'      => array(
					array(
						'id'       => '',
						'discount' => '',
					),
				),
				'priority'     => 30,
			)
		);

		$metabox->add_field(
			array(
				'id'          => 'allow_customize',
				'name'        => esc_html__( 'Allow customize', 'xts-theme' ),
				'description' => esc_html__( 'Enable this option to allow users customize the bundle and check/uncheck some products.', 'xts-theme' ),
				'group'       => esc_html__( 'Settings', 'xts-theme' ),
				'type'        => 'switcher',
				'section'     => 'general',
				'default'     => '1',
				'class'       => 'xts-col-6',
				'priority'    => 40,
			)
		);
		$metabox->add_field(
			array(
				'id'       => 'fbt_default_checkbox_state',
				'name'     => esc_html__( 'Default checkbox state', 'xts-theme' ),
				'group'    => esc_html__( 'Settings', 'xts-theme' ),
				'type'     => 'buttons',
				'section'  => 'general',
				'options'  => array(
					'check'   => array(
						'name'  => esc_html__( 'Check', 'xts-theme' ),
						'value' => 'check',
					),
					'uncheck' => array(
						'name'  => esc_html__( 'Uncheck', 'xts-theme' ),
						'value' => 'uncheck',
					),
				),
				'default'  => 'check',
				'requires' => array(
					array(
						'key'     => 'allow_customize',
						'compare' => 'equals',
						'value'   => true,
					),
				),
				'priority' => 50,
				'class'    => 'xts-col-6',
			)
		);

		$metabox->add_field(
			array(
				'id'       => 'fbt_hide_out_of_stock_product',
				'name'     => esc_html__( 'Hide out of stock product', 'xts-theme' ),
				'group'    => esc_html__( 'Settings', 'xts-theme' ),
				'type'     => 'switcher',
				'section'  => 'general',
				'on-text'  => esc_html__( 'Yes', 'xts-theme' ),
				'off-text' => esc_html__( 'No', 'xts-theme' ),
				'priority' => 60,
				'requires' => array(
					array(
						'key'     => 'allow_customize',
						'compare' => 'equals',
						'value'   => true,
					),
				),
			)
		);
	}
}
