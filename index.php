<?php

require_once('init.php');

$cats = get_cats($con);
$lots = [];
$pagination_data = [];

$cur_page = $_GET['page'] ?? 1;
$page_lots = 9;

$res = mysqli_query($con, 'SELECT COUNT(*) as lots_amount FROM lot');
$lots_count = mysqli_fetch_assoc($res)['lots_amount'];
$pages_count = ceil($lots_count / $page_lots);

if ($pages_count <= 1) {
    $pagination_data = [];
} else {
    $offset = ($cur_page - 1) * $page_lots;
    $pages = range(1, $pages_count);

    $sql = 'SELECT l.*, c.name AS cat_name, COUNT(b.id) as bids_amount FROM lot l '
        . 'JOIN cat c ON l.cat_id = c.id '
        . 'LEFT JOIN bid b ON b.lot_id = l.id '
        . 'GROUP BY l.id ORDER BY l.created_at DESC LIMIT ' . $page_lots . ' OFFSET ' . $offset;
    $res = mysqli_query($con, $sql);

    if ($res) {
        $lots = mysqli_fetch_all($res, MYSQLI_ASSOC);
        $pagination_data = [
            'cur_page' => $cur_page,
            'pages' => $pages,
            'pages_count' => $pages_count
        ];
    }
    $pagination_data = ['cur_page' => $cur_page,
        'pages' => $pages,
        'pages_count' => $pages_count];
}

$page_title = 'Главная';

$page_content = include_template('index.php', [
    'lots' => $lots,
    'cats' => $cats,
    'pagination_data' => $pagination_data
]);

$layout_content = include_template('layout.php', [
    'content' => $page_content,
    'title' => $page_title,
    'cats' => $cats
]);

print($layout_content);
