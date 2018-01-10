<?php

if($_SERVER['SERVER_ADDR']==="192.168.56.101") {

	define('DB_HOST', 'localhost');
	define('DB_USERNAME', 'root');
	define('DB_PASSWORD', 'db_password');
	define('DB_NAME', 'xoka');
	define('ROOT_DIR', __DIR__.'/../');
	define('BASE_DIR', '/basic-web-service/api/v1/');
	define('SITE_URL', 'http://192.168.56.101/basic-web-service/api/v1');
	define('PASSWORD_SALT', 'password_salt');

} else {

	define('DB_HOST', 'localhost');
	define('DB_USERNAME', 'db_username');
	define('DB_PASSWORD', 'db_password');
	define('DB_NAME', 'basic_web_service');
	define('ROOT_DIR', __DIR__.'/../');
	define('BASE_DIR', '/basic-web-service/api/v1/');
	define('SITE_URL', 'http://basic-web-service.com/api/v1');
	define('PASSWORD_SALT', 'password_salt');

}