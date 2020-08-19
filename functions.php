<?php

// Функция обрезания текста на главной странице. Обрезает слишком длинный текст и добавляет ссылку на полную страницу статьи
function cut_text($text, $symbols = 300) {
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

// Функция-шаблонизатор
function include_template($path, array $data = [])
{
    $path = 'templates/' . $path . '.php';

    // проверка наличия файла и доступа к нему
    if (!is_readable($path)) {
        return 'Шаблон не найден: [' . $path . ']';
    }

    // Включение буферизации вывода
    ob_start();

    // extract импортирует переменные из массива в текущую таблицу символов, вот только что за таблица символов такая, не использовать с непроверенными данными,поэтому применяем htmlspecialchars
    htmlspecialchars(extract($data));
    require_once $path;

    // Получить содержимое текущего буфера и удалить его
    return ob_get_clean();
}
