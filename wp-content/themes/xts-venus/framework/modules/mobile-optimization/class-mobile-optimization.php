<?php
/**
 * Mobile Optimization class.
 *
 * @package xts
 */

namespace XTS\Modules;

if ( ! defined( 'ABSPATH' ) ) {
	exit( 'No direct script access allowed' );
}

use XTS\Framework\Module;
use XTS\Framework\Options;
use XTS\Options\Metaboxes;

/**
 * Mobile Optimization class.
 *
 * @since 1.0.0
 */
class Mobile_Optimization extends Module {
	/**
	 * Basic initialization class required for Module class.
	 *
	 * @since 1.0.0
	 */
	public function init() {
		add_action( 'init', array( $this, 'add_options' ) );
		add_action( 'init', array( $this, 'add_metaboxes' ), 110 );
	}

	/**
	 * Add options
	 *
	 * @since 1.0.0
	 */
	public function add_options() {
		Options::add_section(
			array(
				'id'       => 'mobile_performance_section',
				'name'     => esc_html__( 'Mobile optimization', 'xts-theme' ),
				'priority' => 50,
				'parent'   => 'general_performance_section',
				'icon'     => 'xf-performance',
			)
		);

		Options::add_field(
			array(
				'id'          => 'mobile_optimization',
				'type'        => 'switcher',
				'section'     => 'mobile_performance_section',
				'name'        => esc_html__( 'Mobile DOM optimization (experimental)', 'xts-theme' ),
				'description' => esc_html__( 'You can reduce the number of DOM elements on mobile devices. This option currently removes all HTML tags from the desktop header version on mobile devices.', 'xts-theme' ),
				'default'     => '0',
				'priority'    => 10,
			)
		);
	}

	/**
	 * Register page metaboxes.
	 *
	 * @since 1.0.0
	 */
	public function add_metaboxes() {
		$page_mobile_metabox = Metaboxes::add_metabox(
			array(
				'id'         => 'xts_page_mobile_metaboxes',
				'title'      => esc_html__( 'Page mobile optimizations', 'xts-theme' ),
				'post_types' => array( 'page' ),
			)
		);

		$page_mobile_metabox->add_section(
			array(
				'id'       => 'performance_section',
				'name'     => esc_html__( 'Performance', 'xts-theme' ),
				'priority' => 10,
				'icon'     => 'xf-performance',
			)
		);

		$page_mobile_metabox->add_field(
			array(
				'id'           => 'mobile_content',
				'name'         => esc_html__( 'Mobile version HTML block (experimental)', 'xts-theme' ),
				'description'  => esc_html__( 'You can create a separate content that will be displayed on mobile devices to optimize the performance.', 'xts-theme' ),
				'group'        => esc_html__( 'Mobile optimizations', 'xts-theme' ),
				'type'         => 'select',
				'section'      => 'performance_section',
				'empty_option' => true,
				'options'      => xts_get_html_blocks_array(),
				'priority'     => 10,
			)
		);
	}
}
