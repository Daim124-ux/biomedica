<?php
/**
 * Marquee map
 *
 * @package xts
 */

namespace XTS\Elementor;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Typography;
use Elementor\Repeater;
use Elementor\Widget_Base;
use Elementor\Plugin;
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

/**
 * Elementor widget that inserts an embeddable content into the page, from any given URL.
 *
 * @since 1.6.0
 */
class Marquee extends Widget_Base {
	/**
	 * Get widget name.
	 *
	 * @since 1.6.0
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'xts_marquee';
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
		return esc_html__( 'Marquee', 'xts-theme' );
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
		return 'xf-woo-el-marquee';
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

		$this->add_responsive_control(
			'speed',
			array(
				'label'       => esc_html__( 'Scrolling speed', 'xts-theme' ),
				'description' => esc_html__( 'Duration of one animation cycle (in seconds)', 'xts-theme' ),
				'placeholder' => '5',
				'type'        => Controls_Manager::NUMBER,
				'selectors'   => array(
					'{{WRAPPER}} .xts-marquee' => '--xts-marquee-speed: {{VALUE}}s;',
				),
			)
		);

		$this->add_control(
			'direction',
			array(
				'label'     => esc_html__( 'Scrolling direction', 'xts-theme' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => array(
					''                  => esc_html__( 'Right to left', 'xts-theme' ),
					'reverse'           => esc_html__( 'Left to right', 'xts-theme' ),
					'alternate'         => esc_html__( 'Right to left and reverse', 'xts-theme' ),
					'alternate-reverse' => esc_html__( 'Left to right and reverse', 'xts-theme' ),
				),
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .xts-marquee' => '--xts-marquee-direction: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'paused_on_hover',
			array(
				'label'        => esc_html__( 'Pause on hover', 'xts-theme' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'no',
				'label_on'     => esc_html__( 'Yes', 'xts-theme' ),
				'label_off'    => esc_html__( 'No', 'xts-theme' ),
				'return_value' => 'yes',
			)
		);

		$this->end_controls_section();

		/**
		 * Content settings
		 */
		$this->start_controls_section(
			'content_section',
			array(
				'label' => esc_html__( 'Content', 'xts-theme' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'text',
			array(
				'label'   => esc_html__( 'Text', 'xts-theme' ),
				'type'    => Controls_Manager::TEXTAREA,
				'default' => esc_html__( 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.', 'elementor' ),
			)
		);

		$repeater->add_control(
			'link',
			array(
				'label'       => esc_html__( 'Link', 'xts-theme' ),
				'description' => esc_html__( 'Enter URL if you want this banner to have a link.', 'xts-theme' ),
				'type'        => Controls_Manager::URL,
				'default'     => array(
					'url'         => '',
					'is_external' => false,
					'nofollow'    => false,
				),
			)
		);

		$repeater->add_control(
			'icon_type',
			array(
				'label'   => esc_html__( 'Icon type', 'xts-theme' ),
				'type'    => Controls_Manager::SELECT,
				'options' => array(
					'inherit' => esc_html__( 'Inherit', 'xts-theme' ),
					'image'   => esc_html__( 'With image', 'xts-theme' ),
				),
				'default' => 'inherit',
			)
		);

		$repeater->add_control(
			'icon_image',
			array(
				'label'     => esc_html__( 'Choose image', 'xts-theme' ),
				'type'      => Controls_Manager::MEDIA,
				'condition' => array(
					'icon_type' => array( 'image' ),
				),
			)
		);

		$repeater->add_group_control(
			Group_Control_Image_Size::get_type(),
			array(
				'name'      => 'icon_image',
				'default'   => 'thumbnail',
				'separator' => 'none',
				'condition' => array(
					'icon_type' => array( 'image' ),
				),
			)
		);

		$this->add_control(
			'marquee_contents',
			array(
				'label'   => esc_html__( 'Marquee content', 'xts-theme' ),
				'type'    => Controls_Manager::REPEATER,
				'fields'  => $repeater->get_controls(),
				'default' => array(
					array(),
				),
			)
		);

		$this->end_controls_section();

		/**
		 * Icon settings.
		 */
		$this->start_controls_section(
			'icon_content_section',
			array(
				'label' => esc_html__( 'Icon', 'xts-theme' ),
			)
		);

		$this->add_control(
			'icon_type',
			array(
				'label'   => esc_html__( 'Type', 'xts-theme' ),
				'type'    => Controls_Manager::SELECT,
				'options' => array(
					'icon'    => esc_html__( 'With icon', 'xts-theme' ),
					'image'   => esc_html__( 'With image', 'xts-theme' ),
					'without' => esc_html__( 'Without icon', 'xts-theme' ),
				),
				'default' => 'without',
			)
		);

		$this->add_control(
			'icon_image',
			array(
				'label'     => esc_html__( 'Choose image', 'xts-theme' ),
				'type'      => Controls_Manager::MEDIA,
				'condition' => array(
					'icon_type' => array( 'image' ),
				),
			)
		);

		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			array(
				'name'      => 'icon_image',
				'default'   => 'thumbnail',
				'separator' => 'none',
				'condition' => array(
					'icon_type' => array( 'image' ),
				),
			)
		);

		$this->add_control(
			'icon',
			array(
				'label'     => esc_html__( 'Icon', 'xts-theme' ),
				'type'      => Controls_Manager::ICONS,
				'default'   => array(
					'value'   => 'fas fa-star',
					'library' => 'fa-solid',
				),
				'condition' => array(
					'icon_type' => array( 'icon' ),
				),
			)
		);

		$this->end_controls_section();

		/**
		 * Content settings.
		 */
		$this->start_controls_section(
			'general_style_section',
			array(
				'label' => esc_html__( 'Content', 'xts-theme' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'marquee_typography',
				'label'    => esc_html__( 'Typography', 'xts-theme' ),
				'selector' => '{{WRAPPER}} .xts-marquee',
			)
		);

		$this->add_control(
			'marquee_color',
			array(
				'label'     => esc_html__( 'Color', 'xts-theme' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .xts-marquee' => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'content_gap',
			array(
				'label'     => esc_html__( 'Items gap', 'xts-theme' ),
				'type'      => Controls_Manager::SLIDER,
				'default'   => array(
					'size' => '',
				),
				'range'     => array(
					'px' => array(
						'min'  => 0,
						'max'  => 200,
						'step' => 1,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .xts-marquee' => '--xts-marquee-gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		/**
		 * Icon settings.
		 */
		$this->start_controls_section(
			'icon_style_section',
			array(
				'label'     => esc_html__( 'Icon', 'xts-theme' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'icon_type' => array( 'icon' ),
				),
			)
		);

		$this->add_control(
			'marquee_icon_color',
			array(
				'label'     => esc_html__( 'Color', 'xts-theme' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .xts-marquee .xts-icon svg, {{WRAPPER}} .xts-marquee .xts-icon i' => 'color: {{VALUE}}',
				),
				'condition' => array(
					'icon_type' => array( 'icon' ),
				),
			)
		);

		$this->add_control(
			'marquee_icon_size',
			array(
				'label'     => esc_html__( 'Size', 'xts-theme' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => array(
					'px' => array(
						'min'  => 1,
						'max'  => 100,
						'step' => 1,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .xts-marquee .xts-icon svg, {{WRAPPER}} .xts-marquee .xts-icon i' => 'font-size: {{SIZE}}{{UNIT}};',
				),
				'condition' => array(
					'icon_type' => array( 'icon' ),
				),
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
		$element_args = $this->get_settings_for_display();
		xts_marquee_template( $element_args, $this );
	}
}

Plugin::instance()->widgets_manager->register( new Marquee() );
