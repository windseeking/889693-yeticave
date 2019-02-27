<?php
require_once('mysql_helper.php');

date_default_timezone_set('Europe/Moscow');

/**s
 * @param $con
 * @param int $bid
 * @param int $user_id
 * @param int $lot_id
 * @return bool
 */
function add_bid($con, int $bid, int $user_id, int $lot_id): bool
{
    $sql =
        'INSERT INTO bid (buyer_price, buyer_id, lot_id, created_at) 
        VALUES (?, ?, ?, NOW())';
    $values = [
        $bid,
        $user_id,
        $lot_id
    ];

    $stmt = db_get_prepare_stmt($con, $sql, $values);
    mysqli_stmt_execute($stmt);

    if (mysqli_error($con)) {
        return false;
    }
    return true;
}

/**
 * @param $con
 * @param array $lot
 * @param int $user_id
 * @return bool
 */
function add_lot($con, array $lot, int $user_id): bool
{
    $sql =
        'INSERT INTO lot (title, description, img_url, cat_id, opening_price, current_price, ends_at, bid_step, author_id, created_at) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())';
    $values = [
        $lot['title'] = filter_tags($lot['title']),
        $lot['description'] = filter_tags($lot['description']),
        $lot['img_url'],
        $lot['cat_id'],
        $lot['opening_price'],
        $lot['opening_price'],
        $lot['ends_at'],
        $lot['bid_step'],
        $user_id
    ];
    $stmt = db_get_prepare_stmt($con, $sql, $values);
    mysqli_stmt_execute($stmt);

    if (mysqli_error($con)) {
        return false;
    }
    return true;
}

/**
 * @param $con
 * @param array $user
 * @return bool
 */
function add_user($con, array $user): bool
{
    $sql =
        'INSERT INTO user (email, password, name, contacts, avatar_url, created_at) 
        VALUES (?, ?, ?, ?, ?, NOW())';
    $values = [
        $user['email'] = strtolower($user['email']),
        $user['password'] = password_hash($user['password'], PASSWORD_DEFAULT),
        $user['name'] = filter_tags($user['name']),
        $user['contacts'] = filter_tags($user['contacts']),
        $user['avatar_url']
    ];
    $stmt = db_get_prepare_stmt($con, $sql, $values);
    mysqli_stmt_execute($stmt);

    if (mysqli_error($con)) {
        return false;
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
 * @param float|null $price
 * @return string
 */
function format_price(float $price = null): string
{
    if ($price === null) {
        return '';
    }
    $price = ceil($price);
    if ($price >= 1000) {
        $price = number_format($price, 0, ',', ' ');
    }
    return $price . ' ₽';
}


/**
 * @param $con
 * @param $lot_id
 * @return array|string|null
 */
function get_bids_by_lot_id($con, $lot_id): array
{
    $sql = 'SELECT b.*, u.name as user_name FROM bid b
            JOIN user u ON u.id = b.buyer_id
            WHERE b.lot_id = ' . $lot_id .
          ' ORDER BY b.created_at DESC';
    $res = mysqli_query($con, $sql);
    if ($res) {
        return mysqli_fetch_all($res, MYSQLI_ASSOC);
    }
    return [];
}

/**
 * @param $con
 * @return array|string|null
 */
function get_cats($con): array
{
    $sql =
        'SELECT * FROM cat';
    $res = mysqli_query($con, $sql);
    if ($res) {
        return mysqli_fetch_all($res, MYSQLI_ASSOC);
    }
    return [];
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
    $sql = 'SELECT l.*, COUNT(b.id) AS bids_amount
            FROM lot l
            LEFT JOIN bid b on b.lot_id = l.id
            GROUP BY l.id
            ORDER BY l.ends_at';
    $res = mysqli_query($con, $sql);
    if ($res) {
        return mysqli_fetch_all($res, MYSQLI_ASSOC);
    }
    return [];
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
            WHERE l.id = ' . $lot_id;
    $res = mysqli_query($con, $sql);
    if ($res) {
        return mysqli_fetch_assoc($res);
    }
    return false;
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
 * @param $con
 * @param string $email
 * @return bool
 */
function is_email_exist($con, string $email): bool
{
    $sql =
        'SELECT id FROM user ' .
        'WHERE email = ?';
    $values = [$email];
    $user = db_fetch_data($con, $sql, $values);
    if (empty($user)) {
        return false;
    }
    return true;
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
    return date_interval_format($diff, "%dд %Hч %Iм");
}

function time_passed(string $date)
{
    $diff = strtotime('now') - strtotime($date);
    $now = date_create('now');
    $date = date_create($date);

    if ($diff < 86400 and $diff > 3540) {
        $diff = date_diff($now, $date);
        return date_interval_format($diff, "%hч %iм") . ' назад';
    } elseif ($diff < 60) {
        $diff = date_diff($now, $date);
        return date_interval_format($diff, "%sс") . ' назад';
    } elseif ($diff > 60 and $diff < 86400) {
        $diff = date_diff($now, $date);
        return date_interval_format($diff, "%iм") . ' назад';
    } else {
        return date_format($date, "d.m.Y в H:i");
    }
}

function update_price($con, string $new_price, int $lot_id)
{
    $sql = 'UPDATE lot SET current_price = ' . $new_price .
        ' WHERE id = ' . $lot_id;
    $res = mysqli_query($con, $sql);
    if ($res) {
        return true;
    }
    return false;
}
