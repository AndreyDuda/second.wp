<?php
/**
 * The template used for displaying page content in page.php
 *
 * @package WordPress
 * @subpackage Twenty_Twelve
 * @since Twenty Twelve 1.0
 */
 
	$postclass = "home-image";
	if( has_post_thumbnail() ) {
		$thumb = get_the_post_thumbnail() . '<h2 class="product-title">' . get_the_title() . '</h2>';
	} else {
		$postclass .= " no-thumbnail";
		$thumb = '<span class="no-thumbnail">' . get_the_title() . '</span>';
	}
?>

	<!--div class="product-image-box"-->
	<article id="post-<?php the_ID(); ?>" <?php post_class( $postclass ); ?>>
		<a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>">
			<?php _e( $thumb, 'shop' ); //the_post_thumbnail(); ?>
			<!--h2 class="product-title"><?php //the_title(); ?></h2-->
		</a>
	<footer class="entry-meta">
		<?php edit_post_link( __( 'Edit', 'shop' ), '<span class="edit-link">', '</span>' ); ?>
	</footer><!-- .entry-meta -->
	</article>
