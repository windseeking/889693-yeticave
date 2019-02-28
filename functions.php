<?php
require_once('mysql_helper.php');

date_default_timezone_set('Europe/Moscow');

/**
 * Добавляет ставку к лоту с определенным ID
 *
 * @param mysqli $con Ресурс соединения
 * @param int $buyer_price Сумма ставки
 * @param int $user_id ID пользователя, добавившего лот
 * @param int $lot_id ID лота, к которому добавили ставку
 * @return bool Результат добавления ставки: true - добавлена, false - не добавлена
 */
function add_bid(mysqli $con, int $buyer_price, int $user_id, int $lot_id): bool
{
    $sql =
        'INSERT INTO bid (buyer_price, buyer_id, lot_id, created_at) 
        VALUES (?, ?, ?, NOW())';
    $values = [
        $buyer_price,
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
 * Добавляет лот
 *
 * @param mysqli $con Ресурс соединения
 * @param array $lot Массив с данными лота
 * @param int $user_id ID пользователя, добавившего лот
 * @return bool Результат добавления лота: true - добавлен, false - не добавлен
 */
function add_lot(mysqli $con, array $lot, int $user_id): bool
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
 * Добавляет пользователя
 *
 * @param mysqli $con Ресурс соединения
 * @param array $user Массив с данными пользователя
 * @return bool Результат добавления пользователя: true - добавлен, false - не добавлен
 */
function add_user(mysqli $con, array $user): bool
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
 * Убирает теги из строки
 *
 * @param string|null $str Строка с тегами или без|null
 * @return string Строка без тегов
 */
function filter_tags(string $str = null): string
{
    if ($str === null) {
        return '';
    }
    return strip_tags($str);
}


/**
 * Форматирует цену лота
 *
 * @param float $price Цена лота
 * @return string Отформатированная цена и симол рубля
 */
function format_price(float $price = null): string
{
    $price = ceil($price);
    if ($price >= 1000) {
        $price = number_format($price, 0, ',', ' ');
    }
    return $price . ' ₽';
}


/**
 * Возвращает массив с данными ставки для лота с определенным ID
 *
 * @param mysqli $con Ресурс соединения
 * @param int $lot_id ID лота
 * @return array|string|null Массив с данными ставки|Пустой массив, если объект результата пуст
 */
function get_bids_by_lot_id(mysqli $con, int $lot_id): array
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
 * Возвращает массив с данными категорий
 *
 * @param mysqli $con Ресурс соединения
 * @return array|string|null Массив с данными категорий|Пустой массив, если объект результата пуст
 */
function get_cats(mysqli $con): array
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
 * Устанавливает соединение с БД
 *
 * @param array $database_config Массив с данными для подключения к БД
 * @return mysqli $con Ресурс соединения
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
 * Возвращает массив с данными лотов
 *
 * @param mysqli $con Ресурс соединения
 * @return array Массив с данными лотов
 */
function get_lots(mysqli $con): array
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
 * Возвращает лот с определенным ID
 *
 * @param mysqli $con Ресурс соединения
 * @param int $lot_id ID лота
 * @return array|bool|null Массив данных лота|Если объект результата не получен, то false
 */
function get_lot_by_id(mysqli $con, int $lot_id)
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

//function get_pagination_data($con, int $cur_page, int $page_lots)
//{
//    $res = mysqli_query($con, 'SELECT COUNT(*) as lots_amount FROM lot');
//    $lots_count = mysqli_fetch_assoc($res)['lots_amount'];
//
//    $pages_count = ceil($lots_count / $page_lots);
//
//    if ($pages_count <= 1) {
//        return [];
//    } else {
//        $offset = ($cur_page - 1) * $page_lots;
//        $pages = range(1, $pages_count);
//
//        $sql = 'SELECT l.*, c.name FROM lot l '
//            . 'JOIN cat c ON l.cat_id = c.id '
//            . 'ORDER BY l.created_at DESC LIMIT ' . $page_lots . ' OFFSET ' . $offset;
//
//        $res = mysqli_query($con, $sql);
//        if ($res) {
//            $lots = mysqli_fetch_all($res, MYSQLI_ASSOC);
//            return [
//                'pages' => $pages,
//                'cur_page' => $cur_page,
//                'lots' => $lots
//            ];
//        }
//        return [];
//    }
//}

/**
 * Подключает шаблон
 *
 * @param string $name Имя шаблона
 * @param array $data Данные шаблона
 * @return false|string
 */
function include_template(string $name, array $data)
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
 * Проверяет существование категории
 *
 * @param array $cats Массив с данными категории
 * @param array $lot Массив с данными лота
 * @return bool Результат проверки: true - категория найдена, false - категория не найдена
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
 * Проверяет существавание пользователя с определенным email-адресом
 *
 * @param mysqli $con Ресурс соединения
 * @param string $email Адрес пользователя
 * @return bool Результат проверки: true - такой адрес есть в БД, false - такого адреса нет в БД
 */
function is_email_exist(mysqli $con, string $email): bool
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
 * Проверяет дату на соотетствие формату
 *
 * @param string $date
 * @param string $format Нужный формат даты
 * @return bool Результат проверки: true - соответствует формату, false - не соответствует
 */
function is_valid_date_format(string $date, string $format = 'Y-m-d'): bool
{
    $d = DateTime::createFromFormat($format, $date);
    return $d && $d->format($format) == $date;
}

/**
 * Проверяет, больше ли дата текущей минимум на 1 день
 *
 * @param string $user_date Дата, введенная пользователем
 * @return bool Результат проверки: true - больше, false - не больше
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
 * Вычисляет время до окончания торгов
 *
 * @param string $deadline Дата окончания торгов
 * @return string Время до окончания торгов
 */
function time_left(string $deadline): string
{
    $now = date_create('now');
    $deadline = date_create($deadline);
    $diff = date_diff($now, $deadline);
    return date_interval_format($diff, "%dд %Hч %Iм");
}

/**
 * Вычисляет количество времени с момента добавления ставки
 *
 * @param string $date Дата добавления ставки
 * @return false|string Время с момента добавления ставки
 */
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

/**
 * Обновляет текущую цену лота при добавлении ставки к нему
 *
 * @param mysqli $con Ресурс соединения
 * @param string $new_price Новая цена
 * @param int $lot_id ID лота, цена которого обновляется
 * @return bool Результат: true - текущая цена обновлена, false - не обновлена
 */
function update_price(mysqli $con, string $new_price, int $lot_id)
{
    $sql = 'UPDATE lot SET current_price = ' . $new_price .
        ' WHERE id = ' . $lot_id;
    $res = mysqli_query($con, $sql);
    if ($res) {
        return true;
    }
    return false;
}
