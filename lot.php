<?php

require_once('init.php');

if (!empty($_SESSION['user'])) {
    $user = $_SESSION['user'];
}

$cats = get_cats($con);
$errors = [];
$bids = [];

if (isset($_GET['id'])) {
    $lot_id = (int)$_GET['id'];
    $lot = get_lot_by_id($con, $lot_id);
} else {
    $page_content = include_template('404.php', ['cats' => $cats]);
    $page_title = 'Error 404';
}

if (isset($lot)) {
    $page_title = $lot['title'];
    $bids = get_bids_by_lot_id($con, $lot_id);
    $page_content = include_template('lot.php', [
        'lot' => $lot,
        'cats' => $cats,
        'bids' => $bids,
        'errors' => $errors
    ]);
} else {
    $page_content = include_template('404.php', ['cats' => $cats]);
    $page_title = 'Error 404';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $form = $_POST;
    if (empty($form['bid'])) {
        $errors['bid'] = "Введите сумму";
        $page_content = include_template('lot.php', [
            'lot' => $lot,
            'cats' => $cats,
            'bids' => $bids,
            'errors' => $errors
        ]);
    } elseif ($form['bid'] <= 0 || $form['bid'] != intval($form['bid'])) {
        $errors['bid'] = 'Введите целое число больше нуля';
    } elseif ($form['bid'] < ($lot['current_price'] + $lot['bid_step'])) {
        $errors['bid'] = 'Ставка должна быть больше, чем текущая цена + шаг ставки';
    }

    if (empty($errors)) {
        $bid = $form['bid'];
        if (!empty($user) && add_bid($con, $bid, $user['id'], $lot_id) && update_price($con, $bid, $lot_id)) {
            header('Location: lot.php?id=' . $lot_id);
            die();
        }
        $errors['bid'] = 'Ставка не добавлена. Повторите попытку позже.';
    }
    $page_content = include_template('lot.php', [
        'lot' => $lot,
        'cats' => $cats,
        'bids' => $bids,
        'errors' => $errors
    ]);
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
