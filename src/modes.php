<?php
namespace modes;

require __DIR__ . '/config.php';

if (DEV) {
	require __DIR__ . '/../vendor/firephp/firephp-core/lib/FirePHPCore/fb.php';
} else {
	function fb() {}
}

require __DIR__ . '/template.php';
require __DIR__ . '/session.php';
require __DIR__ . '/upload.php';
require __DIR__ . '/files.php';

function get_dbh() {
	$dbh = new \PDO(DSN);
	$dbh->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
	return $dbh;
}

function get_random_string($length = 32) {
	$fp = fopen('/dev/urandom', 'r');
	$result = base64_encode(fread($fp, $length));
	fclose($fp);
	return $result;
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

function format_datetime($datetime) {
	return date('Y-m-d H:i O', $datetime);
}

function format_bytes($bytes, $precision = 2)
{
    $units = array('B', 'KiB', 'MiB', 'GiB', 'TiB');

    $bytes = max($bytes, 0);
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow = min($pow, count($units) - 1);

    $bytes /= pow(1024, $pow);

    return round($bytes, $precision) . ' ' . $units[$pow];
}
