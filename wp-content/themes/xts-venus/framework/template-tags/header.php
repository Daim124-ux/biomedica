<?php
/**
 * Header templates functions
 *
 * @package xts
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

if ( ! function_exists( 'xts_page_top_part' ) ) {
	/**
	 * Generate page top part
	 *
	 * @since 1.0.0
	 */
	function xts_page_top_part() {
		$sidebar_classes = '';

		if ( is_singular( 'post' ) || xts_is_blog_archive() ) {
			if ( xts_get_opt( 'blog_offcanvas_sidebar_desktop' ) ) {
				$sidebar_classes .= ' xts-sidebar-hidden-lg';
			}

			if ( xts_get_opt( 'blog_offcanvas_sidebar_mobile' ) ) {
				$sidebar_classes .= ' xts-sidebar-hidden-md';
			}
		} elseif ( xts_is_shop_archive() ) {
			if ( xts_get_opt( 'shop_offcanvas_sidebar_desktop' ) ) {
				$sidebar_classes .= ' xts-sidebar-hidden-lg';
			}

			if ( xts_get_opt( 'shop_offcanvas_sidebar_mobile' ) ) {
				$sidebar_classes .= ' xts-sidebar-hidden-md';
			}
		} elseif ( is_singular( 'product' ) || is_singular( 'xts-template' ) ) {
			if ( xts_get_opt( 'single_product_offcanvas_sidebar_desktop' ) ) {
				$sidebar_classes .= ' xts-sidebar-hidden-lg';
			}

			if ( xts_get_opt( 'single_product_offcanvas_sidebar_mobile' ) ) {
				$sidebar_classes .= ' xts-sidebar-hidden-md';
			}
		} else {
			if ( xts_get_opt( 'offcanvas_sidebar_desktop' ) ) {
				$sidebar_classes .= ' xts-sidebar-hidden-lg';
			}

			if ( xts_get_opt( 'offcanvas_sidebar_mobile' ) ) {
				$sidebar_classes .= ' xts-sidebar-hidden-md';
			}
		}

		?>
		<?php if ( ! xts_is_ajax() ) : ?>
			<div class="xts-site-content">
		<?php elseif ( xts_is_ajax() ) : ?>
			<title><?php wp_title(); ?></title>

			<?php if ( xts_get_document_description() ) : ?>
				<meta name="description" content="<?php echo esc_attr( xts_get_document_description() ); ?>" />
			<?php endif; ?>
		<?php endif ?>

		<?php do_action( 'xts_before_site_content_container' ); ?>

		<div class="<?php echo esc_attr( xts_get_site_content_container_classes( get_the_ID() ) ); ?>">
			<div class="row row-spacing-40<?php echo esc_attr( $sidebar_classes ); ?>">
		<?php
	}
}

if ( ! function_exists( 'xts_mobile_menu' ) ) {
	/**
	 * Generate mobile menu
	 *
	 * @since 1.0.0
	 */
	function xts_mobile_menu() {
		$menu_locations = get_nav_menu_locations();
		$location       = 'main-menu';
		$menu_link      = get_admin_url( null, 'nav-menus.php' );
		$search_args    = apply_filters(
			'xts_mobile_menu_search_default_args',
			array(
				'search_style' => 'icon-alt',
				'location'     => 'mobile',
				'ajax'         => true,
			)
		);
		$settings       = xts_get_header_settings();

		if ( isset( $settings['search'] ) ) {
			$search_args['post_type'] = $settings['search']['post_type'];
			$search_args['ajax']      = isset( $settings['search']['ajax'] ) ? $settings['search']['ajax'] : true;
		}

		$search_args['post_type'] = isset( $settings['burger']['post_type'] ) ? $settings['burger']['post_type'] : 'post';

		if ( ! isset( $settings['burger'] ) ) {
			return;
		}

		$search_form            = isset( $settings['burger']['search_form'] ) ? $settings['burger']['search_form'] : true;
		$show_html_block        = isset( $settings['burger']['show_html_block'] ) ? $settings['burger']['show_html_block'] : false;
		$wrapper_classes        = '';
		$extra_menu_classes     = '';
		$position               = $settings['burger']['position'];
		$color_scheme           = isset( $settings['burger']['color_scheme'] ) ? $settings['burger']['color_scheme'] : '';
		$mobile_categories      = isset( $settings['burger']['categories_menu'] ) ? $settings['burger']['categories_menu'] : false;
		$mobile_categories_menu = isset( $settings['burger']['cat_menu_id'] ) && $mobile_categories ? $settings['burger']['cat_menu_id'] : '';
		$primary_menu_title     = isset( $settings['burger']['primary_menu_title'] ) && $settings['burger']['primary_menu_title'] ? $settings['burger']['primary_menu_title'] : esc_html__( 'Menu', 'xts-theme' );
		$secondary_menu_title   = isset( $settings['burger']['secondary_menu_title'] ) && $settings['burger']['secondary_menu_title'] ? $settings['burger']['secondary_menu_title'] : esc_html__( 'Categories', 'xts-theme' );

		$pages_active      = ' xts-active';
		$categories_active = '';
		$tab_classes       = '';

		$wrapper_classes .= ' xts-side-' . $position;
		if ( 'dark' !== $color_scheme && $color_scheme ) {
			$wrapper_classes .= ' xts-scheme-' . $color_scheme;
			$wrapper_classes .= ' xts-widget-scheme-' . $color_scheme;
		}
 
		if ( $show_html_block ) {
			$extra_menu_classes .= ' xts-html-block-on';
		}

		xts_enqueue_js_script( 'menu-mobile' );

		echo '<div class="xts-side-mobile xts-side-hidden' . esc_attr( $wrapper_classes ) . '">';

		if ( $search_form ) {
			xts_search_form( $search_args );
		}

		if ( $mobile_categories ) {

			if ( isset( $settings['burger']['tabs_swap'] ) && $settings['burger']['tabs_swap'] ) {
				$pages_active      = '';
				$categories_active = ' xts-active';
				$tab_classes      .= ' xts-swap';
			}

			?>
			<ul class="xts-nav xts-nav-mobile-tab xts-style-underline<?php echo esc_attr( $tab_classes ); ?>">
				<li class="xts-tabs-pages<?php echo esc_attr( $pages_active ); ?>" data-menu="pages">
					<a href="#" rel="nofollow noopener">
						<span class="nav-link-text">
							<?php echo esc_html( $primary_menu_title ); ?>
						</span>
					</a>
				</li>
				<li class="xts-tabs-categories<?php echo esc_attr( $categories_active ); ?>" data-menu="categories">
					<a href="#" rel="nofollow noopener">
						<span class="nav-link-text">
							<?php echo esc_html( $secondary_menu_title ); ?>
						</span>
					</a>
				</li>
			</ul>
			<?php
			if ( ! empty( $mobile_categories_menu ) ) :
				?>
				<?php

				wp_nav_menu(
					array(
						'container'  => '',
						'menu'       => $mobile_categories_menu,
						'menu_class' => 'menu xts-nav xts-nav-mobile xts-direction-v xts-mobile-categories ' . $categories_active,
						'walker'     => new XTS\Module\Mega_Menu\Walker( 'default' ),
					)
				);
				?>
			<?php else : ?>
				<div class="create-nav-msg"><?php esc_html_e( 'Set your categories menu in Theme Settings -> Header -> Menu -> Mobile menu (categories)', 'xts-theme' ); ?></div>
			<?php endif; ?>
			<?php
		}

		if ( isset( $menu_locations['mobile-menu'] ) && 0 !== $menu_locations['mobile-menu'] ) {
			$location = 'mobile-menu';
		}

		if ( has_nav_menu( $location ) ) {
			wp_nav_menu(
				array(
					'theme_location' => $location,
					'container'      => '',
					'menu_class'     => 'menu xts-nav xts-nav-mobile xts-direction-v xts-mobile-pages' . $extra_menu_classes . $pages_active,
					'walker'         => new XTS\Module\Mega_Menu\Walker( 'default' ),
				)
			);
		} elseif ( current_user_can( 'administrator' ) ) {
			?>
			<div class="xts-nav-msg">
			<?php
				printf(
					wp_kses(
						/* translators: 1: menu settings link */
						__( 'Create your first <a href="%s"><strong>navigation menu here</strong></a>', 'xts-theme' ),
						'default'
					),
					esc_attr( $menu_link )
				);
			?>
			</div>
			<?php
		}

		?>

		<?php if ( is_active_sidebar( 'mobile-menu-widget-sidebar' ) ) : ?>
			<div class="xts-widgetarea-mobile">
				<?php dynamic_sidebar( 'mobile-menu-widget-sidebar' ); ?>	
			</div>
		<?php endif; ?>

		<?php

		echo '</div>';
	}


	add_action( 'xts_after_site_wrapper', 'xts_mobile_menu', 130 );
}
