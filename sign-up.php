<?php

require_once('functions.php');
require_once('data.php');
require_once('config.php');

$con = get_connection($database_config);
$cats = get_cats($con);
$user = [];
$errors = [];
$page_title = 'Регистрация';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user = $_POST['user'];
    $required = ['email', 'password', 'name', 'contacts'];
    foreach ($required as $item) {
        if (empty($user[$item])) {
            $errors[$item] = 'Это поле нужно заполнить';
        }
    }

    if (!empty($user['email'])) {
        if (!filter_var($user['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Неверный формат email-адреса';
        }
        if (is_email_exist($con, $user['email'])) {
            $errors['email'] = 'Пользователь с таким email-адресом уже существует';
        }
    }

    if (empty($_FILES['avatar_url']['name'])) {
        $user['avatar_url'] = '';
    } else {
        $tmp_name = $_FILES['avatar_url']['tmp_name'];
        $img_type = mime_content_type($tmp_name);
        $img_size = $_FILES['avatar_url']['size'];
        if (($img_type != 'image/jpg') and ($img_type != 'image/jpeg') and ($img_type != 'image/png')) {
            $errors['avatar_url'] = 'Допустимые форматы файлов: jpg, jpeg, png';
        } elseif ($img_size > 5242880) {
            $errors['avatar_url'] = 'Изображение не должно превышать 5 Мб';
        } else {
            $img_name = 'avatar' . '-' . uniqid() . '-' . $_FILES['avatar_url']['name'];
            $img_path = __DIR__ . '/uploads/';
            move_uploaded_file($tmp_name, $img_path . $img_name);
            $user['avatar_url'] = '/uploads/' . $img_name;
        }
    }

    if (count($errors)) {
        $page_content = include_template('sign-up.php', [
            'user' => $user,
            'cats' => $cats,
            'errors' => $errors
        ]);
        $_SESSION['error'] = 'Пожалуйста, исправьте ошибки в форме.';
    } elseif (add_user($con, $user)) {
        header('Location: login.php');
        die();
    } else {
        $page_content = include_template('sign-up.php', [
            'user' => $user,
            'cats' => $cats,
            'errors' => $errors
        ]);
        $_SESSION['error'] = 'Что-то пошло не так, форма не отправлена. Повторите отправку позже.';
    }
}
$page_content = include_template('sign-up.php', [
    'user' => $user,
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
