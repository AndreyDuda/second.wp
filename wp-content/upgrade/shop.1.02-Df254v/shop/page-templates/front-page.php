<?php
/**
 * Template Name: Front Page Template
 *
 * Description: A page template that provides a key component of WordPress as a CMS
 * by meeting the need for a carefully crafted introductory page. The front page template
 * in Twenty Twelve consists of a page content area for adding text, images, video --
 * anything you'd like -- followed by front-page-only widgets in one or two columns.
 *
 * @package WordPress
 * @subpackage Twenty_Twelve
 * @since Twenty Twelve 1.0
 */

get_header(); ?>

	<div id="primary" class="site-content">
		<div id="content" role="main">
			<?php //query_posts( array( 'showposts' => 10, 'post_type' => 'page' ) ); //more loading time, may use get_posts or WP_Query ?>
			<?php //while ( have_posts() ) : the_post(); ?>
				<?php //if ( has_post_thumbnail() ) : ?>
					<!--div class="entry-page-image"-->
						<?php //the_post_thumbnail(); ?>
					<!--/div--><!-- .entry-page-image -->
				<?php //endif; ?>
			<?php //endwhile; // end of the loop. ?>

			<?php
			//use get_pages to improve loading speed to query_posts()
			$pages = get_pages();
			foreach( $pages as $page ) : 
				get_template_part( 'content', 'home' );
			endforeach; 
			?>
		</div><!-- #content -->
	</div><!-- #primary -->

<?php get_sidebar( 'front' ); ?>
<?php get_footer(); ?>