<?php

namespace modes;

require __DIR__ . '/../src/modes.php';

session_start();

header('Content-Type: text/html; charset=utf-8');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Cache-Control: post-check=0, pre-check=0', false);
header('Pragma: no-cache');

$request_uri = $_SERVER['REQUEST_URI'];

if (substr($request_uri, 0, strlen(WWW_ROOT)) === WWW_ROOT) {
	$request_uri = substr($request_uri, strlen(WWW_ROOT));
}

$path = array_values(array_filter(explode('/', $request_uri)));
if (count($path) === 0) {
	$path[0] = '';
}

// Routing

if ($path[0] === 'reset') {
	error(417);	

	return;
}
if (DEV && $path[0] === 'info') {
	phpinfo();

	return;
}

try {
	global $dbh;
	$dbh = get_dbh();
} catch (\PDOException $e) {
	error(500, $e->getMessage());
	
	return;
}

if (!empty($_POST)) {
	if (!verify_post_nonce()) {
		error(417);

		return;
	}
	if (isset($_POST['user'])) {
		if (!login($_POST['user'], $_POST['password'])) {
			message('Login failed');

			redirect_home();

			return;
		}

		redirect_back();
	}


	return;
}

if (!isset($_SESSION['user'])) {
	template('login', array('title' => 'Log in'));

	return;
}

if (!empty($path) && $path[0] === 'logout') {
	if (isset($path[1]) && log_out($path[1])) {
		redirect_home();

		return;
	}
	error(417);

	return;
}

if ($path[0] === '') {
	template('home');

	return;
}

header('HTTP/1.0 404 Not Found');
error(404);