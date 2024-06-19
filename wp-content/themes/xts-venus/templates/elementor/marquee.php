<?php
/**
 * Marquee template function
 *
 * @package xts
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

if ( ! function_exists( 'xts_marquee_template' ) ) {
	/**
	 * Marquee template
	 *
	 * @since 1.6.0
	 *
	 * @param array  $element_args Associative array of arguments.
	 * @param object $marquee Marquee Object.
	 */
	function xts_marquee_template( $element_args, $marquee ) {

		$marquee->add_render_attribute(
			array(
				'wrapper' => array(
					'class' => array(
						'xts-marquee',
						'yes' === $element_args['paused_on_hover'] ? 'xts-with-pause' : '',
					),
				),
			)
		);

		$icon_output = '';

		$custom_image_size = ! empty( $element_args['icon_image_custom_dimension']['width'] ) ? $element_args['icon_image_custom_dimension'] : array(
			'width'  => 128,
			'height' => 128,
		);

		if ( 'image' === $element_args['icon_type'] && ! empty( $element_args['icon_image']['id'] ) ) {
			$icon_output = xts_get_image_html( $element_args, 'icon_image' );

			if ( xts_is_svg( $element_args['icon_image']['url'] ) ) {
				if ( 'custom' === $element_args['icon_image_size'] && ! empty( $element_args['icon_image_custom_dimension'] ) ) {
					$icon_output = '<div class="xts-image-type-svg" style="width:' . esc_attr( $element_args['icon_image_custom_dimension']['width'] ) . 'px; height:' . esc_attr( $element_args['icon_image_custom_dimension']['height'] ) . 'px;">' . xts_get_svg( '', '', xts_get_image_url( $element_args['icon_image']['id'], 'icon_image', $element_args ) ) . '</div>';
				}
			}
		} elseif ( 'icon' === $element_args['icon_type'] && ! empty( $element_args['icon'] ) ) {
			$icon_output = xts_elementor_get_render_icon( $element_args['icon'] );
		}

		$content_html = '';

		foreach ( $element_args['marquee_contents'] as $index => $item ) {
			$item_icon_output        = $icon_output;
			$item['icon_image_size'] = ! empty( $item['icon_image_size'] ) ? $item['icon_image_size'] : 'thumbnail';
			$item['link']['class']   = 'xts-fill';
			$link_attrs              = '';

			if ( isset( $item['link'] ) ) {
				$link_attrs = xts_get_link_attrs( $item['link'] );
			}

			if ( empty( $item['icon_image_custom_dimension']['width'] ) ) {
				$item['icon_image_custom_dimension'] = $custom_image_size;
			}

			if ( 'image' === $item['icon_type'] && ! empty( $item['icon_image']['id'] ) ) {
				$item_icon_output = xts_get_image_html( $item, 'icon_image' );

				if ( xts_is_svg( $item['icon_image']['url'] ) ) {
					if ( 'custom' === $item['icon_image_size'] && ! empty( $item['icon_image_custom_dimension'] ) ) {
						$icon_output_size = $item['icon_image_custom_dimension'];
						$item_icon_output = '<div class="xts-image-type-svg" style="width:' . esc_attr( $icon_output_size['width'] ) . 'px; height:' . esc_attr( $icon_output_size['height'] ) . 'px;">' . xts_get_svg( '', '', xts_get_image_url( $item['icon_image']['id'], 'icon_image', $element_args ) ) . '</div>';
					} else {
						$icon_output_size = $item['icon_image_size'];
						$thumb_size       = xts_get_image_size( $icon_output_size );
						if ( $thumb_size ) {
							$item_icon_output = '<div class="xts-image-type-svg" style="width:' . esc_attr( $thumb_size[0] ) . 'px; height:' . esc_attr( $thumb_size[1] ) . 'px;">' . xts_get_svg( '', '', xts_get_image_url( $item['icon_image']['id'], 'icon_image', $element_args ) ) . '</div>';
						}
					}
				}
			}

			ob_start();

			?>
			<span>
				<span class="xts-icon">
					<?php echo ! empty( $item_icon_output ) ? $item_icon_output : $icon_output; // phpcs:ignore.?>	
				</span>

				<?php if ( ! empty( $item['text'] ) ) : ?>
					<?php echo do_shortcode( shortcode_unautop( $item['text'] ) ); ?>
				<?php endif; ?>

				<?php if ( isset( $item['link']['url'] ) && $item['link']['url'] ) : ?>
					<a <?php echo $link_attrs; // phpcs:ignore. ?> aria-label="<?php esc_attr_e( 'marquee item link', 'xts-theme' ); ?>"></a>
				<?php endif; ?>
			</span>
			<?php
			$content_html .= ob_get_clean();
		}
		?>
		<div <?php echo $marquee->get_render_attribute_string( 'wrapper' ); // phpcs:ignore. ?>>
			<div class="xts-marquee-content">
				<?php echo $content_html; // phpcs:ignore. ?>
			</div>
			<div class="xts-marquee-content">
				<?php echo $content_html; // phpcs:ignore. ?>
			</div>
		</div>
		
		<?php
	}
}
