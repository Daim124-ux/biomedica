<?php
/**
 * Product price table.
 *
 * @package xts
 */

namespace XTS\Modules\Layouts;

use Elementor\Group_Control_Typography;
use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Plugin;
use XTS\Modules\Dynamic_Discounts\Frontend as Dynamic_Discounts_Module;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

/**
 * Elementor widget that inserts an embeddable content into the page, from any given URL.
 */
class Price_Table extends Widget_Base {
	/**
	 * Get widget name.
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'xts_single_product_dynamic_discounts_table';
	}

	/**
	 * Get widget title.
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return esc_html__( 'Dynamic discounts table', 'xts-theme' );
	}

	/**
	 * Get widget icon.
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'xf-woo-el-dynamic-discounts';
	}

	/**
	 * Show in panel.
	 *
	 * Whether to show the widget in the panel or not. By default returns true.
	 *
	 * @return bool Whether to show the widget in the panel or not.
	 */
	public function show_in_panel() {
		return xts_get_opt( 'dynamic_discounts_enabled' );
	}

	/**
	 * Get widget categories.
	 *
	 * @return array Widget categories.
	 */
	public function get_categories() {
		return array( 'xts-single-product-elements' );
	}

	/**
	 * Register the widget controls.
	 */
	protected function register_controls() {
		/**
		 * General settings.
		 */
		$this->start_controls_section(
			'general_style_section',
			array(
				'label' => esc_html__( 'General', 'xts-theme' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'price_color',
			array(
				'label'     => esc_html__( 'Price color', 'xts-theme' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .xts-dynamic-discounts .amount' => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'price_typography',
				'label'    => esc_html__( 'Price typography', 'xts-theme' ),
				'selector' => '{{WRAPPER}} .xts-dynamic-discounts .amount',
			)
		);

		$this->add_control(
			'discount_color',
			array(
				'label'     => esc_html__( 'Discount color', 'xts-theme' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .xts-dynamic-discounts tr td:last-child' => 'color: {{VALUE}}',
				),
				'separator' => 'before',
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'discount_typography',
				'label'    => esc_html__( 'Discount typography', 'xts-theme' ),
				'selector' => '{{WRAPPER}} .xts-dynamic-discounts tr td:last-child',
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Render the widget output on the frontend.
	 */
	protected function render() {
		if ( ! xts_is_woocommerce_installed() ) {
			return;
		}

		global $post;

		if ( ! is_singular( 'product' ) ) {
			$post = xts_get_preview_product(); // phpcs:ignore
			setup_postdata( $post );
		}

		echo Dynamic_Discounts_Module::get_instance()->render_dynamic_discounts_table(); // phpcs:ignore.

		if ( ! is_singular( 'product' ) ) {
			wp_reset_postdata();
		}
	}
}

Plugin::instance()->widgets_manager->register( new Price_Table() );
