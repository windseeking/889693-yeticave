<?php

require_once('init.php');

$cats = get_cats($con);
$lots = get_lots($con);
$page_title = 'Главная';

$page_content = include_template('index.php', [
    'lots' => $lots,
    'cats' => $cats
]);

$layout_content = include_template('layout.php', [
    'content' => $page_content,
    'title' => $page_title,
    'cats' => $cats
]);

print($layout_content);
