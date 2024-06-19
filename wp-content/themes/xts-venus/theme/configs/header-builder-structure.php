<?php
/**
 * Default header builder structure
 *
 * @package xts
 * @version 1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

return array(
	'id'      => 'root',
	'type'    => 'root',
	'content' => array(
		0 => array(
			'id'      => 'top-bar',
			'type'    => 'row',
			'content' => array(
				0 => array(
					'id'      => 'column5',
					'type'    => 'column',
					'content' => array(),
				),
				1 => array(
					'id'      => 'column6',
					'type'    => 'column',
					'content' => array(),
				),
				2 => array(
					'id'      => 'column7',
					'type'    => 'column',
					'content' => array(),
				),
				3 => array(
					'id'      => 'column_mobile1',
					'type'    => 'column',
					'content' => array(),
				),
			),
			'params'  => array(
				'flex_layout'            => array(
					'id'    => 'flex_layout',
					'value' => 'stretch-center',
					'type'  => 'selector',
				),
				'height'                 => array(
					'id'    => 'height',
					'value' => 42,
					'type'  => 'slider',
				),
				'mobile_height'          => array(
					'id'    => 'mobile_height',
					'value' => 40,
					'type'  => 'slider',
				),
				'align_dropdowns_bottom' => array(
					'id'    => 'align_dropdowns_bottom',
					'value' => false,
					'type'  => 'switcher',
				),
				'hide_desktop'           => array(
					'id'    => 'hide_desktop',
					'value' => true,
					'type'  => 'switcher',
				),
				'hide_mobile'            => array(
					'id'    => 'hide_mobile',
					'value' => true,
					'type'  => 'switcher',
				),
				'sticky'                 => array(
					'id'    => 'sticky',
					'value' => false,
					'type'  => 'switcher',
				),
				'sticky_height'          => array(
					'id'    => 'sticky_height',
					'value' => 40,
					'type'  => 'slider',
				),
				'color_scheme'           => array(
					'id'    => 'color_scheme',
					'value' => 'dark',
					'type'  => 'selector',
				),
				'shadow'                 => array(
					'id'    => 'shadow',
					'value' => false,
					'type'  => 'switcher',
				),
				'background'             => array(
					'id'    => 'background',
					'value' => array(),
					'type'  => 'bg',
				),
				'border'                 => array(
					'id'    => 'border',
					'value' => '',
					'type'  => 'border',
				),
			),
		),
		1 => array(
			'id'      => 'general-header',
			'type'    => 'row',
			'content' => array(
				0 => array(
					'id'      => 'column8',
					'type'    => 'column',
					'content' => array(
						0 => array(
							'id'     => 'u6t8q88voym0qtelvptf',
							'type'   => 'logo',
							'params' => array(
								'image'        => array(
									'id'    => 'image',
									'value' => array(
										'id'     => 1331,
										'url'    => 'https://space.xtemos.com/dummy/venus/wp-content/uploads/sites/6/2020/11/venus-medic-logo-1.svg',
										'width'  => 0,
										'height' => 0,
									),
									'type'  => 'image',
								),
								'width'        => array(
									'id'    => 'width',
									'value' => 120,
									'type'  => 'slider',
								),
								'sticky_image' => array(
									'id'    => 'sticky_image',
									'value' => '',
									'type'  => 'image',
								),
								'sticky_width' => array(
									'id'    => 'sticky_width',
									'value' => 150,
									'type'  => 'slider',
								),
								'logo_notice'  => array(
									'id'    => 'logo_notice',
									'value' => '',
									'type'  => 'notice',
								),
							),
						),
						1 => array(
							'id'     => 'aaltepwryg9ej92ovr0v',
							'type'   => 'space',
							'params' => array(
								'direction' => array(
									'id'    => 'direction',
									'value' => 'h',
									'type'  => 'selector',
								),
								'width'     => array(
									'id'    => 'width',
									'value' => 30,
									'type'  => 'slider',
								),
								'css_class' => array(
									'id'    => 'css_class',
									'value' => '',
									'type'  => 'text',
								),
							),
						),
					),
				),
				1 => array(
					'id'      => 'column9',
					'type'    => 'column',
					'content' => array(
						0 => array(
							'id'     => 'tiueim5f5uazw1f1dm8r',
							'type'   => 'mainmenu',
							'params' => array(
								'menu_style'       => array(
									'id'    => 'menu_style',
									'value' => 'default',
									'type'  => 'selector',
								),
								'menu_full_height' => array(
									'id'    => 'menu_full_height',
									'value' => false,
									'type'  => 'switcher',
								),
								'menu_align'       => array(
									'id'    => 'menu_align',
									'value' => 'left',
									'type'  => 'selector',
								),
								'menu_items_gap'   => array(
									'id'    => 'menu_items_gap',
									'value' => 'm',
									'type'  => 'selector',
								),
							),
						),
					),
				),
				2 => array(
					'id'      => 'column10',
					'type'    => 'column',
					'content' => array(
						0 => array(
							'id'     => '73txwcpfk8az9jro41es',
							'type'   => 'button',
							'params' => array(
								'button_text'  => array(
									'id'    => 'button_text',
									'value' => 'venus@mail.com',
									'type'  => 'text',
								),
								'button_link'  => array(
									'id'    => 'button_link',
									'value' => 'mailto:venus@mail.com',
									'type'  => 'text',
								),
								'button_size'  => array(
									'id'    => 'button_size',
									'value' => 'm',
									'type'  => 'select',
								),
								'button_color' => array(
									'id'    => 'button_color',
									'value' => 'primary',
									'type'  => 'select',
								),
								'button_style' => array(
									'id'    => 'button_style',
									'value' => 'bordered',
									'type'  => 'selector',
								),
								'button_shape' => array(
									'id'    => 'button_shape',
									'value' => 'round',
									'type'  => 'selector',
								),
							),
						),
						1 => array(
							'id'     => 'np47yhbvcnhpqagzsbrj',
							'type'   => 'space',
							'params' => array(
								'direction' => array(
									'id'    => 'direction',
									'value' => 'h',
									'type'  => 'selector',
								),
								'width'     => array(
									'id'    => 'width',
									'value' => 10,
									'type'  => 'slider',
								),
								'css_class' => array(
									'id'    => 'css_class',
									'value' => '',
									'type'  => 'text',
								),
							),
						),
						2 => array(
							'id'     => '4fwb5bn0xsbnctu4b53n',
							'type'   => 'button',
							'params' => array(
								'button_text'  => array(
									'id'    => 'button_text',
									'value' => '+41 44 257 11 20',
									'type'  => 'text',
								),
								'button_link'  => array(
									'id'    => 'button_link',
									'value' => 'tel:+41 44 257 11 20',
									'type'  => 'text',
								),
								'button_size'  => array(
									'id'    => 'button_size',
									'value' => 'm',
									'type'  => 'select',
								),
								'button_color' => array(
									'id'    => 'button_color',
									'value' => 'primary',
									'type'  => 'select',
								),
								'button_style' => array(
									'id'    => 'button_style',
									'value' => 'default',
									'type'  => 'selector',
								),
								'button_shape' => array(
									'id'    => 'button_shape',
									'value' => 'round',
									'type'  => 'selector',
								),
							),
						),
						3 => array(
							'id'     => 'dzz74amch7ym3djozq3t',
							'type'   => 'space',
							'params' => array(
								'direction' => array(
									'id'    => 'direction',
									'value' => 'h',
									'type'  => 'selector',
								),
								'width'     => array(
									'id'    => 'width',
									'value' => 10,
									'type'  => 'slider',
								),
								'css_class' => array(
									'id'    => 'css_class',
									'value' => '',
									'type'  => 'text',
								),
							),
						),
						4 => array(
							'id'     => 'duljtjrl87kj7pmuut6b',
							'type'   => 'search',
							'params' => array(
								'display'           => array(
									'id'    => 'display',
									'value' => 'dropdown',
									'type'  => 'selector',
								),
								'search_style'      => array(
									'id'    => 'search_style',
									'value' => 'default',
									'type'  => 'selector',
								),
								'form_color_scheme' => array(
									'id'    => 'form_color_scheme',
									'value' => 'inherit',
									'type'  => 'selector',
								),
								'icon_style'        => array(
									'id'    => 'icon_style',
									'value' => 'icon-bg',
									'type'  => 'selector',
								),
								'icon_type'         => array(
									'id'    => 'icon_type',
									'value' => 'default',
									'type'  => 'selector',
								),
								'custom_icon'       => array(
									'id'    => 'custom_icon',
									'value' => '',
									'type'  => 'image',
								),
								'ajax'              => array(
									'id'    => 'ajax',
									'value' => true,
									'type'  => 'switcher',
								),
								'ajax_result_count' => array(
									'id'    => 'ajax_result_count',
									'value' => 10,
									'type'  => 'slider',
								),
								'post_type'         => array(
									'id'    => 'post_type',
									'value' => 'post',
									'type'  => 'selector',
								),
								'color_scheme'      => array(
									'id'    => 'color_scheme',
									'value' => 'dark',
									'type'  => 'selector',
								),
							),
						),
					),
				),
				3 => array(
					'id'      => 'column_mobile2',
					'type'    => 'column',
					'content' => array(
						0 => array(
							'id'     => 'wn5z894j1g5n0yp3eeuz',
							'type'   => 'burger',
							'params' => array(
								'menu_id'      => array(
									'id'    => 'menu_id',
									'value' => 'mobile-menu',
									'type'  => 'select',
								),
								'style'        => array(
									'id'    => 'style',
									'value' => 'icon',
									'type'  => 'selector',
								),
								'icon_type'    => array(
									'id'    => 'icon_type',
									'value' => 'default',
									'type'  => 'selector',
								),
								'custom_icon'  => array(
									'id'    => 'custom_icon',
									'value' => '',
									'type'  => 'image',
								),
								'position'     => array(
									'id'    => 'position',
									'value' => 'left',
									'type'  => 'selector',
								),
								'color_scheme' => array(
									'id'    => 'color_scheme',
									'value' => 'inherit',
									'type'  => 'selector',
								),
								'search_form'  => array(
									'id'    => 'search_form',
									'value' => true,
									'type'  => 'switcher',
								),
							),
						),
					),
				),
				4 => array(
					'id'      => 'column_mobile3',
					'type'    => 'column',
					'content' => array(
						0 => array(
							'id'     => 'g5z57bkgtznbk6v9pll5',
							'type'   => 'logo',
							'params' => array(
								'image'        => array(
									'id'    => 'image',
									'value' => array(
										'id'     => 1331,
										'url'    => 'https://space.xtemos.com/dummy/venus/wp-content/uploads/sites/6/2020/11/venus-medic-logo-1.svg',
										'width'  => 0,
										'height' => 0,
									),
									'type'  => 'image',
								),
								'width'        => array(
									'id'    => 'width',
									'value' => 96,
									'type'  => 'slider',
								),
								'sticky_image' => array(
									'id'    => 'sticky_image',
									'value' => '',
									'type'  => 'image',
								),
								'sticky_width' => array(
									'id'    => 'sticky_width',
									'value' => 150,
									'type'  => 'slider',
								),
								'logo_notice'  => array(
									'id'    => 'logo_notice',
									'value' => '',
									'type'  => 'notice',
								),
							),
						),
					),
				),
				5 => array(
					'id'      => 'column_mobile4',
					'type'    => 'column',
					'content' => array(
						0 => array(
							'id'     => '2q7u48qwe2ykyyw8qzws',
							'type'   => 'mobilesearch',
							'params' => array(
								'style'       => array(
									'id'    => 'style',
									'value' => 'icon',
									'type'  => 'selector',
								),
								'icon_type'   => array(
									'id'    => 'icon_type',
									'value' => 'default',
									'type'  => 'selector',
								),
								'custom_icon' => array(
									'id'    => 'custom_icon',
									'value' => '',
									'type'  => 'image',
								),
							),
						),
					),
				),
			),
			'params'  => array(
				'flex_layout'            => array(
					'id'    => 'flex_layout',
					'value' => 'stretch-center',
					'type'  => 'selector',
				),
				'height'                 => array(
					'id'    => 'height',
					'value' => 100,
					'type'  => 'slider',
				),
				'mobile_height'          => array(
					'id'    => 'mobile_height',
					'value' => 60,
					'type'  => 'slider',
				),
				'align_dropdowns_bottom' => array(
					'id'    => 'align_dropdowns_bottom',
					'value' => true,
					'type'  => 'switcher',
				),
				'hide_desktop'           => array(
					'id'    => 'hide_desktop',
					'value' => false,
					'type'  => 'switcher',
				),
				'hide_mobile'            => array(
					'id'    => 'hide_mobile',
					'value' => false,
					'type'  => 'switcher',
				),
				'sticky'                 => array(
					'id'    => 'sticky',
					'value' => true,
					'type'  => 'switcher',
				),
				'sticky_height'          => array(
					'id'    => 'sticky_height',
					'value' => 65,
					'type'  => 'slider',
				),
				'color_scheme'           => array(
					'id'    => 'color_scheme',
					'value' => 'dark',
					'type'  => 'selector',
				),
				'shadow'                 => array(
					'id'    => 'shadow',
					'value' => false,
					'type'  => 'switcher',
				),
				'background'             => array(
					'id'    => 'background',
					'value' => array(
						'background-color' => array(
							'r' => 255,
							'g' => 255,
							'b' => 255,
							'a' => 1,
						),
					),
					'type'  => 'bg',
				),
				'border'                 => array(
					'id'    => 'border',
					'value' => array(
						'width' => '0',
						'color' => array(
							'r' => 129,
							'g' => 129,
							'b' => 129,
							'a' => 0.20000000000000001,
						),
						'sides' => array(
							0 => 'top',
							1 => 'bottom',
							2 => 'left',
							3 => 'right',
						),
					),
					'type'  => 'border',
				),
			),
		),
		2 => array(
			'id'      => 'header-bottom',
			'type'    => 'row',
			'content' => array(
				0 => array(
					'id'      => 'column11',
					'type'    => 'column',
					'content' => array(),
				),
				1 => array(
					'id'      => 'column12',
					'type'    => 'column',
					'content' => array(),
				),
				2 => array(
					'id'      => 'column13',
					'type'    => 'column',
					'content' => array(),
				),
				3 => array(
					'id'      => 'column_mobile5',
					'type'    => 'column',
					'content' => array(),
				),
			),
			'params'  => array(
				'flex_layout'            => array(
					'id'    => 'flex_layout',
					'value' => 'stretch-center',
					'type'  => 'selector',
				),
				'height'                 => array(
					'id'    => 'height',
					'value' => 50,
					'type'  => 'slider',
				),
				'mobile_height'          => array(
					'id'    => 'mobile_height',
					'value' => 50,
					'type'  => 'slider',
				),
				'align_dropdowns_bottom' => array(
					'id'    => 'align_dropdowns_bottom',
					'value' => false,
					'type'  => 'switcher',
				),
				'hide_desktop'           => array(
					'id'    => 'hide_desktop',
					'value' => true,
					'type'  => 'switcher',
				),
				'hide_mobile'            => array(
					'id'    => 'hide_mobile',
					'value' => true,
					'type'  => 'switcher',
				),
				'sticky'                 => array(
					'id'    => 'sticky',
					'value' => false,
					'type'  => 'switcher',
				),
				'sticky_height'          => array(
					'id'    => 'sticky_height',
					'value' => 50,
					'type'  => 'slider',
				),
				'color_scheme'           => array(
					'id'    => 'color_scheme',
					'value' => 'dark',
					'type'  => 'selector',
				),
				'shadow'                 => array(
					'id'    => 'shadow',
					'value' => false,
					'type'  => 'switcher',
				),
				'background'             => array(
					'id'    => 'background',
					'value' => '',
					'type'  => 'bg',
				),
				'border'                 => array(
					'id'    => 'border',
					'value' => '',
					'type'  => 'border',
				),
			),
		),
	),
);
