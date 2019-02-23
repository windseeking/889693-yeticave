<?php

require_once('functions.php');
require_once('data.php');
require_once('config.php');

session_start();
if (isset($_SESSION['user'])) {
    header("Location: index.php");
    exit();
};

$con = get_connection($database_config);
$cats = get_cats($con);
$lots = get_lots($con);
$page_title = 'Вход';
$errors = [];
$form = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $form = $_POST;
    $required = ['email', 'password'];
    foreach ($required as $field) {
        if (empty($form[$field])) {
            $errors[$field] = 'Это поле нужно заполнить';
        }
    }

    $email = mysqli_real_escape_string($con, $form['email']);
    $sql = "SELECT * FROM user WHERE email = '$email'";
    $res = mysqli_query($con, $sql);

    $user = $res ? mysqli_fetch_array($res, MYSQLI_ASSOC) : null;

    if ($user) {
        if (password_verify($form['password'], $user['password'])) {
            $_SESSION['user'] = $user;
            if (isset($_SESSION['user']) and !count($errors)) {
                $user = $_SESSION['user'];
                header("Location: index.php");
                die();
            } else {
                $page_content = include_template('login.php', []);
            }
        } else {
            $errors['password'] = "Неверно введен пароль";
            $page_content = include_template('login.php', [
                'form' => $form,
                'errors' => $errors
            ]);
        }
    } else {
        $errors['email'] = "Пользователь стаким email-адресом не найден";
        $page_content = include_template('login.php', [
            'form' => $form,
            'errors' => $errors
        ]);
    }
}
$page_content = include_template('login.php', [
    'form' => $form,
    'cats' => $cats,
    'user' => $user,
    'errors' => $errors
]);

$layout_content = include_template('layout.php', [
    'content' => $page_content,
    'title' => $page_title,
    'cats' => $cats
]);

print($layout_content);
