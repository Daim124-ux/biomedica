<?php

if ( ! defined( 'WP_ROCKET_VERSION' ) ) {
	return;
}

if ( ! function_exists( 'xts_remove_elementor_css_from_exclude' ) ) {
	/**
	 * Remove from CSS exclude Elementor post file.
	 *
	 * @param array $excluded_files Excluded files.
	 *
	 * @return array
	 */
	function xts_remove_elementor_css_from_exclude( $excluded_files ) {
		$upload   = wp_get_upload_dir();
		$basepath = wp_parse_url( $upload['baseurl'], PHP_URL_PATH );

		if ( empty( $basepath ) ) {
			return $excluded_files;
		}

		$key = array_search( $basepath . '/elementor/css/(.*).css', $excluded_files, true );

		if ( false !== $key ) {
			unset( $excluded_files[ $key ] );
		}

		return $excluded_files;
	}

	add_action( 'rocket_exclude_css', 'xts_remove_elementor_css_from_exclude' );
}

if ( ! function_exists( 'xts_delay_js_exclusions' ) ) {
	/**
	 * Exclusions JS files.
	 *
	 * @param array $exclude_delay_js Exclude files.
	 * @return array
	 */
	function xts_delay_js_exclusions( $exclude_delay_js ) {
		if ( ! xts_get_opt( 'rocket_delay_js_exclusions', false ) ) {
			return $exclude_delay_js;
		}

		return wp_parse_args(
			$exclude_delay_js,
			array(
				'/jquery-?[0-9.](.*)(.min|.slim|.slim.min)?.js',
				'helpers',
				'clickOnScrollButton',
				'menuOffsets',
				'menuDropdownsAJAX',
				'offCanvasCartWidget',
				'offCanvasMyAccount',
				'mobileNavigation',
				'menuClickEvent',
				'menuStickyOffsets',
				'cart-fragments',
				'swiper',
				'singleProductGallery',
				'js.cookie',
				'imagesLoaded',
				'ageVerify',
				'magnific-popup',
			)
		);
	}

	add_filter( 'rocket_delay_js_exclusions', 'xts_delay_js_exclusions' );
}

if ( ! function_exists( 'xts_rejected_uri_exclusions' ) ) {
	/**
	 * Add uris to the wp_rocket rejected uri.
	 *
	 * @param array $uris List of rejected uri.
	 */
	function xts_rejected_uri_exclusions( $uris ) {
		$urls = array();

		if ( xts_get_opt( 'wishlist' ) && xts_get_opt( 'wishlist_page' ) ) {
			$urls[] = xts_get_whishlist_page_url();
		}
		if ( xts_get_opt( 'compare' ) && xts_get_opt( 'compare_page' ) ) {
			$urls[] = xts_get_compare_page_url();
		}

		if ( $urls ) {
			foreach ( $urls as $url ) {
				$uri = str_replace( home_url(), '', $url ) . '(.*)';

				if ( ! empty( $uri ) ) {
					$uris[] = $uri;
				}
			}
		}

		return $uris;
	}

	add_filter( 'rocket_cache_reject_uri', 'xts_rejected_uri_exclusions' );
}
