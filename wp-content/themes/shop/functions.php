<?php

function get_navigation() {
	$templates = array();
	$templates[] = 'navigation.php';

	locate_template($templates, true);
}

function shop_styles_scripts() {
	wp_enqueue_style('shop-bootstrapcss', get_template_directory_uri() . '/assets/css/bootstrap.css');
	wp_enqueue_style('shop-component', get_template_directory_uri() . '/assets/css/component.css');
	wp_enqueue_style('shop-flexslider', get_template_directory_uri() . '/assets/css/flexslider.css');
	wp_enqueue_style('shop-style-old', get_template_directory_uri() . '/assets/css/style.css');
	wp_enqueue_style('shop-style-new', get_template_directory_uri() . '/style.css');

	wp_deregister_script('jquery');
	wp_register_script('jquery', get_template_directory_uri() . '/assets/js/jquery.min.js');

	/*wp_enqueue_script('jquery');*/

	wp_enqueue_script('shop-bootstrapjs', get_template_directory_uri() . '/assets/js/bootstrap-3.1.1.min.js');
	wp_enqueue_script('shop-responsiveslides', get_template_directory_uri() . '/assets/js/responsiveslides.min.js');
	wp_enqueue_script('shop-simpleCart', get_template_directory_uri() . '/assets/js/simpleCart.min.js');
	wp_enqueue_script('shop-flexisel-js', get_template_directory_uri() . '/assets/js/jquery.flexisel.js');


	add_action('wp_footer', function (){

		/*wp_enqueue_script('shop-bootstrapjs', get_template_directory_uri() . '/assets/js/bootstrap-3.1.1.min.js');
		wp_enqueue_script('shop-simpleCart', get_template_directory_uri() . '/assets/js/simpleCart.min.js');*/

	});
}

add_action('wp_enqueue_scripts', 'shop_styles_scripts');
add_filter('woocommerce_enqueue_styles', '__return_empty_array');