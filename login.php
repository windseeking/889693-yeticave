<?php

require_once('functions.php');
require_once('data.php');
require_once('config.php');

$con = get_connection($database_config);
$cats = get_cats($con);
$lots = get_lots($con);

$page_title = 'Вход';

$page_content = include_template('login.php', [
    'cats' => $cats
]);

$layout_content = include_template('layout.php', [
    'content' => $page_content,
    'title' => $page_title,
    'cats' => $cats
]);

print($layout_content);
