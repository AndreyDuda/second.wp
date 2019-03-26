<?php
/**
 * The template for displaying the footer.
 *
 * Contains footer content and the closing of the
 * #main and #page div elements.
 *
 * @package WordPress
 * @subpackage Twenty_Twelve
 * @since Twenty Twelve 1.0
 */
?>
	</div><!-- #main .wrapper -->
	<footer id="colophon" role="contentinfo">
		<div id="footer-widgets" class="widget-area three">
			<?php for( $i=3; $i<6; $i++ ) :
				$sidebar = 'sidebar-' . $i;
				$footer_widget = 'Footer Widget ' . ( $i-2 );
				if (!dynamic_sidebar($sidebar)) : //show informative text if no widegt?>
					<div class="widget">
					<div class="widget-title"><h3><?php _e( $footer_widget ); ?></h3></div>
					<div class="textwidget"><?php _e( 'Footer Widget ' . ($i-2) . ' : to edit please go to Appearance > Widgets. Title is also manageable from widgets as well.'); ?></div>
			
					</div><!-- end of .widget-wrapper -->
				<?php endif; //end of home-widget-1 ?>
			<?php endfor; ?>
		</div><!-- #footer-widgets -->	

		<div class="site-info" style="clear:both;">
			<?php do_action( 'zn_shop_credits' ); ?>
			<a href="<?php echo esc_url( __( 'http://wordpress.org/', 'shop' ) ); ?>" title="<?php esc_attr_e( 'Semantic Personal Publishing Platform', 'shop' ); ?>"><?php printf( __( 'Proudly powered by %s', 'shop' ), 'WordPress' ); ?></a>
		</div><!-- .site-info -->
	</footer><!-- #colophon -->
</div><!-- #page -->

<?php wp_footer(); ?>
</body>
</html>