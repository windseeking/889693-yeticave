<?php

require_once('init.php');

if (isset($_SESSION['user'])) {
    $user = $_SESSION['user'];
}

$con = get_connection($database_config);
$cats = get_cats($con);
$lots = [];
$search = [];

if (isset($_GET['search'])) {
    $search = trim($_GET['search']);
    $sql = 'SELECT l.*, c.name as cat_name, COUNT(b.id) as bids_amount FROM lot l '
         . 'JOIN cat c ON l.cat_id = c.id '
         . 'LEFT JOIN bid b ON b.lot_id = l.id '
         . 'WHERE MATCH(l.title, l.description) AGAINST(?) '
         . 'GROUP BY l.id ORDER BY l.created_at DESC';
    $stmt = db_get_prepare_stmt($con, $sql, [$search]);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);

    if ($res) {
        $lots = mysqli_fetch_all($res, MYSQLI_ASSOC);
        $page_title = $search . " – YetiCave";
        $page_content = include_template('search.php', [
            'lots' => $lots,
            'cats' => $cats,
            'search' => $search
        ]);
    }
}
//
//$cur_page = (int) $_GET['page'] ?? 1;
//$page_lots = 3;
//
//$res = mysqli_query($con, 'SELECT COUNT(*) as lots_amount FROM lot');
//$lots_count = mysqli_fetch_assoc($res)['lots_amount'];
//$pages_count = ceil($lots_count / $page_lots);
//
//if ($pages_count <= 1) {
//    $pagination_data = [];
//} else {
//    $offset = ($cur_page - 1) * $page_lots;
//    $pages = range(1, $pages_count);
//
//    $sql = 'SELECT l.*, c.name FROM lot l '
//        . 'JOIN cat c ON l.cat_id = c.id '
//        . 'ORDER BY l.created_at DESC LIMIT ' . $page_lots . ' OFFSET ' . $offset;
//    $res = mysqli_query($con, $sql);
//
//    if ($res) {
//        $lots = mysqli_fetch_all($res, MYSQLI_ASSOC);
//    }
//}
//
//if (isset($_GET['cat'])) {
//    if (!is_cat_id_exists($con, $_GET['cat'])) {
//        $page_content = include_template('404.php', ['cats' => $cats]);
//        $page_title = 'Error 404';
//    } else {
//        $cat_id = (int)$_GET['cat'];
//        $cat_name = get_cat_name_by_id($con, $cat_id);
//        $lots = get_lots_by_cat_id($con, $cat_id);
//    }
//}
//
//$scriptname = pathinfo(__FILE__, PATHINFO_BASENAME);
//$query = http_build_query($_GET);
//$url = '/' . $scriptname . '?' . $query;
//
//$pagination_data = [
//    'cur_page' => $cur_page,
//    'pages' => $pages,
//    'pages_count' => $pages_count,
//    'url' => $url
//];

$page_content = include_template('search.php', [
    'lots' => $lots,
    'cats' => $cats,
    'search' => $search,
    'pagination_data' => $pagination_data
]);

$layout_content = include_template('layout.php', [
    'content' => $page_content,
    'title' => $page_title,
    'cats' => $cats
]);

print($layout_content);
