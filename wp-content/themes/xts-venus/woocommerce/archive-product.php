<?php
/**
 * The Template for displaying product archives, including the main shop page which is a post type archive
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/archive-product.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 3.4.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

if ( 'fragments' === xts_is_ajax() ) {
	xts_get_product_main_loop( true );
	die();
}

$category_description_position = xts_get_opt( 'category_description_position' );

?>

<?php if ( ! xts_is_ajax() ) : ?>
	<?php get_header( 'shop' ); ?>
<?php else : ?>
	<?php xts_page_top_part(); ?>
<?php endif; ?>

<?php if ( 'sidebar-left' === xts_get_page_layout() ) : ?>
	<?php
	/**
	 * Hook: woocommerce_sidebar.
	 *
	 * @hooked woocommerce_get_sidebar - 10
	 */
	do_action( 'woocommerce_sidebar' );
	?>
<?php endif; ?>

<?php
/**
 * Hook: woocommerce_before_main_content.
 *
 * @hooked woocommerce_output_content_wrapper - 10 (outputs opening divs for the content)
 * @hooked woocommerce_breadcrumb - 20
 * @hooked WC_Structured_Data::generate_website_data() - 30
 */
do_action( 'woocommerce_before_main_content' );
?>

<?php if ( 'before' === $category_description_position && is_product_taxonomy() ) : ?>
	<?php
	/**
	 * Hook: woocommerce_archive_description.
	 *
	 * @hooked woocommerce_taxonomy_archive_description - 10
	 * @hooked woocommerce_product_archive_description - 10
	 */
	do_action( 'woocommerce_archive_description' );
	?>
<?php endif; ?>

<?php do_action( 'xts_before_products_loop_head' ); ?>

<?php do_action( 'xts_shop_filters_area' ); ?>

<div class="xts-active-filters"><?php do_action( 'xts_active_product_filters' ); // Must be in one line. ?></div>

<?php if ( xts_get_opt( 'ajax_shop' ) ) : ?>
	<?php xts_enqueue_js_script( 'sticky-loader-position' ); ?>
	<div class="xts-shop-loader xts-sticky-loader">
		<span class="xts-loader"></span>
	</div>
<?php endif; ?>

<?php do_action( 'xts_product_main_loop' ); ?>

<?php if ( 'after' === $category_description_position && is_product_taxonomy() ) : ?>
	<?php
	/**
	 * Hook: woocommerce_archive_description.
	 *
	 * @hooked woocommerce_taxonomy_archive_description - 10
	 * @hooked woocommerce_product_archive_description - 10
	 */
	do_action( 'woocommerce_archive_description' );
	?>
<?php endif; ?>

<?php
/**
 * Hook: woocommerce_after_main_content.
 *
 * @hooked woocommerce_output_content_wrapper_end - 10 (outputs closing divs for the content)
 */
do_action( 'woocommerce_after_main_content' );
?>

<?php if ( 'sidebar-right' === xts_get_page_layout() ) : ?>
	<?php
	/**
	 * Hook: woocommerce_sidebar.
	 *
	 * @hooked woocommerce_get_sidebar - 10
	 */
	do_action( 'woocommerce_sidebar' );
	?>
<?php endif; ?>

<?php if ( ! xts_is_ajax() ) : ?>
	<?php get_footer( 'shop' ); ?>
<?php else : ?>
	<?php xts_page_bottom_part(); ?>
<?php endif; ?>
