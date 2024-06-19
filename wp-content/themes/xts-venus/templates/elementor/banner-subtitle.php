<?php
/**
 * Banner subtitle template function
 *
 * @package xts
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

$subtitle_attrs = '';

if ( 'image' === $banner['subtitle_color_presets'] && $banner['subtitle_image_color']['id'] ) {
	$subtitle_attrs .= ' style="background-image: url(' . xts_get_image_url( $banner['subtitle_image_color']['id'], 'subtitle_image_color', $banner ) . ');"';
}

?>

<div class="xts-iimage-subtitle<?php echo esc_attr( $subtitle_classes ); ?>" data-elementor-setting-key="<?php echo esc_attr( $inline_editing_key ); ?>subtitle"<?php echo wp_kses( $subtitle_attrs, true ); // phpcs:ignore ?>>
	<?php echo wp_kses( $banner['subtitle'], xts_get_allowed_html() ); ?>
</div>