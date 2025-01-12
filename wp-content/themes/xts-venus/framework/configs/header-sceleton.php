<?php
/**
 * Default header builder structure
 *
 * @package xts
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

$sceleton_structure = array(
	'id'      => 'root',
	'type'    => 'root',
	'title'   => 'Header builder',
	'content' => array(
		'top-bar'        => array(
			'id'      => 'top-bar',
			'type'    => 'row',
			'title'   => 'Top bar',
			'content' => array(
				'column5'        => array(
					'id'           => 'column5',
					'type'         => 'column',
					'title'        => 'Mega column',
					'class'        => 'xts-start' . ' xts-desktop',
					'desktop_only' => true,
				),
				'column6'        => array(
					'id'           => 'column6',
					'type'         => 'column',
					'title'        => 'Mega column',
					'class'        => 'xts-center xts-desktop',
					'desktop_only' => true,
				),
				'column7'        => array(
					'id'           => 'column7',
					'type'         => 'column',
					'title'        => 'Mega column',
					'class'        => 'xts-end xts-desktop',
					'desktop_only' => true,
				),
				'column_mobile1' => array(
					'id'          => 'column_mobile1',
					'type'        => 'column',
					'title'       => 'Mobile column',
					'class'       => 'xts-full xts-mobile',
					'mobile_only' => true,
				),
			),
		),
		'general-header' => array(
			'id'      => 'general-header',
			'type'    => 'row',
			'title'   => 'Main header',
			'content' => array(
				'column8'        => array(
					'id'           => 'column8',
					'type'         => 'column',
					'title'        => 'Mega column',
					'class'        => 'xts-start xts-desktop',
					'desktop_only' => true,
				),
				'column9'        => array(
					'id'           => 'column9',
					'type'         => 'column',
					'title'        => 'Mega column',
					'class'        => 'xts-center xts-desktop',
					'desktop_only' => true,
				),
				'column10'       => array(
					'id'           => 'column10',
					'type'         => 'column',
					'title'        => 'Mega column',
					'class'        => 'xts-end xts-desktop',
					'desktop_only' => true,
				),
				'column_mobile2' => array(
					'id'          => 'column_mobile2',
					'type'        => 'column',
					'title'       => 'Mobile column',
					'class'       => 'xts-start xts-mobile',
					'mobile_only' => true,
				),
				'column_mobile3' => array(
					'id'          => 'column_mobile3',
					'type'        => 'column',
					'title'       => 'Mobile column',
					'class'       => 'xts-center xts-mobile',
					'mobile_only' => true,
				),
				'column_mobile4' => array(
					'id'          => 'column_mobile4',
					'type'        => 'column',
					'title'       => 'Mobile column',
					'class'       => 'xts-end xts-mobile',
					'mobile_only' => true,
				),
			),
		),
		'header-bottom'  => array(
			'id'      => 'header-bottom',
			'type'    => 'row',
			'title'   => 'Header bottom',
			'content' => array(
				'column11'       => array(
					'id'           => 'column11',
					'type'         => 'column',
					'title'        => 'Mega column',
					'class'        => 'xts-start xts-desktop',
					'desktop_only' => true,
				),
				'column12'       => array(
					'id'           => 'column12',
					'type'         => 'column',
					'title'        => 'Mega column',
					'class'        => 'xts-center xts-desktop',
					'desktop_only' => true,
				),
				'column13'       => array(
					'id'           => 'column13',
					'type'         => 'column',
					'title'        => 'Mega column',
					'class'        => 'xts-end xts-desktop',
					'desktop_only' => true,
				),
				'column_mobile5' => array(
					'id'          => 'column_mobile5',
					'type'        => 'column',
					'title'       => 'Mobile column',
					'class'       => 'xts-full xts-mobile',
					'mobile_only' => true,
				),
			),
		),
	),
);

return apply_filters( 'xts_header_sceleton', $sceleton_structure );
