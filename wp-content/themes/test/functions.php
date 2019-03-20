<?php


function test_scripts() {
	wp_enqueue_style('test-bootstrapcss', get_template_directory_uri() . '/assets/bootstrap/css/bootstrap.min.css');
	wp_enqueue_style('test-style', get_template_directory_uri() . '/style.css');

	add_action('wp_footer', function (){
		wp_deregister_script('jquery');
		wp_register_script('jquery', 'https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js');

		/*wp_enqueue_script('jquery');*/
		wp_enqueue_script('test-popper', 'https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js', array('jquery'));
		wp_enqueue_script('test-bootstrapjs', get_template_directory_uri() . '/assets/bootstrap/js/bootstrap.min.js');
	});
}

add_action('wp_enqueue_scripts', 'test_scripts');

function test_setup() {
	add_theme_support('title-tag');
	add_theme_support('post-thumbnails');
	add_image_size('mini-thumbnails', 100, 100, true);
}

add_action('after_setup_theme', 'test_setup');

function my_navigation_template() {
	return '
	<nav class="navigation" role="navigation">
		<div class="nav-links">%3$s</div>
	</nav>';
}

add_filter('navigation_markup_template', 'my_navigation_template');