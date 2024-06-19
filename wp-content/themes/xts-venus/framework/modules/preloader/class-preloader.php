<?php
/**
 * Preloader class.
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
 * Preloader class.
 *
 * @since 1.0.0
 */
class Preloader extends Module {
	/**
	 * Basic initialization class required for Module class.
	 *
	 * @since 1.0.0
	 */
	public function init() {
		add_action( 'init', array( $this, 'hooks' ) );
		add_action( 'init', array( $this, 'add_options' ) );
	}

	/**
	 * Add options
	 *
	 * @since 1.0.0
	 */
	public function add_options() {
		Options::add_section(
			array(
				'id'       => 'preloader_section',
				'name'     => esc_html__( 'Preloader', 'xts-theme' ),
				'priority' => 40,
				'parent'   => 'general_performance_section',
				'icon'     => 'xf-performance',
			)
		);

		Options::add_field(
			array(
				'id'          => 'preloader',
				'name'        => esc_html__( 'Preloader (beta)', 'xts-theme' ),
				'description' => esc_html__( 'Enable preloader animation while loading your website content. Useful when you move all the CSS to the footer.', 'xts-theme' ),
				'type'        => 'switcher',
				'section'     => 'preloader_section',
				'default'     => '0',
				'priority'    => 10,
			)
		);

		Options::add_field(
			array(
				'id'       => 'preloader_image',
				'name'     => esc_html__( 'Animated image', 'xts-theme' ),
				'type'     => 'upload',
				'section'  => 'preloader_section',
				'priority' => 20,
			)
		);

		Options::add_field(
			array(
				'id'       => 'preloader_background_color',
				'name'     => esc_html__( 'Background for loader screen', 'xts-theme' ),
				'type'     => 'color',
				'default'  => array(
					'idle' => '#ffffff',
				),
				'section'  => 'preloader_section',
				'priority' => 30,
			)
		);
	}

	/**
	 * Hooks
	 *
	 * @since 1.0.0
	 */
	public function hooks() {
		add_action( 'xts_before_site_wrapper', array( $this, 'template' ), 500 );
	}

	/**
	 * Template
	 *
	 * @since 1.0.0
	 */
	public function template() {
		if ( ! xts_get_opt( 'preloader' ) ) {
			return;
		}

		$background_color = xts_get_opt( 'preloader_background_color' );
		$image            = xts_get_opt( 'preloader_image' );
		$classes          = '';

		if ( isset( $image['id'] ) && $image['id'] ) {
			$classes .= ' xts-with-img';
		}

		xts_enqueue_js_script( 'preloader' );
		?>
		<style class="xts-preloader-style">
			html {
				overflow: hidden;
			}
		</style>
		<div class="xts-preloader<?php echo esc_attr( $classes ); ?>">
			<style>
				<?php if ( isset( $background_color['idle'] ) && $background_color['idle'] ) : ?>
				.xts-preloader {
					background-color: <?php echo esc_attr( $background_color['idle'] ); ?>
				}
				<?php endif; ?>
			</style>

			<div class="xts-preloader-img">
				<?php if ( isset( $image['id'] ) && $image['id'] ) : ?>
					<img src="<?php echo esc_url( wp_get_attachment_url( $image['id'] ) ); ?>" alt="<?php esc_attr_e( 'preloader', 'xts-theme' ); ?>">
				<?php endif; ?>
			</div>
		</div>
		<?php
	}
}
