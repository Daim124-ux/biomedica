<?php
/**
 * Nested carousel element.
 *
 * @package xts
 */

namespace XTS\Elementor;

use Elementor\Controls_Manager;
use Elementor\Modules\NestedElements\Module as NestedElementsModule;
use Elementor\Modules\NestedElements\Base\Widget_Nested_Base;
use Elementor\Modules\NestedElements\Controls\Control_Nested_Repeater;
use Elementor\Plugin;
use Elementor\Repeater;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

/**
 * Elementor widget that inserts an embeddable content into the page, from any given URL.
 *
 * @since 1.6.0
 */
class Nested_Carousel extends Widget_Nested_Base {
	/**
	 * Get widget name.
	 *
	 * @since 1.6.0
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'xts_nested_carousel';
	}

	/**
	 * Get widget title.
	 *
	 * @since 1.6.0
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return esc_html__( 'Nested carousel', 'xts-theme' );
	}

	/**
	 * Get widget icon.
	 *
	 * @since 1.6.0
	 * @access public
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'xf-el-nested-carousel';
	}

	/**
	 * Get widget categories.
	 *
	 * @since 1.6.0
	 * @access public
	 *
	 * @return array Widget categories.
	 */
	public function get_categories() {
		return array( 'xts-elements' );
	}

	/**
	 * Get widget keywords.
	 *
	 * Retrieve the list of keywords the widget belongs to.
	 *
	 * @since 1.6.0
	 * @access public
	 *
	 * @return array Widget keywords.
	 */
	public function get_keywords() {
		return array( 'nested', 'carousel' );
	}

	public function show_in_panel() {
		return Plugin::$instance->experiments->is_feature_active( NestedElementsModule::EXPERIMENT_NAME );
	}

	protected function slide_content_container( int $index ) {
		return array(
			'elType'   => 'container',
			'settings' => array(
				'_title'        => sprintf( __( 'Slide #%s', 'xts-theme' ), $index ),
				'content_width' => 'full',
			),
		);
	}

	protected function get_default_children_elements() {
		return array(
			$this->slide_content_container( 1 ),
			$this->slide_content_container( 2 ),
			$this->slide_content_container( 3 ),
		);
	}

	protected function get_default_repeater_title_setting_key() {
		return 'slide_title';
	}

	protected function get_default_children_title() {
		return esc_html__( 'Slide #%d', 'xts-theme' );
	}

	protected function get_default_children_placeholder_selector() {
		return '.xts-carousel-wrap';
	}

	protected function get_html_wrapper_class() {
		return 'xts-nested-carousel';
	}

	/**
	 * Register the widget controls.
	 *
	 * @since 1.6.0
	 * @access protected
	 */
	protected function register_controls() {
		/**
		 * General settings
		 */
		$this->start_controls_section(
			'general_section',
			array(
				'label' => esc_html__( 'General', 'xts-theme' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'slide_title',
			array(
				'label'       => esc_html__( 'Slide title', 'xts-theme' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'Slide title', 'xts-theme' ),
				'placeholder' => esc_html__( 'Slide title', 'xts-theme' ),
				'label_block' => true,
				'dynamic'     => array(
					'active' => true,
				),
			)
		);

		$this->add_control(
			'slides',
			array(
				'label'       => esc_html__( 'Slides', 'xts-theme' ),
				'type'        => Control_Nested_Repeater::CONTROL_TYPE,
				'fields'      => $repeater->get_controls(),
				'default'     => array(
					array(
						'slide_title' => esc_html__( 'Slide #1', 'xts-theme' ),
					),
					array(
						'slide_title' => esc_html__( 'Slide #2', 'xts-theme' ),
					),
					array(
						'slide_title' => esc_html__( 'Slide #3', 'xts-theme' ),
					),
				),
				'title_field' => '{{{ slide_title }}}',
				'button_text' => 'Add Slide',
			)
		);
		$this->end_controls_section();

		/**
		 * Style settings
		 */
		$this->start_controls_section(
			'general_style_section',
			array(
				'label' => esc_html__( 'General', 'xts-theme' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		xts_get_carousel_map(
			$this,
			array(
				'arrows_horizontal_position' => true,
				'items'                      => 2,
				'items_tablet'               => 2,
				'items_mobile'               => 1,
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Render the widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.6.0
	 *
	 * @access protected
	 */
	protected function render() {
		$default_args = array(
			'carousel_items'        => array( 'size' => 2 ),
			'carousel_items_tablet' => array( 'size' => 2 ),
			'carousel_items_mobile' => array( 'size' => 1 ),
			'carousel_spacing'      => xts_get_default_value( 'items_gap' ),
			'library'               => 'swiper',
		);
		$settings     = wp_parse_args( $this->get_settings_for_display(), $default_args );

		$slides             = $settings['slides'];
		$slide_content_html = '';
		$wrapper_classes    = '';

		foreach ( $slides as $index => $item ) {

			// Slide content.
			$children = $this->get_children();

			if ( ! empty( $children[ $index ] ) ) {
				ob_start();
				$column_classes_loop = 'elementor-repeater-item-' . $item['_id'];
				?>
				<div class="xts-col <?php echo esc_attr( $column_classes_loop ); ?>">
					<?php
					$children[ $index ]->print_element();
					?>
				</div>
				<?php
				$slide_content_html .= ob_get_clean();
			}
		}

		// Get wrapper classes and attributes.
		$wrapper_classes .= xts_get_carousel_classes( $settings );
		$wrapper_classes .= xts_get_row_classes( $settings['carousel_items']['size'], $settings['carousel_items_tablet']['size'], $settings['carousel_items_mobile']['size'], $settings['carousel_spacing'] );
		$carousel_atts    = xts_get_carousel_atts( $settings );

		?>
		<div class="xts_nested_carousel<?php echo esc_attr( $wrapper_classes ); ?>" <?php echo wp_kses( $carousel_atts, true ); // phpcs:ignore?>>
			<?php echo $slide_content_html; // phpcs:ignore?>
		</div>
		
		<?php
	}

	/**
	 * Get the widget content template.
	 *
	 * @since 1.6.0
	 * @access protected
	 */
	protected function content_template() {
		?>
		<# if ( settings['slides'] ) {
			const elementUid = view.getIDInt().toString().substr(0, 3);
			const carouselOutsideWrapperKey = 'xts-carousel-wrapper-' + elementUid;
			
			const carouselArgs = {
				autoplay: settings.autoplay,
				autoplay_speed: settings.autoplay_speed,
				infinite_loop: settings.infinite_loop,
				center_mode: settings.center_mode,
				draggable: settings.draggable,
				auto_height: settings.auto_height,
				init_on_scroll: settings.init_on_scroll,
				dots: settings.dots,
				dots_color_scheme:  settings.dots_color_scheme,
				arrows: settings.arrows,
				arrows_color_scheme: settings.arrows_color_scheme,
				arrows_vertical_position: settings.arrows_vertical_position,
				arrows_horizontal_position: settings.arrows_horizontal_position,
				arrows_design: 'undefined' !== settings.arrows_design ? settings.arrows_design : "default",
				parent: settings.parent,
				center_mode_opacity: settings.center_mode_opacity,
				library: 'swiper',
				source: settings.source,
				carousel_items: settings.carousel_items,
				carousel_items_tablet: settings.carousel_items_tablet,
				carousel_items_mobile: settings.carousel_items_mobile,
				carousel_spacing: settings.carousel_spacing,
				sync: settings.sync,
				sync_parent_id: settings.sync_parent_id,
				sync_child_id: settings.sync_parent_id,
			};

			let controlsClasses = [
				'xts-arrows-hpos-' + settings.arrows_horizontal_position,
				'xts-arrows-vpos-' + settings.arrows_vertical_position,
				'xts-arrows-design-default',
				'xts-arrows-' + settings.arrows_color_scheme,
				'xts-dots-' + settings.dots_color_scheme,
			]

			// Carousel classes handling
			let carouselClasses = [
				'xts_nested_carousel xts-carousel-wrap xts-carousel xts-lib-swiper',
				(settings.dots === 'yes' ? 'xts-dots-' : ''),
				(settings.arrows === 'yes' ? controlsClasses.join(' ') : ''),
				(settings.init_on_scroll === 'yes' ? 'xts-init-on-scroll' : ''),
				'xts-row-spacing-' + settings.carousel_spacing,
				'xts-row-md-' + settings.carousel_items_tablet.size,
				'xts-row-lg-' + settings.carousel_items.size,
				'xts-row-' + settings.carousel_items_mobile.size
			];

			// Pass 'carouselArgs' as a JSON attribute and other attributes
			view.addRenderAttribute(carouselOutsideWrapperKey, {
				'class': carouselClasses.join(' '),
				'data-carousel-args': JSON.stringify(carouselArgs),
				'data-xts-carousel-dots': settings.dots === 'yes' ? 'yes' : 'no',
				'data-xts-carousel': ''
			});

		#>
		<div {{{ view.getRenderAttributeString(carouselOutsideWrapperKey) }}}>
		</div>
		<# } #>
		<?php
	}
}

Plugin::instance()->widgets_manager->register( new Nested_Carousel() );
