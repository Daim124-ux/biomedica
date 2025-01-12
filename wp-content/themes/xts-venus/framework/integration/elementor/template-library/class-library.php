<?php
/**
 * Template library file.
 *
 * @package xts
 */

namespace XTS\Elementor;

use Elementor\Plugin;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * XTS template library.
 *
 * @since 1.1.0
 */
class Library {
	/**
	 * Object constructor. Init basic things.
	 *
	 * @since 1.1.0
	 */
	public function __construct() {
		$this->hooks();
		$this->register_templates_source();
	}

	/**
	 * Initialize Hooks.
	 *
	 * @since 1.1.0
	 */
	public function hooks() {
		add_action( 'elementor/editor/after_enqueue_scripts', array( $this, 'editor_scripts' ) );
		add_action( 'elementor/editor/footer', array( $this, 'html_templates' ) );
	}

	/**
	 * Register source.
	 *
	 * @since 1.1.0
	 */
	public function register_templates_source() {
		Plugin::instance()->templates_manager->register_source( 'XTS\Elementor\Library_Source' );
	}

	/**
	 * Load Editor JS.
	 *
	 * @since 1.1.0
	 */
	public function editor_scripts() {
		if ( ! xts_is_build_for_space() ) {
			return;
		}

		$slug = get_option( 'xts_all_themes_license' ) ? get_option( 'xts_all_themes_license' ) : XTS_THEME_SLUG;

		wp_enqueue_style( 'xts-template-library-style', XTS_FRAMEWORK_URL . '/integration/elementor/assets/css/template-library.css', [], XTS_VERSION, false );

		wp_enqueue_script( 'xts-template-library-script', XTS_FRAMEWORK_URL . '/integration/elementor/assets/js/template-library.js', [], XTS_VERSION, false );

		wp_localize_script(
			'xts-template-library-script',
			'xts_template_library_script',
			array(
				'demoAjaxUrl' => XTS_DEMO_URL . 'tethys/wp-admin/admin-ajax.php',
				'token'       => get_option( 'xts_' . $slug . '_token' ),
			)
		);
	}

	/**
	 * Templates Modal Markup.
	 *
	 * @since 1.1.0
	 */
	public function html_templates() {
		?>
		<script type="text/html" id="tmpl-elementor-xts-library-modal-header">
			<div class="elementor-templates-modal__header">
				<div class="elementor-templates-modal__header__logo-area">
					<div class="elementor-templates-modal__header__logo">
						<span class="elementor-templates-modal__header__logo__title">
							XTemos
						</span>
					</div>
				</div>

				<div class="elementor-templates-modal__header__items-area">
					<div class="elementor-templates-modal__header__close elementor-templates-modal__header__close--normal elementor-templates-modal__header__item">
						<i class="eicon-close" aria-hidden="true" title="<?php echo esc_attr__( 'Close', 'xts-theme' ); ?>"></i>

						<span class="elementor-screen-only">
							<?php echo esc_html__( 'Close', 'xts-theme' ); ?>
						</span>
					</div>
				</div>
			</div>
		</script>

		<script type="text/html" id="tmpl-elementor-xts-library-modal-order">
			<div id="elementor-template-library-filter">
				<select id="elementor-template-library-filter-subtype" class="elementor-template-library-filter-select" data-elementor-filter="subtype">
					<option value="all"><?php echo esc_html__( 'All', 'xts-theme' ); ?></option>
					<# data.tags.forEach(function(item, i) { #>
					<option value="{{{item.slug}}}">{{{item.title}}}</option>
					<# }); #>
				</select>
			</div>
		</script>

		<script type="text/html" id="tmpl-elementor-xts-library-modal">
			<div id="elementor-template-library-templates" data-template-source="remote">
				<div id="elementor-template-library-toolbar">
					<div id="elementor-template-library-filter-toolbar-remote" class="elementor-template-library-filter-toolbar"></div>

					<div id="elementor-template-library-filter-text-wrapper">
						<label for="elementor-template-library-filter-text" class="elementor-screen-only"><?php echo esc_html__( 'Search Templates:', 'xts-theme' ); ?></label>
						<input id="elementor-template-library-filter-text" placeholder="<?php esc_attr_e( 'Search', 'xts-theme' ); ?>">
						<i class="eicon-search"></i>
					</div>
				</div>

				<div id="elementor-template-library-templates-container"></div>
			</div>

			<div class="elementor-loader-wrapper">
				<div class="elementor-loader">
					<div class="elementor-loader-boxes">
						<div class="elementor-loader-box"></div>
						<div class="elementor-loader-box"></div>
						<div class="elementor-loader-box"></div>
						<div class="elementor-loader-box"></div>
					</div>
				</div>
				<div class="elementor-loading-title"><?php echo esc_html__( 'Loading', 'xts-theme' ); ?></div>
			</div>
		</script>

		<script type="text/html" id="tmpl-elementor-xts-library-modal-item">
			<# data.elements.forEach(function(item, i) { #>
			<div class="elementor-template-library-template elementor-template-library-template-remote elementor-template-library-template-block" data-title="{{{item.image}}}" data-slug="{{{item.slug}}}" data-tag="{{{item.class}}}">
				<div class="elementor-template-library-template-body">
					<img src="{{{item.image}}}" alt="{{{item.title}}}" />

					<a class="elementor-template-library-template-preview" href="{{{item.link}}}" target="_blank">
						<i class="eicon-zoom-in-bold" aria-hidden="true"></i>
					</a>
				</div>

				<div class="elementor-template-library-template-footer">
					<a class="elementor-template-library-template-action elementor-template-library-template-insert elementor-button" data-id="{{{item.id}}}" data-blog_id="{{{item.blog_id}}}">
						<i class="eicon-file-download" aria-hidden="true"></i>
						<span class="elementor-button-title">Insert</span>
					</a>
					<div class="xts-elementor-template-library-template-name">{{{item.title}}}</div>
				</div>
			</div>
			<# }); #>
		</script>
		<?php
	}
}

new Library();
