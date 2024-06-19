<?php
/**
 * Mobile search element template
 *
 * @package xts
 */

$extra_classes  = '';
$icon_type      = $params['icon_type'];
$extra_classes .= ' xts-display-' . $params['display'];
$icon_classes   = '';

if ( 'custom' === $icon_type ) {
	$icon_classes .= ' xts-icon-custom';
}

if ( 'form' === $params['display'] ) {
	$form_wrapper_classes  = 'xts-header-search-form-mobile';
	$form_wrapper_classes .= ' xts-scheme-' . $params['form_color_scheme'] . '-form';

	xts_search_form(
		array(
			'ajax'                => true,
			'post_type'           => $params['post_type'],
			'icon_type'           => $icon_type,
			'search_style'        => isset( $params['search_style'] ) ? $params['search_style'] : 'default',
			'custom_icon'         => $params['custom_icon'],
			'wrapper_classes'     => $form_wrapper_classes,
			'categories_dropdown' => isset( $params['categories_dropdown'] ) && $params['categories_dropdown'] ? 'yes' : 'no',
		)
	);

	return;
}

$extra_classes .= ' xts-style-' . $params['style'];

?>

<div class="xts-header-mobile-search xts-header-el<?php echo esc_attr( $extra_classes ); ?>">
	<a href="#">
		<span class="xts-header-el-icon<?php echo esc_attr( $icon_classes ); ?>">
			<?php if ( 'custom' === $icon_type ) : ?>
				<?php echo xts_get_custom_icon( $params['custom_icon'] ); // phpcs:ignore ?>
			<?php endif; ?>
		</span>

		<span class="xts-header-el-label">
			<?php esc_html_e( 'Search', 'xts-theme' ); ?>
		</span>
	</a>
</div>
