<?php
date_default_timezone_set('Europe/Moscow');
$is_auth = rand(0, 1);

function filter_tags(string $str = null): string {
    return $str === null ? '' : strip_tags($str);
};

function format_cost(float $cost): string {
    $cost = ceil($cost);
    if ($cost >= 1000) {
        $cost = number_format($cost, 0, ',', ' ');
    }
    return $cost . ' ₽';
};

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
};

function time_left(): string {
    $now = date_create('now');
    $deadline = date_create('tomorrow');
    $diff = date_diff($now, $deadline);
    return date_interval_format($diff,"%H:%I");
};
