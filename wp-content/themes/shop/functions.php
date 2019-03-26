<?php

function get_navigation() {
	$templates = array();
	$templates[] = 'navigation.php';

	locate_template($templates);
}