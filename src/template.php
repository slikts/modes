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

function error_page($code = 500, $message = '', $template = TRUE) {
	static $titles = array(
		500 => 'Internal Server Error',
		404 => 'Not Found',
		417 => 'Expectation Failed'
	);
	header($_SERVER['SERVER_PROTOCOL'] . ' ' . $code . ' ' . $titles[$code], TRUE);
	if ($template) {
		template('error', array('title' => 'Error ' . $code, 'code' => $code, 'message' => '', 'descr' => $titles[$code]));
	} else {
		echo $message;
	}
}
