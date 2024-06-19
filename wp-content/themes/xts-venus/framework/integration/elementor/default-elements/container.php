<?php
/**
 * Elementor container custom controls
 *
 * @package xts
 */

use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

if ( ! function_exists( 'xts_add_container_full_width_control' ) ) {
	/**
	 * Container full width option.
	 *
	 * @since 1.0.0
	 *
	 * @param object $element The control.
	 */
	function xts_add_container_full_width_control( $element ) {
		$element->start_controls_section(
			'xts_extra_layout',
			array(
				'label' => esc_html__( '[XTemos] Layout', 'xts-theme' ),
				'tab'   => Controls_Manager::TAB_LAYOUT,
			)
		);

		$options = array(
			'disabled'        => esc_html__( 'Disabled', 'xts-theme' ),
			'stretch'         => esc_html__( 'Stretch section', 'xts-theme' ),
			'stretch-content' => esc_html__( 'Stretch section and content', 'xts-theme' ),
		);

		$element->add_control(
			'xts_section_stretch',
			array(
				'label'        => esc_html__( 'Stretch container CSS', 'xts-theme' ),
				'description'  => esc_html__( 'Enable this option instead of native Elementor\'s one to stretch section with CSS and not with JS.', 'xts-theme' ),
				'type'         => Controls_Manager::SELECT,
				'default'      => 'disabled',
				'options'      => $options,
				'render_type'  => 'template',
				'prefix_class' => 'xts-section-',
			)
		);

		$element->end_controls_section();
	}

	add_action( 'elementor/element/container/section_layout_container/after_section_end', 'xts_add_container_full_width_control' );
}

if ( ! function_exists( 'xts_add_container_custom_controls' ) ) {
	/**
	 * Column section controls.
	 *
	 * @since 1.0.0
	 *
	 * @param object $element The control.
	 */
	function xts_add_container_custom_controls( $element ) {
		$element->start_controls_section(
			'xts_extra_advanced',
			array(
				'label' => esc_html__( '[XTemos] Extra', 'xts-theme' ),
				'tab'   => Controls_Manager::TAB_ADVANCED,
			)
		);

		/**
		 * Container parallax on scroll
		 */
		$element->add_control(
			'column_parallax',
			array(
				'label'        => esc_html__( 'Parallax on scroll', 'xts-theme' ),
				'description'  => esc_html__( 'Smooth element movement when you scroll the page to create beautiful parallax effect.', 'xts-theme' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => '',
				'label_on'     => esc_html__( 'Yes', 'xts-theme' ),
				'label_off'    => esc_html__( 'No', 'xts-theme' ),
				'return_value' => 'parallax-on-scroll',
				'prefix_class' => 'xts-',
				'render_type'  => 'template',
				'condition'    => array(
					'container_sticky' => '',
				),
			)
		);

		$element->add_control(
			'scroll_x',
			array(
				'label'        => esc_html__( 'X axis translation', 'xts-theme' ),
				'description'  => esc_html__( 'Recommended -200 to 200', 'xts-theme' ),
				'type'         => Controls_Manager::TEXT,
				'default'      => 0,
				'render_type'  => 'template',
				'prefix_class' => 'xts_scroll_x_',
				'condition'    => array(
					'column_parallax' => array( 'parallax-on-scroll' ),
				),
			)
		);

		$element->add_control(
			'scroll_y',
			array(
				'label'        => esc_html__( 'Y axis translation', 'xts-theme' ),
				'description'  => esc_html__( 'Recommended -200 to 200', 'xts-theme' ),
				'type'         => Controls_Manager::TEXT,
				'default'      => - 80,
				'render_type'  => 'template',
				'prefix_class' => 'xts_scroll_y_',
				'condition'    => array(
					'column_parallax' => array( 'parallax-on-scroll' ),
				),
			)
		);

		$element->add_control(
			'scroll_z',
			array(
				'label'        => esc_html__( 'Z axis translation', 'xts-theme' ),
				'description'  => esc_html__( 'Recommended -200 to 200', 'xts-theme' ),
				'type'         => Controls_Manager::TEXT,
				'default'      => 0,
				'render_type'  => 'template',
				'prefix_class' => 'xts_scroll_z_',
				'condition'    => array(
					'column_parallax' => array( 'parallax-on-scroll' ),
				),
			)
		);

		$element->add_control(
			'scroll_smoothness',
			array(
				'label'        => esc_html__( 'Parallax smoothness', 'xts-theme' ),
				'description'  => esc_html__( 'Define the parallax smoothness on mouse scroll. By default - 30', 'xts-theme' ),
				'type'         => Controls_Manager::SELECT,
				'options'      => array(
					'10'  => '10',
					'20'  => '20',
					'30'  => '30',
					'40'  => '40',
					'50'  => '50',
					'60'  => '60',
					'70'  => '70',
					'80'  => '80',
					'90'  => '90',
					'100' => '100',
				),
				'default'      => '30',
				'render_type'  => 'template',
				'prefix_class' => 'xts_scroll_smoothness_',
				'condition'    => array(
					'column_parallax' => array( 'parallax-on-scroll' ),
				),
			)
		);

		$element->add_control(
			'column_parallax_hr',
			array(
				'type'  => Controls_Manager::DIVIDER,
				'style' => 'thick',
			)
		);

		/**
		 * Sticky container
		 */
		$element->add_responsive_control(
			'container_sticky',
			array(
				'label'        => esc_html__( 'Sticky container', 'xts-theme' ),
				'type'         => Controls_Manager::SWITCHER,
				'devices'      => array( 'desktop', 'tablet', 'mobile' ),
				'default'      => '',
				'label_on'     => esc_html__( 'Yes', 'xts-theme' ),
				'label_off'    => esc_html__( 'No', 'xts-theme' ),
				'prefix_class' => ' xts-sticky-con%s-',
				'render_type'  => 'template',
				'condition'    => array(
					'column_parallax' => '',
				),
			)
		);

		$element->add_control(
			'container_sticky_hr',
			array(
				'type'  => Controls_Manager::DIVIDER,
				'style' => 'thick',
			)
		);

		$element->add_control(
			'container_sticky_offset',
			array(
				'label'        => esc_html__( 'Sticky container offset (px)', 'xts-theme' ),
				'type'         => Controls_Manager::TEXT,
				'default'      => 50,
				'prefix_class' => 'xts-sticky-offset-',
				'condition'    => array(
					'container_sticky!' => '',
				),
			)
		);

		$element->add_control(
			'xts_container_sticky_hr',
			array(
				'type'  => Controls_Manager::DIVIDER,
				'style' => 'thick',
			)
		);

		/**
		 * Animations
		 */
		xts_get_animation_map( $element );

		$element->end_controls_section();
	}

	add_action( 'elementor/element/container/section_layout/after_section_end', 'xts_add_container_custom_controls' );
}

if ( ! function_exists( 'xts_container_before_render' ) ) {
	/**
	 * Container before render.
	 *
	 * @since 1.0.0
	 *
	 * @param object $widget Element.
	 */
	function xts_container_before_render( $widget ) {
		$settings = $widget->get_settings_for_display();

		if ( isset( $settings['xts_animation'] ) && $settings['xts_animation'] ) {
			xts_enqueue_js_script( 'animations' );
		}

		if ( isset( $settings['column_parallax'] ) && $settings['column_parallax'] ) {
			xts_enqueue_js_library( 'parallax-scroll' );
		}

		if ( ! empty( $settings['container_sticky'] ) || ! empty( $settings['container_sticky_tablet'] ) || ! empty( $settings['container_sticky_mobile'] ) ) {
			xts_enqueue_js_script( 'sticky-container' );
		}
	}

	add_action( 'elementor/frontend/container/before_render', 'xts_container_before_render', 10 );
}

if ( ! function_exists( 'xts_container_negative_gap' ) ) {
	/**
	 * Container negative gap fix.
	 *
	 * @since 1.0.0
	 */
	function xts_container_negative_gap() {
		if ( 'enabled' === xts_get_opt( 'negative_gap', 'enabled' ) ) {
			add_action( 'elementor/frontend/container/before_render', 'xts_add_container_class_if_content_width', 10 );
		}

		$negative_gap = get_post_meta( get_the_ID(), '_xts_negative_gap', true );

		if ( 'enabled' === $negative_gap ) {
			add_action(
				'xts_before_site_content_container',
				function () {
					add_action( 'elementor/frontend/container/before_render', 'xts_add_container_class_if_content_width', 10 );
				},
				10
			);

			add_action(
				'xts_after_site_content_container',
				function () {
					remove_action( 'elementor/frontend/container/before_render', 'xts_add_container_class_if_content_width', 10 );
				},
				10
			);
		} elseif ( 'disabled' === $negative_gap ) {
			add_action(
				'xts_before_site_content_container',
				function () {
					remove_action( 'elementor/frontend/container/before_render', 'xts_add_container_class_if_content_width', 10 );
				},
				10
			);

			add_action(
				'xts_after_site_content_container',
				function () {
					add_action( 'elementor/frontend/container/before_render', 'xts_add_container_class_if_content_width', 10 );
				},
				10
			);
		}
	}

	add_action( 'wp', 'xts_container_negative_gap' );
}

if ( ! function_exists( 'xts_add_container_class_if_content_width' ) ) {
	/**
	 * Add class to container is content with is set.
	 *
	 * @since 1.0.0
	 *
	 * @param object $widget Element.
	 */
	function xts_add_container_class_if_content_width( $widget ) {
		$settings = $widget->get_settings_for_display();

		if ( isset( $settings['content_width'] ) && 'boxed' === $settings['content_width'] && isset( $settings['boxed_width']['size'] ) && ! $settings['boxed_width']['size'] ) {
			$widget->add_render_attribute( '_wrapper', 'class', 'xts-negative-gap' );
		}
	}
}
