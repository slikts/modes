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

	$query = $dbh->prepare('SELECT password, salt, id FROM users WHERE name = ?');
	$query->execute(array($user));
	$result = $query->fetch();

	$stored_password = $result['password'];

	if (test_password($password, $stored_password, $result['salt'])) {
		populate_session($user, $result['id']);

		return TRUE;
	}

	return FALSE;
}

function populate_session($user, $uid) {
	$_SESSION['user'] = $user;
	$_SESSION['uid'] = $uid;
}

function error($code = 500, $message = '') {
	static $titles = array(
		500 => 'Internal Server Error',
		404 => 'Not Found',
		417 => 'Expectation Failed'
	);
	header($_SERVER['SERVER_PROTOCOL'] . ' ' . $code . ' ' . $message, TRUE, 500);
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

	session_regenerate_id(TRUE);

	return $result;
}

function get_random_string($length = 32) {
	$fp = fopen('/dev/urandom', 'r');
	$result = base64_encode(fread($fp, $length));
	fclose($fp);
	return $result;
}

function set_password($user, $password) {
	global $dbh;

	$salt = get_random_string();
	$hash = hash_password($password, $salt);

	$query = $dbh->prepare('UPDATE users SET password = :password, salt = :salt WHERE name = :name');
	$query->execute(array(
		'password' => $hash,
		'salt' => $salt,
		'name' => $user));
}

function hash_password($password, $salt) {
	return base64_encode(hash_hmac('sha512', $password . $salt, SITE_KEY, TRUE));
}

function test_password($password, $stored_password, $salt) {
	return hash_password($password, $salt) === $stored_password;
}

function end_session() {
	session_destroy();
	session_unset();
}

function end_autologin() {
	global $dbh;

	$delete = $dbh->prepare('DELETE FROM autologin WHERE uid = ? AND ip = ?');
	$delete->execute(array($_SESSION['uid'], $_SERVER['REMOTE_ADDR']));

	unset_cookie('autologin');
	unset_cookie('uid');
}

function unset_cookie($name) {
	setcookie($name, '', time() - LONG_TIME, WWW_ROOT);
	unset($_COOKIE[$name]);
}

function logout($check) {
	if ($check === short_id()) {
		if (isset($_COOKIE['autologin'])) {
			end_autologin();
		}

		end_session();

		return TRUE;
	}

	return FALSE;
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

function get_url($part = '') {
	if ($part === 'home') {
		$part = '';
	}
	return WWW_ROOT . '/' . $part;
}

function autologin() {
	global $dbh;

	if (empty($_SESSION['uid']) || empty($_SESSION['bid']) || empty($_SESSION['autologin'])) {
		return FALSE;
	}
	$uid = $_COOKIE['uid'];
	$bid = $_COOKIE['bid'];
	$key = $_COOKIE['autologin'];

	$ip = $_SERVER['REMOTE_ADDR'];

	$select = $dbh->prepare('SELECT a.hash, b.name FROM autologin a LEFT JOIN users b ON a.uid = b.id
		WHERE a.uid = ? AND a.ip = ? AND a.bid = ?');
	$select->execute(array($uid, $ip, $bid));
	$results = $select->fetch();

	if (!isset($results['hash']) || $results['hash'] !== autologin_hash($ip, $uid, $key)) {
		unset_cookie('autologin');

		return FALSE;
	}

	populate_session($results['name'], $uid);

	$update = $dbh->prepare('UPDATE autologin SET used = current_timestamp WHERE uid = ? AND ip = ?');
	$update->execute(array($uid, $ip));

	return TRUE;
}

function get_bid() {
	return empty($_COOKIE['bid']) ? get_random_string(16) : $_COOKIE['bid'];
}

function create_autologin() {
	global $dbh;

	$uid = $_SESSION['uid'];
	$bid = get_bid();
	$ip = $_SERVER['REMOTE_ADDR'];
	$key = get_random_string(24);
	$key_hash = autologin_hash($ip, $uid, $key);

	$dbh->beginTransaction();
	$delete = $dbh->prepare('DELETE FROM autologin WHERE uid = ? AND ip = ? AND bid = ?');
	$delete->execute(array($uid, $ip, $bid));
	$insert = $dbh->prepare('INSERT INTO autologin (ip, uid, hash, bid) VALUES (:ip, :uid, :hash, :bid)');
	$insert->execute(array(
		'ip' => $ip,
		'uid' => $uid,
		'hash' => $key_hash,
		'bid' => $bid
		));
	$dbh->commit();

	set_long_cookie('uid', $uid);
	set_long_cookie('autologin', $key);
	set_long_cookie('bid', $bid);
}

function set_long_cookie($name, $value) {
	setcookie($name, $value, time() + LONG_TIME, WWW_ROOT);
}

function autologin_hash($ip, $uid, $key) {
	return base64_encode(hash_hmac('sha512', $ip . $uid . $key, SITE_KEY, TRUE));
}
function set_autologin_cookie($key) {

}