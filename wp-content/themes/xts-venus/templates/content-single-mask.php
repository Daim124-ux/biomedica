<?php
/**
 * Template used to display post content on single pages.
 *
 * @package xts
 */

$has_post_thumbnail = xts_has_post_thumbnail( get_the_ID(), true );
$thumb_classes      = '';

if ( $has_post_thumbnail && ( ! get_post_format() || 'gallery' === get_post_format() || 'image' === get_post_format() ) ) {
	$thumb_classes .= ' xts-scheme-light';
}
?>

<article id="post-<?php the_ID(); ?>" <?php post_class( xts_get_single_post_classes() ); ?>>

	<div class="xts-single-post-thumb<?php echo esc_attr( $thumb_classes ); ?>">
		<div class="xts-single-post-header-wrap">
			<?php if ( ! xts_get_opt( 'page_title_show_current_post_title' ) ) : ?>
				<h1 class="xts-single-post-title xts-entities-title">
					<?php the_title(); ?>
				</h1>
			<?php endif; ?>

			<div class="xts-single-post-header">
				<div class="xts-post-meta">
					<?php xts_meta_post_author(); ?>
					<?php xts_meta_post_date(); ?>
				</div>

				<?php xts_meta_post_categories(); ?>
			</div>
		</div>

		<?php if ( $has_post_thumbnail ) : ?>
			<?php xts_single_post_thumbnail(); ?>
		<?php endif; ?>
	</div>

	<?php if ( xts_get_opt( 'blog_single_content_boxed' ) ) : ?>
		<div class="xts-single-post-boxed">
	<?php endif; ?>

	<div class="xts-single-post-content">
		<?php the_content(); ?>
		<?php wp_link_pages(); ?>
	</div>

	<?php if ( get_the_tag_list() || xts_get_opt( 'blog_single_share_buttons' ) ) : ?>
		<footer class="xts-single-post-footer">
			<?php if ( get_the_tag_list() ) : ?>
				<div class="xts-tags-list">
					<?php the_tags( '', ' ' ); ?>
				</div>
			<?php endif; ?>

			<?php if ( xts_get_opt( 'blog_single_share_buttons' ) ) : ?>
				<?php xts_social_buttons_template( xts_get_default_value( 'single_post_social_buttons_args' ) ); ?>
			<?php endif; ?>
		</footer>
	<?php endif; ?>

	<?php if ( get_the_author_meta( 'description' ) && xts_get_opt( 'blog_single_author_bio' ) ) : ?>
		<?php xts_author_bio(); ?>
	<?php endif; ?>

	<?php if ( xts_get_opt( 'blog_single_navigation' ) ) : ?>
		<?php xts_get_template_part( 'templates/single-posts-navigation' ); ?>
	<?php endif; ?>

	<?php if ( xts_get_opt( 'blog_single_content_boxed' ) ) : ?>
		</div>
	<?php endif; ?>
</article>

<?php if ( xts_get_opt( 'blog_single_content_boxed' ) ) : ?>
	<div class="xts-single-post-boxed">
<?php endif; ?>

<?php if ( xts_get_opt( 'blog_single_related_posts' ) ) : ?>
	<?php xts_get_related_posts( $post ); ?>
<?php endif; ?>

<?php if ( comments_open() || get_comments_number() ) : ?>
	<?php comments_template(); ?>
<?php endif; ?>

<?php if ( xts_get_opt( 'blog_single_content_boxed' ) ) : ?>
	</div>
<?php endif; ?>