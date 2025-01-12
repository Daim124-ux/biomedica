<?php
/**
 * Progress bar template
 *
 * @package xts
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit( 'No direct script access allowed' );
}

$wrapper_classes = '';

$wrapper_classes .= ' xts-stock-progress-bar';

if ( $extra_classes ) {
	$wrapper_classes .= ' ' . $extra_classes;
}

?>

<div class="xts-progress-bar<?php echo esc_attr( $wrapper_classes ); ?>">
	<div class="xts-spb-info row row-spacing-10">
		<div class="col-auto">
			<?php esc_html_e( 'Ordered:', 'xts-theme' ); ?>
			<span>
				<?php echo esc_html( $data['total_sold'] ); ?>
			</span>
		</div>

		<div class="col-auto">
			<?php esc_html_e( 'Items available:', 'xts-theme' ); ?>
			<span>
				<?php echo esc_html( $data['current_stock'] ); ?>
			</span>
		</div>
	</div>

	<div class="xts-progress-line" title="<?php echo esc_attr__( 'Sold:', 'xts-theme' ) . ' ' . esc_html( $data['percentage'] ); ?>%">
		<div class="xts-progress-track" style="width:<?php echo esc_html( $data['percentage'] ); ?>%"></div>
	</div>
</div>
