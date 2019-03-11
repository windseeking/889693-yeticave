<?php

require_once('init.php');

if (isset($_SESSION['user'])) {
    header("Location: index.php");
    exit();
};

$user = [];
$cats = get_cats($con);
$lots = get_lots($con);
$page_title = 'Вход';
$errors = [];
$form = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $form = $_POST;

    if (empty($form['password'])) {
        $errors['password'] = 'Введите пароль';
    }

    if (empty($form['email'])) {
        $errors['email'] = 'Введите email';
    } else {
        $email = mysqli_real_escape_string($con, $form['email']);
        $sql = "SELECT * FROM user WHERE email = '$email'";
        $res = mysqli_query($con, $sql);

        $user = $res ? mysqli_fetch_array($res, MYSQLI_ASSOC) : null;

        if (!$user) {
            $errors['email'] = "Пользователь стаким email-адресом не найден";
            $page_content = include_template('login.php', [
                'form' => $form,
                'errors' => $errors
            ]);
        } elseif (password_verify($form['password'], $user['password'])) {
            $_SESSION['user'] = $user;
            header("Location: index.php");
        } else {
            $errors['password'] = "Неверно введен пароль";
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
