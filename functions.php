<?php
require_once('mysql_helper.php');

date_default_timezone_set('Europe/Moscow');
$is_auth = rand(0, 1);

/**
 * @param $con
 * @param array $lot
 * @param int $user_id
 * @return bool
 */
function add_lot($con, array $lot, int $user_id): bool
{
    $sql =
        'INSERT INTO lot (title, description, img_url, cat_id, opening_price, ends_at, bid_step, created_at, author_id) 
        VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), ?)';
    $values = [
        $lot['title'] = filter_tags($lot['title']),
        $lot['description'] = filter_tags($lot['description']),
        $lot['img_url'],
        $lot['cat_id'],
        $lot['opening_price'],
        $lot['ends_at'],
        $lot['bid_step'],
        $lot['created_at'],
        $user_id
    ];
    $stmt = db_get_prepare_stmt($con, $sql, $values);
    mysqli_stmt_execute($stmt);

    if (mysqli_error($con)) {
        return mysqli_error($con);
    }
    return true;
}

/**
 * @param string|null $str
 * @return string
 */
function filter_tags(string $str = null): string
{
    if ($str === null) {
        return '';
    }
    return strip_tags($str);
}

/**
 * @param $con
 * @return array
 */
function get_cats($con): array
{
    $sql =
        'SELECT * FROM cat';
    $res = mysqli_query($con, $sql);
    return $cats = mysqli_fetch_all($res, MYSQLI_ASSOC);
}

/**
 * @param array $database_config
 * @return mysqli
 */
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

/**
 * @param $con
 * @return array
 */
function get_lots($con): array
{
    $sql =
        'SELECT * FROM lot ORDER BY created_at DESC';
    $res = mysqli_query($con, $sql);
    return mysqli_fetch_all($res, MYSQLI_ASSOC);
}

/**
 * @param $con
 * @param int $lot_id
 * @return array|bool|null
 */
function get_lot_by_id($con, int $lot_id)
{
    $sql = 'SELECT l.*,
            c.name AS cat_name
            FROM lot l
            JOIN cat c ON c.id = l.cat_id
            WHERE l.id = ' . $lot_id . ';';
    $res = mysqli_query($con, $sql);
    if ($res) {
        return mysqli_fetch_assoc($res);
    }
    return false;
}

/**
 * @param float $cost
 * @return string
 */
function format_cost(float $cost): string
{
    $cost = ceil($cost);
    if ($cost >= 1000) {
        $cost = number_format($cost, 0, ',', ' ');
    }
    return $cost . ' ₽';
}

/**
 * @param $name
 * @param $data
 * @return false|string
 */
function include_template($name, $data)
{
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

/**
 * @param array $cats
 * @param array $lot
 * @return bool
 */
function is_cat_exists(array $cats, array $lot): bool
{
    foreach ($cats as $cat) {
        if ($cat['id'] == $lot['cat_id']) {
            return true;
        }
    }
    return false;
}

/**
 * @param string $date
 * @param string $format
 * @return bool
 */
function is_valid_date_format(string $date, string $format = 'Y-m-d'): bool
{
    $d = DateTime::createFromFormat($format, $date);
    return $d && $d->format($format) == $date;
}

/**
 * @param string $user_date
 * @return bool
 */
function is_valid_date_interval(string $user_date): bool
{
    $user_date = strtotime($user_date);
    $diff = $user_date - strtotime('now');
    $days = floor($diff / 86400);
    if ($days > 0) {
        return true;
    }
    return false;
}

/**
 * @param string $deadline
 * @return string
 */
function time_left(string $deadline): string
{
    $now = date_create('now');
    $deadline = date_create($deadline);
    $diff = date_diff($now, $deadline);
    return date_interval_format($diff, "%H:%I");
}
