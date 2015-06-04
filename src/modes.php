<?php

namespace modes;

require __DIR__ . '/config.php';

if (DEV) {
	require __DIR__ . '/../vendor/firephp/firephp-core/lib/FirePHPCore/fb.php';
} else {
	function fb() {}
}

function template($part, $args = array()) {
	static $default_args = array(
		'title' => ''
	);
	$args = array_merge($default_args, $args);
	$template_part = __DIR__ . '/parts/' . $part . '.php';
	require __DIR__ . '/template.php';
}

function get_dbh() {
	$dbh = new \PDO(DSN);
	$dbh->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
	return $dbh;
}

function login($user, $password) {
	global $dbh;

	$query = $dbh->prepare('SELECT password, salt FROM users WHERE name = ?');
	$query->execute(array($user));
	$result = $query->fetch();

	$stored_password = $result['password'];

	if ($password === $stored_password) {
		set_password($user, $password);

		return true;
	} elseif (test_password($password, $stored_password, $result['salt'])) {
		$_SESSION['user'] = $user;

		return true;
	}

	return false;
}

function error($code = 500, $message = '') {
	static $titles = array(
		500 => 'Internal Server Error',
		404 => 'Not Found',
		417 => 'Expectation Failed'
	);
	header($_SERVER['SERVER_PROTOCOL'] . ' ' . $code . ' ' . $message, true, 500);
	template('error', array('title' => $titles[$code], 'code' => $code, 'message' => $message));
}

function message($text) {
	if (empty($_SESSION['messages'])) {
		$_SESSION['messages'] = array($text);
	} else {
		array_push($_SESSION['messages'], $text);
	}
}

function verify_post_nonce() {
	$result = $_POST[session_name()] === session_id();

	session_regenerate_id(true);

	return $result;
}

function set_password($user, $password) {
	global $dbh;

	$fp = fopen('/dev/urandom', 'r');
	$salt = base64_encode(fread($fp, 32));
	fclose($fp);

	$hash = hash_password($password, $salt);

	fb($hash);
	fb($salt);
	fb($query);

	$query = $dbh->prepare('UPDATE users SET password = :password, salt = :salt WHERE name = :name');
	$query->execute(array(
		'password' => $hash,
		'salt' => $salt,
		'name' => $user));
}

function hash_password($password, $salt) {
	return base64_encode(hash_hmac('sha512', $password . $salt, SITE_KEY, true));
}

function test_password($password, $stored_password, $salt) {
	return hash_password($password, $salt) === $stored_password;
}

function log_out($check) {
	if ($check === short_id()) {
		session_destroy();

		return true;
	}

	return false;
}

function redirect_home() {
	header('Location: ' . WWW_ROOT);
}

function redirect_back() {
	header('Location: ' . $_SERVER['REQUEST_URI']);
}

function short_id() {
	return base_convert(session_id(), 10, 32);
}