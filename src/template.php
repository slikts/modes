<?php
namespace modes;

function template($part, $args = array()) {
	static $default_args = array(
		'title' => ''
	);
	$args = array_merge($default_args, $args);
	$template_part = __DIR__ . '/parts/' . $part . '.php';
	require __DIR__ . '/parts/base.php';
}

function error_page($code = 500, $message = '') {
	static $titles = array(
		500 => 'Internal Server Error',
		404 => 'Not Found',
		417 => 'Expectation Failed'
	);
	header($_SERVER['SERVER_PROTOCOL'] . ' ' . $code . ' ' . $message, TRUE, 500);
	template('error', array('title' => $titles[$code], 'code' => $code, 'message' => $message));
}
