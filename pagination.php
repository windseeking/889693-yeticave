<?php

require_once('functions.php');
require_once('data.php');
require_once('config.php');

session_start();

$con = get_connection($database_config);
$cats = get_cats($con);
$lots = get_lots($con);
$pages = [];
$pagination = [];

if (isset($_GET['page'])) {
    $cur_page = $_GET['page'] ?? 1;
    $page_lots = 6;

    $res = mysqli_query($con, 'SELECT COUNT(*) as lots_amount FROM lot');
    $lots_count = mysqli_fetch_assoc($res)['lots_amount'];

    $pages_count = ceil($lots_count / $page_lots);
    $offset = ($cur_page - 1) * $page_lots;

    $pages = range(1, $pages_count);

    $sql = 'SELECT *, c.name FROM lot l '
        . 'JOIN cat c ON l.cat_id = c.id '
        . 'ORDER BY l.created_at DESC LIMIT ' . $page_lots . ' OFFSET ' . $offset;

    if ($lots = mysqli_query($con, $sql)) {
        $pagination_data = [
            'lots' => $lots,
            'pages' => $pages,
            'pages_count' => $pages_count,
            'cur_page' => $cur_page
        ];
        $content = include_template('search.php', $pagination_data);
    } else {
        print(mysqli_error($con));
    }

    $page_title = $search . " – YetiCave";

    $page_content = include_template('_pagination.php', [
        'lots' => $lots,
        'cats' => $cats,
        'pages' => $pages,
        'cur_page' => $cur_page
    ]);
}

$layout_content = include_template('layout.php', [
    'content' => $page_content,
    'title' => $page_title,
    'user_name' => $user_name,
    'cats' => $cats
]);

print($layout_content);
