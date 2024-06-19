<?php
/**
 * Search icon for mobile devices
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
 * Search icon for mobile devices
 */
class Mobile_Search extends Element {
	/**
	 * Object constructor. Init basic things.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		parent::__construct();
		$this->template_name = 'mobile-search';
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
	
		$this->args = array(
			'type'            => 'mobilesearch',
			'title'           => esc_html__( 'Search', 'xts-theme' ),
			'text'            => esc_html__( 'Search form', 'xts-theme' ),
			'icon'            => XTS_ASSETS_IMAGES_URL . '/header-builder/elements/search.svg',
			'editable'        => true,
			'container'       => false,
			'edit_on_create'  => true,
			'drag_target_for' => array(),
			'drag_source'     => 'content_element',
			'removable'       => true,
			'addable'         => true,
			'mobile'          => true,
			'params'          => array(

				'display'           => array(
					'id'          => 'display',
					'title'       => esc_html__( 'Display', 'xts-theme' ),
					'type'        => 'selector',
					'tab'         => esc_html__( 'General', 'xts-theme' ),
					'value'       => 'icon',
					'options'     => array(
						'icon' => array(
							'value' => 'icon',
							'label' => esc_html__( 'Icon', 'xts-theme' ),
						),
						'form' => array(
							'value' => 'form',
							'label' => esc_html__( 'Form', 'xts-theme' ),
						),
					),
					'description' => esc_html__( 'Display search icon/form in the header in different views.', 'xts-theme' ),
				),

				'search_style'      => array(
					'id'       => 'search_style',
					'title'    => esc_html__( 'Search style', 'xts-theme' ),
					'type'     => 'selector',
					'tab'      => esc_html__( 'General', 'xts-theme' ),
					'value'    => 'icon-alt',
					'options'  => xts_get_available_options( 'search_style_header_builder' ),
					'requires' => array(
						'display' => array(
							'comparison' => 'equal',
							'value'      => 'form',
						),
					),
				),

				'form_color_scheme' => array(
					'id'       => 'form_color_scheme',
					'title'    => esc_html__( 'Color scheme', 'xts-theme' ),
					'tab'      => esc_html__( 'General', 'xts-theme' ),
					'type'     => 'selector',
					'value'    => 'dark',
					'options'  => array(
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
					'requires' => array(
						'display' => array(
							'comparison' => 'equal',
							'value'      => 'form',
						),
					),
				),

				'style'             => array(
					'id'          => 'style',
					'title'       => esc_html__( 'Style', 'xts-theme' ),
					'type'        => 'selector',
					'tab'         => esc_html__( 'General', 'xts-theme' ),
					'value'       => 'icon',
					'options'     => xts_get_available_options( 'search_icon_style_header_builder' ),
					'description' => esc_html__( 'You can change the search icon style.', 'xts-theme' ),
					'requires'    => array(
						'display' => array(
							'comparison' => 'equal',
							'value'      => 'icon',
						),
					),
				),

				'post_type'         => array(
					'id'          => 'post_type',
					'title'       => esc_html__( 'Post type', 'xts-theme' ),
					'type'        => 'selector',
					'tab'         => esc_html__( 'General', 'xts-theme' ),
					'value'       => 'product',
					'options'     => $post_types,
					'description' => esc_html__( 'You can set up the search for posts, pages, projects or for products (woocommerce)', 'xts-theme' ),
					'requires'    => array(
						'display' => array(
							'comparison' => 'equal',
							'value'      => 'form',
						),
					),
				),

				'icon_type'         => array(
					'id'      => 'icon_type',
					'title'   => esc_html__( 'Icon type', 'xts-theme' ),
					'type'    => 'selector',
					'tab'     => esc_html__( 'General', 'xts-theme' ),
					'value'   => 'default',
					'options' => array(
						'default' => array(
							'value' => 'default',
							'label' => esc_html__( 'Default', 'xts-theme' ),
							'image' => XTS_ASSETS_IMAGES_URL . '/header-builder/search/icon/default.svg',
						),
						'custom'  => array(
							'value' => 'custom',
							'label' => esc_html__( 'Custom', 'xts-theme' ),
							'image' => XTS_ASSETS_IMAGES_URL . '/header-builder/custom-icon.svg',
						),
					),
				),

				'custom_icon'       => array(
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
					),
				),
			),
		);
	}
}
