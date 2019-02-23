<?php

require_once('functions.php');
require_once('data.php');
require_once('config.php');

session_start();

$con = get_connection($database_config);
$cats = get_cats($con);

if (isset($_GET['id'])) {
    $lot = get_lot_by_id($con, $_GET['id']);
    if (isset($lot)) {
        $page_content = include_template('lot.php', ['lot' => $lot, 'cats' => $cats]);
        $page_title = $lot['title'];
    } else {
        $page_content = include_template('404.php', ['cats' => $cats]);
        $page_title = 'Error 404';
    }
} else {
    $page_content = include_template('404.php', ['cats' => $cats]);
    $page_title = 'Error 404';
}

$layout_content = include_template('layout.php', [
    'content' => $page_content,
    'title' => $page_title,
    'user_name' => $user_name,
    'cats' => $cats
]);

print($layout_content);
