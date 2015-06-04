<?php

define('DSN', 'pgsql:host=localhost;dbname=db;user=usr;password=pwd');

define('FILES_DIR', '/home/slikts/m');
define('WWW_ROOT', '/m');
define('STATIC_ROOT', WWW_ROOT);

define('LONG_TIME', 60 * 60 * 24 * 100);

define('HTTPS', FALSE);

define('DEV', TRUE);

define('NAME', 'modes');

define('SITE_KEY', '908uyzs79y123jhoiuzsolk');

define('GITHUB_URL', 'https://github.com/slikts/modes');

define('VERSION', file_get_contents(__DIR__ . '/../VERSION'));

date_default_timezone_set('Europe/Riga');

session_cache_limiter('private_no_expire');

ini_set('session.use_only_cookies', TRUE);
ini_set('session.cookie_httponly', TRUE);
ini_set('session.cookie_secure', !DEV);
ini_set('session.entropy_file', '/dev/urandom');
ini_set('session.entropy_length', '512');
ini_set('session.cookie_path', WWW_ROOT);
ini_set('session.use_strict_mode', TRUE);

session_name('sid');

//require __DIR__ . '/../vendor/autoload.php';
