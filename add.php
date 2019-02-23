<?php

require_once('functions.php');
require_once('data.php');
require_once('config.php');

$con = get_connection($database_config);
$user_id = 1;
$cats = get_cats($con);
$lot = [];
$errors = [];
$page_title = 'Создание лота';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $lot = $_POST['lot'];
    $required = ['title', 'description', 'cat_id', 'opening_price', 'ends_at', 'bid_step'];
    foreach ($required as $item) {
        if (empty($lot[$item])) {
            $errors[$item] = 'Это поле нужно заполнить';
        }
    }

    if (!empty($lot['cat_id']) and !is_cat_exists($cats, $lot)) {
        $errors['cat_id'] = 'Выберите существующую категорию';
    }
    if (!empty($lot['ends_at'])) {
        if (!is_valid_date_format($lot['ends_at'])) {
            $errors['ends_at'] = 'Некорректный формат даты';
        } elseif (!is_valid_date_interval($lot['ends_at'])) {
            $errors['ends_at'] = 'Дата должна быть больше текущей хотя бы на 1 день';
        }
    }
    if (!empty($lot['opening_price']) and $lot['opening_price'] <= 0) {
        $errors['opening_price'] = 'Введите число больше нуля';
    }
    if (!empty($lot['bid_step']) and $lot['bid_step'] <= 0 or $lot['bid_step'] != intval($lot['bid_step'])) {
        $errors['bid_step'] = 'Введите целое число больше нуля';
    }
    if (!empty($_FILES['img_url']['name'])) {
        $tmp_name = $_FILES['img_url']['tmp_name'];
        $img_type = mime_content_type($tmp_name);
        $img_size = $_FILES['img_url']['size'];
        if (($img_type != 'image/jpg') and ($img_type != 'image/jpeg') and ($img_type != 'image/png')) {
            $errors['img_url'] = 'Допустимые форматы файлов: jpg, jpeg, png';
        } elseif ($img_size > 10485760) {
            $errors['img_url'] = 'Изображение не должно превышать 10 Мб';
        } else {
            $img_name = 'lot' . '-' . uniqid() . '-' . $_FILES['img_url']['name'];
            $img_path = __DIR__ . '/uploads/';
            move_uploaded_file($tmp_name, $img_path . $img_name);
            $lot['img_url'] = '/uploads/' . $img_name;
        }
    } else {
        $errors['img_url'] = 'Изображение обязательно нужно выбрать';
    }

    if (count($errors)) {
        $page_content = include_template('add.php', [
            'lot' => $lots,
            'cats' => $cats,
            'errors' => $errors
        ]);
        $_SESSION['error'] = 'Пожалуйста, исправьте ошибки в форме.';
    } elseif (add_lot($con, $lot, $user_id)) {
        $lot_id = mysqli_insert_id($con);
        header('Location: lot.php?id=' . $lot_id);
        die();
    } else {
        $page_content = include_template('add.php', [
            'lot' => $lot,
            'cats' => $cats,
            'errors' => $errors
        ]);
        $_SESSION['error'] = 'Что-то пошло не так, форма не отправлена. Повторите отправку позже.';
    }
}
$page_content = include_template('add.php', [
    'lot' => $lot,
    'cats' => $cats,
    'errors' => $errors
]);

$layout_content = include_template('layout.php', [
    'content' => $page_content,
    'title' => $page_title,
    'user_name' => filter_tags($user_name),
    'cats' => $cats
]);

print($layout_content);
