<?php
/**
 * Footer config function
 *
 * @version 1.0
 * @package xts
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

return apply_filters(
	'xts_footer_configs_array',
	array(
		17 => array(
			'cols' => array(
				'col-12 col-sm-4 col-lg-2',
				'col-12 col-sm-4 col-lg-2',
				'col-12 col-sm-4 col-lg-2',
				'col-12 col-sm-4 col-lg-2',
				'col-12 col-sm-6 col-lg-4',
			),
		),
	)
);
