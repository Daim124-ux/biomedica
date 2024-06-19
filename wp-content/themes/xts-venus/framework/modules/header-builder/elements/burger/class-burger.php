<?php
/**
 * Mobile menu burger icon
 *
 * @package xts
 */

namespace XTS\Header_Builder;

if ( ! defined( 'ABSPATH' ) ) {
	exit( 'No direct script access allowed' );
}

use XTS\Framework\Modules;
use XTS\Header_Builder\Element;

/**
 * Mobile menu burger icon class
 */
class Burger extends Element {
	/**
	 * Object constructor. Init basic things.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		parent::__construct();
		$this->template_name = 'burger';
	}

	/**
	 * Map element parameters.
	 *
	 * @since 1.0.0
	 */
	public function map() {
		$post_types = array(
			'post'          => array(
				'value' => 'post',
				'label' => esc_html__( 'Post', 'xts-theme' ),
			),
			'xts-portfolio' => array(
				'value' => 'xts-portfolio',
				'label' => esc_html__( 'Portfolio', 'xts-theme' ),
			),
			'page'          => array(
				'value' => 'page',
				'label' => esc_html__( 'Page', 'xts-theme' ),
			),
		);

		if ( xts_is_woocommerce_installed() ) {
			$post_types['product'] = array(
				'value' => 'product',
				'label' => esc_html__( 'Product', 'xts-theme' ),
			);
		}

		$post_types['any'] = array(
			'value' => 'any',
			'label' => esc_html__( 'All post types', 'xts-theme' ),
		);

		$options = xts_get_menus_array( 'header_builder' );
		$first   = reset( $options );

		$this->args = array(
			'type'            => 'burger',
			'title'           => esc_html__( 'Mobile menu', 'xts-theme' ),
			'text'            => esc_html__( 'Mobile burger icon', 'xts-theme' ),
			'icon'            => XTS_ASSETS_IMAGES_URL . '/header-builder/elements/mobile-menu.svg',
			'editable'        => true,
			'container'       => false,
			'edit_on_create'  => true,
			'drag_target_for' => array(),
			'drag_source'     => 'content_element',
			'removable'       => true,
			'addable'         => true,
			'params'          => array(

				'style'                => array(
					'id'          => 'style',
					'title'       => esc_html__( 'Style', 'xts-theme' ),
					'type'        => 'selector',
					'tab'         => esc_html__( 'General', 'xts-theme' ),
					'value'       => 'icon',
					'options'     => array(
						'icon'      => array(
							'value' => 'icon',
							'label' => esc_html__( 'Icon only', 'xts-theme' ),
						),
						'icon-text' => array(
							'value' => 'icon-text',
							'label' => esc_html__( 'Icon with text', 'xts-theme' ),
						),
						'text'      => array(
							'value' => 'text',
							'label' => esc_html__( 'Only text', 'xts-theme' ),
						),
					),
					'description' => esc_html__( 'You can change the burger icon style.', 'xts-theme' ),
				),

				'icon_type'            => array(
					'id'       => 'icon_type',
					'title'    => esc_html__( 'Icon', 'xts-theme' ),
					'type'     => 'selector',
					'tab'      => esc_html__( 'General', 'xts-theme' ),
					'value'    => 'default',
					'options'  => array(
						'default' => array(
							'value' => 'default',
							'label' => esc_html__( 'Default', 'xts-theme' ),
							'image' => XTS_ASSETS_IMAGES_URL . '/header-builder/burger-menu.svg',
						),
						'custom'  => array(
							'value' => 'custom',
							'label' => esc_html__( 'Custom', 'xts-theme' ),
							'image' => XTS_ASSETS_IMAGES_URL . '/header-builder/custom-icon.svg',
						),
					),
					'requires' => array(
						'style' => array(
							'comparison' => 'not_equal',
							'value'      => 'text',
						),
					),
				),

				'custom_icon'          => array(
					'id'          => 'custom_icon',
					'title'       => esc_html__( 'Custom icon', 'xts-theme' ),
					'type'        => 'image',
					'tab'         => esc_html__( 'General', 'xts-theme' ),
					'value'       => '',
					'description' => '',
					'requires'    => array(
						'icon_type' => array(
							'comparison' => 'equal',
							'value'      => 'custom',
						),
						'style'     => array(
							'comparison' => 'not_equal',
							'value'      => 'text',
						),
					),
				),

				'position'             => array(
					'id'          => 'position',
					'type'        => 'selector',
					'title'       => esc_html__( 'Position', 'xts-theme' ),
					'tab'         => esc_html__( 'General', 'xts-theme' ),
					'value'       => 'left',
					'options'     => array(
						'left'  => array(
							'value' => 'left',
							'label' => esc_html__( 'Left', 'xts-theme' ),
						),
						'right' => array(
							'value' => 'right',
							'label' => esc_html__( 'Right', 'xts-theme' ),
						),
					),
					'description' => esc_html__( 'Position of the mobile menu sidebar.', 'xts-theme' ),
				),

				'color_scheme'         => array(
					'id'      => 'color_scheme',
					'type'    => 'selector',
					'title'   => esc_html__( 'Color scheme', 'xts-theme' ),
					'tab'     => esc_html__( 'General', 'xts-theme' ),
					'value'   => 'dark',
					'options' => array(
						'dark'  => array(
							'value' => 'dark',
							'label' => esc_html__( 'Dark', 'xts-theme' ),
							'image' => XTS_ASSETS_IMAGES_URL . '/header-builder/color/dark.svg',
						),
						'light' => array(
							'value' => 'light',
							'label' => esc_html__( 'Light', 'xts-theme' ),
							'image' => XTS_ASSETS_IMAGES_URL . '/header-builder/color/light.svg',
						),
					),
				),

				'search_form'          => array(
					'id'    => 'search_form',
					'type'  => 'switcher',
					'title' => esc_html__( 'Show search form', 'xts-theme' ),
					'tab'   => esc_html__( 'General', 'xts-theme' ),
					'value' => true,
				),

				'post_type'            => array(
					'id'          => 'post_type',
					'title'       => esc_html__( 'Search Post type', 'xts-theme' ),
					'type'        => 'selector',
					'tab'         => esc_html__( 'General', 'xts-theme' ),
					'value'       => 'post',
					'options'     => $post_types,
					'description' => esc_html__( 'You can set up the search for posts, pages, projects or for products (woocommerce).', 'xts-theme' ),
					'requires'    => array(
						'search_form' => array(
							'comparison' => 'equal',
							'value'      => true,
						),
					),
				),

				'show_html_block'      => array(
					'id'          => 'show_html_block',
					'type'        => 'switcher',
					'title'       => esc_html__( 'Show HTML Blocks', 'xts-theme' ),
					'tab'         => esc_html__( 'General', 'xts-theme' ),
					'description' => esc_html__( 'HTML Blocks that were assigned to the menu items will be shown as items submenus.', 'xts-theme' ),
					'value'       => false,
				),

				'categories_menu'      => array(
					'id'    => 'categories_menu',
					'type'  => 'switcher',
					'title' => esc_html__( 'Show categories menu', 'xts-theme' ),
					'tab'   => esc_html__( 'General', 'xts-theme' ),
					'value' => false,
				),

				'primary_menu_title'   => array(
					'id'          => 'primary_menu_title',
					'type'        => 'text',
					'title'       => esc_html__( 'First menu tab title', 'xts-theme' ),
					'description' => esc_html__( 'You can rewrite mobile menu tab title with this option. Or leave empty to have a default one - Menu.', 'xts-theme' ),
					'tab'         => esc_html__( 'General', 'xts-theme' ),
					'value'       => '',
					'requires'    => array(
						'categories_menu' => array(
							'comparison' => 'equal',
							'value'      => true,
						),
					),
				),

				'secondary_menu_title' => array(
					'id'          => 'secondary_menu_title',
					'title'       => esc_html__( 'Second menu tab title', 'xts-theme' ),
					'type'        => 'text',
					'tab'         => esc_html__( 'General', 'xts-theme' ),
					'description' => esc_html__( 'You can rewrite mobile menu tab title with this option. Or leave empty to have a default one - Categories.', 'xts-theme' ),
					'value'       => '',
					'requires'    => array(
						'categories_menu' => array(
							'comparison' => 'equal',
							'value'      => true,
						),
					),
				),

				'cat_menu_id'          => array(
					'id'          => 'cat_menu_id',
					'title'       => esc_html__( 'Choose menu', 'xts-theme' ),
					'type'        => 'select',
					'tab'         => esc_html__( 'General', 'xts-theme' ),
					'value'       => isset( $first['value'] ) ? $first['value'] : '',
					'options'     => $options,
					'description' => esc_html__( 'Choose which menu to display.', 'xts-theme' ),
					'requires'    => array(
						'categories_menu' => array(
							'comparison' => 'equal',
							'value'      => true,
						),
					),
				),

				'tabs_swap'            => array(
					'id'       => 'tabs_swap',
					'type'     => 'switcher',
					'title'    => esc_html__( 'Swap menus', 'xts-theme' ),
					'tab'      => esc_html__( 'General', 'xts-theme' ),
					'value'    => false,
					'requires' => array(
						'categories_menu' => array(
							'comparison' => 'equal',
							'value'      => true,
						),
					),
				),
			),
		);
	}
}
