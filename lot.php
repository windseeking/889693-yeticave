<?php

require_once('functions.php');
require_once('data.php');
require_once('config.php');

session_start();
if (isset($_SESSION['user'])) {
    $user = $_SESSION['user'];
}

$con = get_connection($database_config);
$cats = get_cats($con);
$lots = [];
$errors = [];

if (isset($_GET['id'])) {
    $lot = get_lot_by_id($con, $_GET['id']);
    if (isset($lot)) {
        $page_title = $lot['title'];
        $lot_id = $_GET['id'];
        $bids = get_bids_by_lot_id($con, $lot_id);
        $page_content = include_template('lot.php', [
            'lot' => $lot,
            'cats' => $cats,
            'bids' => $bids,
            'errors' => $errors]);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $form = $_POST;
            if (empty($form['bid'])) {
                $errors['bid'] = "Введите сумму";
                $page_content = include_template('lot.php', [
                    'lot' => $lot,
                    'cats' => $cats,
                    'bids' => $bids,
                    'errors' => $errors]);
            } elseif ($form['bid'] <= 0 or $form['bid'] != intval($form['bid'])) {
                $errors['bid'] = 'Введите целое число больше нуля';
            } elseif ($form['bid'] < ($lot['current_price'] + $lot['bid_step'])) {
                $errors['bid'] = 'Ставка должна быть больше, чем текущая цена + шаг ставки';
            }

            if (empty($errors)) {
                $bid = $form['bid'];
                if (add_bid($con, $bid, $user['id'], $lot_id)) {
                    if(update_price($con, $bid, $lot_id)) {
                        header('Location: lot.php?id=' . $lot_id);
                    }
                }
            } else {
                $page_content = include_template('lot.php', [
                    'lot' => $lot,
                    'cats' => $cats,
                    'bids' => $bids,
                    'errors' => $errors
                ]);
            }
        }
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
