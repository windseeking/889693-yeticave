<?php

require_once('init.php');

$cats = get_cats($con);
$lots = [];
$cat_name = '';

if (isset($_GET['cat'])) {
    if (!is_cat_id_exists($con, (int)$_GET['cat'])) {
        $page_content = include_template('404.php', ['cats' => $cats]);
        $page_title = 'Error 404';
    } else {
        $cat_id = (int)$_GET['cat'];
        $cat_name = get_cat_name_by_id($con, $cat_id);
        $lots = get_lots_by_cat_id($con, $cat_id);
        if (!empty($lots)) {
            $page_title = $cat_name . ' – YetiCave';
            $page_content = include_template('all-lots.php', [
                'lots' => $lots,
                'cats' => $cats,
                'cat_name' => $cat_name
            ]);
        } else {
            $page_title = $cat_name . ' – YetiCave';
            $page_content = include_template('all-lots.php', [
                'cats' => $cats,
                'lots' => $lots,
                'cat-name' => $cat_name
            ]);
        }
    }
} else {
    $page_content = include_template('404.php', ['cats' => $cats]);
    $page_title = 'Error 404';
}

if (isset($_GET['search'])) {
    $search = trim($_GET['search']);
    $sql = 'SELECT l.*, c.name as cat_name, COUNT(b.id) as bids_amount FROM lot l '
        . 'JOIN cat c ON l.cat_id = c.id '
        . 'LEFT JOIN bid b ON b.lot_id = l.id '
        . 'WHERE MATCH(l.title, l.description) AGAINST(?) AND l.ends_at > CURDATE() '
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

$nav_content = include_template('_navigation.php', ['cats' => $cats]);

$layout_content = include_template('layout.php', [
    'nav' => $nav_content,
    'content' => $page_content,
    'title' => $page_title,
    'user_name' => $user_name,
    'cats' => $cats
]);

print($layout_content);
