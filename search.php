<?php

require_once('init.php');

if (!empty($_SESSION['user'])) {
    $user = $_SESSION['user'];
}

$cats = get_cats($con);
$lots = [];
$search = [];

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
} else {
    $page_content = include_template('404.php', []);
    $page_title = 'Error 404';
}

$nav_content = include_template('_navigation.php', ['cats' => $cats]);

$layout_content = include_template('layout.php', [
    'content' => $page_content,
    'title' => $page_title,
    'cats' => $cats,
    'nav' => $nav_content
]);

print($layout_content);
