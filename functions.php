<?php
date_default_timezone_set('Europe/Moscow');
$is_auth = rand(0, 1);

function filter_tags(string $str = null): string {
    if ($str === null) {
        return '';
    }
    return strip_tags($str);
}

function get_connection(array $database_config)
{
    $con = mysqli_connect($database_config['host'], $database_config['user'], $database_config['password'],
        $database_config['database']);
    if (!$con) {
        die(mysqli_connect_error());
    }
    mysqli_set_charset($con, 'utf8');
    return $con;
}

function get_cats($con): array {
    $sql =
        'SELECT * FROM cat';
    $res = mysqli_query($con, $sql);
    return $cats = mysqli_fetch_all($res, MYSQLI_ASSOC);
}

function get_lots($con): array {
    $sql =
        'SELECT * FROM lot ORDER BY created_at DESC';
    $res = mysqli_query($con, $sql);
    return $lots = mysqli_fetch_all($res, MYSQLI_ASSOC);
}

function format_cost(float $cost): string {
    $cost = ceil($cost);
    if ($cost >= 1000) {
        $cost = number_format($cost, 0, ',', ' ');
    }
    return $cost . ' ₽';
}

function include_template($name, $data) {
    $name = 'templates/' . $name;
    $result = '';

    if (!is_readable($name)) {
        return $result;
    }

    ob_start();
    extract($data);
    require $name;

    $result = ob_get_clean();

    return $result;
}

function time_left(): string {
    $now = date_create('now');
    $deadline = date_create('tomorrow');
    $diff = date_diff($now, $deadline);
    return date_interval_format($diff,"%H:%I");
}
