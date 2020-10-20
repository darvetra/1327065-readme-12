<?php
// Заголовок
$page_title = 'ReadMe: Популярное';

// Дата и время
date_default_timezone_set("Europe/Moscow");
setlocale(LC_ALL, 'ru_RU'); // устанавливаем в качестве дефолтной локали Россию и Русский язык

// Авторизация
$is_auth = rand(0, 1); // рандомная авторизация
$user_name = 'Угон Харлеев'; // Имя пользователя

// двумерный массив с постами
$posts = [
    [
        'title' => 'Цитата',
        'type' => 'post-quote',
        'content' => 'Мы в жизни любим только раз, а после ищем лишь похожих',
        'author' => 'Лариса',
        'avatar' => 'userpic-larisa-small.jpg',
        'dt' => strtotime("-55 minutes")
    ],
    [
        'title' => 'История компании Honda',
        'type' => 'post-text',
        'content' => 'Соичиро Хонда родился в 1906 году в семье кузнеца. Хотя его отец был мастером в своем деле, семья жила очень бедно. В попытках свести концы с концами Хонда–старший занялся починкой велосипедов, а Соичиро довольно рано стал ему в этом помогать. В школе дела у Соичиро шли не лучшим образом: он ненавидел заучивание и формальности. Окончив школу, Хонда отправился в Токио. Устроившись в фирму «Art Shokai», Хонда надеялся получить практические навыки и познакомиться с устройством автомобиля. Его надежды не оправдались: из–за возраста и небольшого опыта ему чаще поручали присмотреть за младшим сыном владельца фирмы, поубирать и приготовить еду.',
        'author' => 'Владик',
        'avatar' => 'userpic.jpg',
        'dt' => strtotime("-12 hours")
    ],
    [
        'title' => 'Наконец, обработал фотки!',
        'type' => 'post-photo',
        'content' => 'rock-medium.jpg',
        'author' => 'Виктор',
        'avatar' => 'userpic-mark.jpg',
        'dt' => strtotime("-6 days")
    ],
    [
        'title' => 'Моя мечта',
        'type' => 'post-photo',
        'content' => 'coast-medium.jpg',
        'author' => 'Лариса',
        'avatar' => 'userpic-larisa-small.jpg',
        'dt' => strtotime("-3 weeks")
    ],
    [
        'title' => 'Лучшие курсы',
        'type' => 'post-link',
        'content' => 'www.htmlacademy.ru',
        'author' => 'Владик',
        'avatar' => 'userpic.jpg',
        'dt' => strtotime("-36 days")
    ]
];

