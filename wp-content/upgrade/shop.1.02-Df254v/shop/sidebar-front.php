<?php
/**
 * The sidebar containing the front page widget areas.
 *
 * If no active widgets in either sidebar, they will be hidden completely.
 *
 * @package WordPress
 * @subpackage Twenty_Twelve
 * @since Twenty Twelve 1.0
 */

/*
 * The front page widget area is triggered if any of the areas
 * have widgets. So let's check that first.
 *
 * If none of the sidebars have widgets, then let's bail early.
 */
//if ( ! is_active_sidebar( 'sidebar-2' ) )
//	return;

// If we get this far, we have widgets. Let do this.
?>
<div id="secondary" class="widget-area" role="complementary">
	<div class="first front-widgets">
	<?php if( !dynamic_sidebar( 'sidebar-2' ) ) : //show informative text if no widegt ?>
		<div class="widget">
		<div class="widget-title"><?php _e( 'Shop title', 'shop' ); ?></div>
		<div class="textwidget"><img src="<?php _e( get_stylesheet_directory_uri() . '/images/shop-front-960.jpg' ); ?>" alt="front widgets image" style="vertical-align:bottom;"/></div>
		</div>
	<?php endif; ?>
	</div><!-- .first -->
</div><!-- #secondary -->