<?php
/**
 * Функция обрезания текста.
 * Используется при выводе списка постов.
 * Обрезает слишком длинный текст и добавляет ссылку на полную страницу статьи
 *
 * @param $text
 * @param int $symbols
 * @return string
 */
function cut_text($text, $symbols = 300)
{
    if (mb_strlen($text) > $symbols) {
        // разбиваем текст на слова при помощи пробелов
        $gap_count = explode(" ", $text); // текст как массив, без пробелов
        // вводим переменные для цикла
        $text_length = 0;
        $i = 0;
        // запускаем цикл для перебора массива для подсчета общего количества символов в словах
        while ($text_length < $symbols) {
            $text_length += mb_strlen($gap_count[$i]); //счетчик длины слов
            $i++;
        }
        // склеиваем массив снова
        $text = implode(" ", array_slice($gap_count, 0, $i)) . '... <a class="post-text__more-link" href="#">Читать далее</a>';
    }
    return $text;
}

/**
 * Функция-шаблонизатор
 * 1. Проверяет наличие файла
 * 2. Работает исключительно через буфер
 * 3. extract импортирует переменные из массива в текущую таблицу символов,
 * 4. Не использовать с непроверенными данными
 * 5. Применяем htmlspecialchars
 *
 * @param $path - Наименование файла
 * @param array $data - Массив с данными для вывода на страницу
 * @return false|string
 */
function include_template($path, array $data = [])
{
    $path = 'templates/' . $path . '.php';

    if (!is_readable($path)) {
        return 'Шаблон не найден: [' . $path . ']';
    }

    ob_start();
    htmlspecialchars(extract($data));
    require_once $path;
    return ob_get_clean();
}

/**
 * Возвращает корректную форму множественного числа
 * Ограничения: только для целых чисел
 *
 * Пример использования:
 * $remaining_minutes = 5;
 * echo "Я поставил таймер на {$remaining_minutes} " .
 *     get_noun_plural_form(
 *         $remaining_minutes,
 *         'минута',
 *         'минуты',
 *         'минут'
 *     );
 * Результат: "Я поставил таймер на 5 минут"
 *
 * @param int $number Число, по которому вычисляем форму множественного числа
 * @param string $one Форма единственного числа: яблоко, час, минута
 * @param string $two Форма множественного числа для 2, 3, 4: яблока, часа, минуты
 * @param string $many Форма множественного числа для остальных чисел
 *
 * @return string Рассчитанная форма множественнго числа
 */
function get_noun_plural_form(int $number, string $one, string $two, string $many): string
{
    $number = (int)$number;
    $mod10 = $number % 10;
    $mod100 = $number % 100;

    switch (true) {
        case ($mod100 >= 11 && $mod100 <= 20):
            return $many;

        case ($mod10 > 5):
            return $many;

        case ($mod10 === 1):
            return $one;

        case ($mod10 >= 2 && $mod10 <= 4):
            return $two;

        default:
            return $many;
    }
}

/**
 * Функция работы с датой. Принимает unix timestamp и вовзвращает дату в "человеческом" виде
 *
 * @param $timestamp
 * @param $format
 * @return false|string
 */
function show_date($time, $format)
{
    $timestamp = strtotime($time);
    $dt = date_create();
    $dt = date_timestamp_set($dt, $timestamp);
    $current_timestamp = time();

    //преобразование формата даты
    switch ($format) {
        case 'datetime_format':
            $format_timestamp = date_format($dt, DATETIME_FORMAT);
            break;
        case 'title_format':
            $format_timestamp = date_format($dt, TITLE_FORMAT);
            break;
        case 'relative_format':
            if ($timestamp + SIXTY_MINUTES > $current_timestamp) { // до 60 минут
                $remaining_minutes = ceil(($current_timestamp - $timestamp) / SIXTY_SECONDS);
                $format_timestamp = $remaining_minutes . get_noun_plural_form(
                        $remaining_minutes,
                        ' минута',
                        ' минуты',
                        ' минут'
                    );
            } elseif ($timestamp + SIXTY_MINUTES <= $current_timestamp && $timestamp + TWENTY_FOUR_HOURS > $current_timestamp) { // от 60 минут до 24 часов
                $remaining_hours = ceil(($current_timestamp - $timestamp) / SIXTY_MINUTES);
                $format_timestamp = $remaining_hours . get_noun_plural_form(
                        $remaining_hours,
                        ' час',
                        ' часа',
                        ' часов'
                    );
            } elseif ($timestamp + TWENTY_FOUR_HOURS <= $current_timestamp && $timestamp + SEVEN_DAYS > $current_timestamp) { // от 24 часов но меньше 7 дней
                $remaining_days = ceil(($current_timestamp - $timestamp) / TWENTY_FOUR_HOURS);
                $format_timestamp = $remaining_days . get_noun_plural_form(
                        $remaining_days,
                        ' день',
                        ' дня',
                        ' дней'
                    );
            } elseif ($timestamp + SEVEN_DAYS <= $current_timestamp && $timestamp + FIVE_WEEKS > $current_timestamp) { // от 7 дней но меньше 5 недель
                $remaining_weeks = ceil(($current_timestamp - $timestamp) / SEVEN_DAYS);
                $format_timestamp = $remaining_weeks . get_noun_plural_form(
                        $remaining_weeks,
                        ' неделя',
                        ' недели',
                        ' недель'
                    );
            } elseif ($timestamp + FIVE_WEEKS <= $current_timestamp) { // больше 5 недель
                $remaining_months = ceil(($current_timestamp - $timestamp) / FIVE_WEEKS);
                $format_timestamp = $remaining_months . get_noun_plural_form(
                        $remaining_months,
                        ' месяц',
                        ' месяца',
                        ' месяцев'
                    );
            }
            break;
    }

    return $format_timestamp;
}

/**
 * Функция выполняющая запросы к БД
 * @param $sql - запрос к БД
 * @return array|int
 */
function requestDataBase($sql, $type)
{
    $connect = mysqli_connect("localhost", "root", "root", "readme");
    mysqli_set_charset($connect, "utf8");

    if (!$connect) {
        $error = mysqli_connect_error();
        $answerDataBase = print("Ошибка MySQL: " . $error);
    } else {

        if ($result = mysqli_query($connect, $sql)) {
            switch ($type) {
                case 'all':
                    $answerDataBase = mysqli_fetch_all($result, MYSQLI_ASSOC);
                    break;
                case 'row':
                    $answerDataBase = mysqli_fetch_row($result)[0];
                    break;
                case 'num':
                    $answerDataBase = mysqli_num_rows($result);
                    break;
            }
        } else {
            $error = mysqli_error($connect);
            $answerDataBase = print("Ошибка MySQL: " . $error);
        }
    }
    return $answerDataBase;
}

/**
 * Вызов ошибки 404
 * @param $is_auth
 * @param $user_name
 */
function open_404_page($is_auth, $user_name)
{
    $getPage404 = include_template('page-404');
    $getLayout = include_template('layout', [
        'page_title' => 'ReadMe: Страница не найдена',
        'contentPage' => $getPage404,
        'is_auth' => $is_auth,
        'user_name' => $user_name
    ]);

    print ($getLayout);
    http_response_code(404);
    die();
}

// Эмуляция даты и преобразование формата
function generate_random_date($current_timestamp)
{
    //эмуляция рандомной даты в заданном диапазоне
    $previous_timestamp = $current_timestamp - 36288; // 3628800; // 42 дня
    $random_timestamp = rand($previous_timestamp, $current_timestamp);
    return $random_timestamp;
}
