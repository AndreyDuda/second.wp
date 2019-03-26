<?php
/**
 * Template Name: Shop Front Page
 *
 * Description: A page template that provides a key component of WordPress as a CMS
 * by meeting the need for a carefully crafted introductory page. The front page template
 * in Twenty Twelve consists of a page content area for adding text, images, video --
 * anything you'd like -- followed by front-page-only widgets in one or two columns.
 *
 * @package DesignPromote
 * @subpackage Shop
 * @since Shop 1.0
 */

get_header(); ?>

	<div id="primary" class="site-content">
		<div id="content" role="main">
			<?php //$front_page = get_option( 'page_on_front' ); ?>
			<?php //query_posts( array( 'post__not_in' => array( $front_page ), 'post_type' => 'page', 'order' => 'ASC' ) ); //hide the front page //query_posts() uses more loading time, may use get_posts or WP_Query ?>
			<?php query_posts( array( 'post_type' => 'page', 'order' => 'ASC' ) ); ?>
			<?php while ( have_posts() ) : the_post(); ?>
				<?php get_template_part( 'content', 'shop' ); ?>
			<?php endwhile; // end of the loop. ?>
		</div><!-- #content -->
	</div><!-- #primary -->

<?php get_sidebar( 'front' ); ?>
<?php get_footer(); ?>