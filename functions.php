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
    $sql = 'INSERT INTO bid (buyer_price, buyer_id, lot_id, created_at) 
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
    $sql = 'INSERT INTO lot (title, description, img_url, cat_id, opening_price, current_price, ends_at, bid_step, author_id, created_at) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())';
    $values = [
        $lot['title'],
        $lot['description'],
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
    $sql = 'INSERT INTO user (email, password, name, contacts, avatar_url, created_at) 
            VALUES (?, ?, ?, ?, ?, NOW())';
    $values = [
        $user['email'] = strtolower($user['email']),
        $user['password'] = password_hash($user['password'], PASSWORD_DEFAULT),
        $user['name'],
        $user['contacts'],
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
    $sql = 'SELECT * FROM cat';
    $res = mysqli_query($con, $sql);
    if ($res) {
        return mysqli_fetch_all($res, MYSQLI_ASSOC);
    }
    return [];
}

/**
 * Возвращает имя категории по переданному ID категории
 *
 * @param mysqli $con Ресурс соединения
 * @param int $cat_id ID категории
 * @return string Строка с именем категории или пустая стока, если массив данных категории пуст
 */
function get_cat_name_by_id(mysqli $con, int $cat_id): string
{
    $sql = 'SELECT name FROM cat WHERE id = ' . $cat_id;
    $res = mysqli_query($con, $sql);
    $cat = mysqli_fetch_all($res, MYSQLI_ASSOC);
    if ($cat) {
        return $cat[0]['name'];
    }
    return '';
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
    $sql = 'SELECT l.*, c.name AS cat_name, COUNT(b.id) AS bids_amount
            FROM lot l
            JOIN cat c ON c.id = l.cat_id
            LEFT JOIN bid b on b.lot_id = l.id
            WHERE l.ends_at > CURDATE()
            GROUP BY l.id
            ORDER BY l.ends_at';
    $res = mysqli_query($con, $sql);
    if ($res) {
        return mysqli_fetch_all($res, MYSQLI_ASSOC);
    }
    return [];
}

/**
 * Возвращает лоты для определенной категории
 *
 * @param mysqli $con Ресурс соединения
 * @param int $cat_id ID категории
 * @return array|null Массив данных лота|Если объект результата не получен, то пустой массив
 */
function get_lots_by_cat_id(mysqli $con, int $cat_id): array
{
    $sql = 'SELECT l.*, c.name AS cat_name, COUNT(b.id) AS bids_amount
            FROM lot l 
            JOIN cat c ON c.id = l.cat_id
            LEFT JOIN bid b ON b.lot_id = l.id 
            WHERE l.cat_id = ? AND l.ends_at > CURDATE()
            GROUP BY l.id
            ORDER BY created_at DESC';
    $values = [$cat_id];
    $lots = db_fetch_data($con, $sql, $values);
    if (!empty($lots)) {
        return $lots;
    }
    return [];
}

/**
 * Возвращает лот с определенным ID
 *
 * @param mysqli $con Ресурс соединения
 * @param int $lot_id ID лота
 * @return array|bool|null Массив данных лота|Если объект результата не получен, то пустой массив
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
    return [];
}

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
 * Проверяет условия для показа блока добавления ставки
 *
 * @param int $user_id ID текущего пользователя
 * @param int $author_id ID автора лота
 * @param array $bids Массив с данными ставок лота
 * @return bool Результат проверки: false - если совпадают ID пользователя и автора лота и если пользователь делал ставку, true - в остальных случаях
 */
function is_bid_block_shown(int $user_id, int $author_id, array $bids): bool
{
    if ($user_id === $author_id) {
        return false;
    }

    foreach ($bids as $bid) {
        if (intval($bid['buyer_id']) === $user_id) {
            return false;
        }
    }
    return true;
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
        if ($cat['id'] === $lot['cat_id']) {
            return true;
        }
    }
    return false;
}

/**
 * Проверяет существование категории с определенным ID
 *
 * @param mysqli $con Ресурс соединения
 * @param int $cat_id ID категории
 * @return bool Результат проверки: true - категория с таким ID существует, false - не существует
 */
function is_cat_id_exists(mysqli $con, int $cat_id): bool
{
    $sql = 'SELECT * FROM cat WHERE id = ?';
    $values = [$cat_id];
    $cat = db_fetch_data($con, $sql, $values);
    if (empty($cat)) {
        return false;
    }
    return true;
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
    $sql = 'SELECT id FROM user ' .
        'WHERE email = ?';
    $values = [$email];
    $user = db_fetch_data($con, $sql, $values);
    if (empty($user)) {
        return false;
    }
    return true;
}

/**
 * Проверяет, истекло ли время торгов по лоту
 *
 * @param string $dt_end Дата окончания торгов по лоту
 * @return bool Результат проверки: true - время истекло, false - время не истекло
 */
function is_time_elapsed(string $dt_end): bool
{
    $now = strtotime('now');
    $dt_end = strtotime($dt_end);
    $diff = $dt_end - $now;
    if ($diff > 0) {
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
    return $d && $d->format($format) === $date;
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
function update_price(mysqli $con, string $new_price, int $lot_id): bool
{
    $new_price = mysqli_real_escape_string($con, $new_price);
    $sql = 'UPDATE lot SET current_price = ' . $new_price .
          ' WHERE id = ' . $lot_id;
    $res = mysqli_query($con, $sql);
    if ($res) {
        return true;
    }
    return false;
}
