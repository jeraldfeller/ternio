<?php
require $_SERVER['DOCUMENT_ROOT'] . '/dashboard/Model/Init.php';
require $_SERVER['DOCUMENT_ROOT'] . '/dashboard/Model/session.php';
unset($_SESSION["userData"]);
unset($_SESSION["isLoggedIn"]);
session_destroy();

Header('location: login.php');
?>