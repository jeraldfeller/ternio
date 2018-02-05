<?php
//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
ob_start();
session_start();

define('DB_USER_MAIN', 'root');
define('DB_PWD_MAIN', 'dfab7c358');
define('DB_NAME_MAIN', 'tio_db');
define('DB_HOST_MAIN', 'localhost');
define('DB_DSN_MAIN', 'mysql:host=' . DB_HOST_MAIN . ';dbname=' . DB_NAME_MAIN .'');

?>
