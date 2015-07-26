<?php
namespace modes;

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

function message($text) {
	if (empty($_SESSION['messages'])) {
		$_SESSION['messages'] = array($text);
	} else {
		array_push($_SESSION['messages'], $text);
	}
}

function verify_post_nonce() {
	$result = VERIFY_POST_NONCE ? $_POST[session_name()] === session_id() : TRUE;

	session_regenerate_id(TRUE);

	return $result;
}

function hash_password($password, $salt) {
	return base64_encode(hash_hmac('sha512', $password . $salt, TOOL_KEY, TRUE));
}

function test_password($password, $stored_password, $salt) {
	return hash_password($password, $salt) === $stored_password;
}

function end_session() {
	session_destroy();
	session_unset();
}

function remove_autologin($uid, $ip=NULL, $bid=NULL) {
	global $dbh;

	$sql = 'DELETE FROM autologin WHERE uid = ?';
	$params = array($uid);
	if ($ip) {
		$params[] = $ip;
		$sql .= ' AND ip = ?';
	}
	if ($bid) {
		$params[] = $bid;
		$sql .= ' AND bid = ?';
	}
	$delete = $dbh->prepare($sql);
	$delete->execute($params);
}

function end_autologin() {
	remove_autologin($_SESSION['uid'], $_SERVER['REMOTE_ADDR'], $_COOKIE['bid']);

	unset_cookie('autologin');
	unset_cookie('uid');
}

function logout($check) {
	if ($check === short_id()) {
		if (have_autologin_cookies()) {
			end_autologin();
		}

		end_session();

		return TRUE;
	}

	return FALSE;
}

function have_autologin_cookies() {
	return !empty($_COOKIE['uid']) && !empty($_COOKIE['bid']) && !empty($_COOKIE['autologin']);
}

function autologin() {
	global $dbh;

	if (!have_autologin_cookies()) {
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
	$insert = $dbh->prepare('INSERT INTO autologin (ip, uid, hash, bid, meta) VALUES (:ip, :uid, :hash, :bid, :meta)');
	$insert->execute(array(
		'ip' => $ip,
		'uid' => $uid,
		'hash' => $key_hash,
		'bid' => $bid,
		'meta' => json_encode(array('user_agent' => $_SERVER["HTTP_USER_AGENT"]))
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
	return base64_encode(hash_hmac('sha512', $ip . $uid . $key, TOOL_KEY, TRUE));
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

function unset_cookie($name) {
	setcookie($name, '', time() - LONG_TIME, WWW_ROOT);
	unset($_COOKIE[$name]);
}

function get_autologin_sessions() {
	global $dbh;

	$select = $dbh->prepare('SELECT created, used, ip, meta->\'user_agent\' AS user_agent FROM autologin WHERE uid = ?');
	$select->execute(array($_SESSION['uid']));

	return $select->fetchAll(\PDO::FETCH_ASSOC);
}

function get_post_nonce_field() {
	?><input type="hidden" name="<?=session_name()?>" value="<?=session_id()?>"><?
}