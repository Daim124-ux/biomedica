<?php
/**
 * Maintenance mode class.
 *
 * @package xts
 */

namespace XTS\Modules;

if ( ! defined( 'ABSPATH' ) ) {
	exit( 'No direct script access allowed' );
}

use XTS\Framework\Module;
use XTS\Framework\Options;

/**
 * Maintenance mode class.
 *
 * @since 1.0.0
 */
class Maintenance extends Module {
	/**
	 * Basic initialization class required for Module class.
	 *
	 * @since 1.0.0
	 */
	public function init() {
		add_action( 'init', array( $this, 'add_options' ), 10 );
		add_action( 'template_redirect', array( $this, 'maintenance_redirect' ), 10 );
	}

	/**
	 * Add theme settings options.
	 *
	 * @since 1.0.0
	 */
	public function maintenance_redirect() {
		$maintenance_page = xts_get_opt( 'maintenance_page' );

		if ( ! xts_get_opt( 'maintenance_mode' ) || ! $maintenance_page || is_user_logged_in() || ( xts_get_opt( 'maintenance_mode' ) && isset( $_GET[ xts_get_opt( 'maintenance_access_key' ) ] ) ) ) { // phpcs:ignore
			return;
		}

		if ( ! is_page( $maintenance_page ) ) {
			wp_safe_redirect( get_permalink( $maintenance_page ) );
			exit();
		}
	}

	/**
	 * Add theme settings options
	 *
	 * @since 1.0.0
	 */
	public function add_options() {
		/**
		 * Maintenance.
		 */
		Options::add_field(
			array(
				'id'          => 'maintenance_mode',
				'name'        => esc_html__( 'Enable maintenance mode', 'xts-theme' ),
				'description' => esc_html__( 'This will block non-logged users access to the site.', 'xts-theme' ),
				'type'        => 'switcher',
				'section'     => 'maintenance_section',
				'default'     => '0',
				'priority'    => 10,
			)
		);

		Options::add_field(
			array(
				'id'           => 'maintenance_page',
				'name'         => esc_html__( 'Select page', 'xts-theme' ),
				'description'  => esc_html__( 'Create a simple page that will be displayed to all not-logged visitors.', 'xts-theme' ),
				'type'         => 'select',

				'section'      => 'maintenance_section',
				'empty_option' => true,
				'select2'      => true,
				'options'      => xts_get_pages_array(),
				'priority'     => 20,
				'requires'     => array(
					array(
						'key'     => 'maintenance_mode',
						'compare' => 'equals',
						'value'   => '1',
					),
				),
			)
		);

		Options::add_field(
			array(
				'id'          => 'maintenance_access_key',
				'name'        => esc_html__( 'Access key for maintenance mode', 'xts-theme' ),
				'description' => esc_html__( 'You can pass a special GET parameter to suppress the maintenance mode. For example, https://website.com/?suppress_maintenance', 'xts-theme' ),
				'type'        => 'text_input',
				'section'     => 'maintenance_section',
				'priority'    => 30,
				'requires'    => array(
					array(
						'key'     => 'maintenance_mode',
						'compare' => 'equals',
						'value'   => '1',
					),
				),
				'default'     => '',
			)
		);
	}
}
