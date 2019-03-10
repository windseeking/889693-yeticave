<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once('functions.php');
require_once('config.php');

session_start();

$con = get_connection($database_config);
$user = [];
$user_name = isset($_SESSION['name']) ? $_SESSION['name'] : '';
