<?php

// показывать или нет выполненные задачи
$show_complete_tasks = rand(0, 1);

// устанавливаем часовой пояс в Московское время
date_default_timezone_set("Europe/Moscow");

$SECONDS_PER_DAY = 86400;
$days = rand(-3, 3);
$task_deadline_ts = strtotime("+" . $days . " day midnight"); // метка времени даты выполнения задачи
$current_ts = strtotime("now midnight"); // текущая метка времени

// запишите сюда дату выполнения задачи в формате дд.мм.гггг
$date_deadline = date("d.m.Y", $task_deadline_ts);
// в эту переменную запишите кол-во дней до даты задачи
$days_until_deadline = floor(($task_deadline_ts - $current_ts) / $SECONDS_PER_DAY);
// переменная заголовка страницы
$title = "Главная";

// массив с проектами
//$projects = ["Все", "Входящие", "Учеба", "Работа", "Домашние дела", "Авто"];
$projects = [
    [
        "name" => "Все",
        "link" => "/index.php",
        "tab" => "0"
    ],
    [
        "name" => "Входящие",
        "link" => "/index.php",
        "tab" => "1"
    ],
    [
        "name" => "Учеба",
        "link" => "/index.php",
        "tab" => "2"
    ],
    [
        "name" => "Работа",
        "link" => "/index.php",
        "tab" => "3"
    ],
    [
        "name" => "Домашние дела",
        "link" => "/index.php",
        "tab" => "4"
    ],
    [
        "name" => "Авто",
        "link" => "/index.php",
        "tab" => "5"
    ]
];


// двумерный массив с задачами
$tasks = [
    [
        "task" => "Собеседование в IT компании",
        "date_of_complete" => "01.06.2018",
        "category" => "Работа",
        "is_compete" => false
    ],
    [
        "task" => "Выполнить тестовое задание",
        "date_of_complete" => "25.05.2018",
        "category" => "Работа",
        "is_complete" => false
    ],
    [
        "task" => "Сделать задание первого раздела",
        "date_of_complete" => "21.04.2018",
        "category" => "Учеба",
        "is_complete" => true
    ],
    [
        "task" => "Встреча с другом",
        "date_of_complete" => "22.04.2018",
        "category" => "Входящие",
        "is_complete" => false
    ],
    [
        "task" => "Купить корм для кота",
        "date_of_complete" => "Нет",
        "category" => "Домашние дела",
        "is_complete" => false
    ],
    [
        "task" => "Заказать пиццу",
        "date_of_complete" => "Нет",
        "category" => "Домашние дела",
        "is_complete" => false
    ]
];

/**
 * Метод задающий кол-во проектов определенного типа
 * @param $tasks // Массив с задачами
 * @param $name_of_project // Имя проекта
 * @return int // Кол-во проектов определенного типа
 */
function setProjectsCount($tasks, $name_of_project) {
    // Счетчик
    $count = 0;

    // Проверка на имя проекта, если все, то просто выводим длинну массива с задачами
    if ($name_of_project == "Все") {
        $count = count($tasks);
    } else {
        // Перебираем массив и находим кол-во проектов определенной категории
        foreach ($tasks as $key => $value) {
            if ($value["category"] == $name_of_project)
                $count++;
        }
    }

    return $count;
}

/**
 * Метод для вывода шаблона на страницу
 * @param $template_url // url шаблона
 * @param $data // массив данных, для текущего шаблона
 * @return string // готовый шаблон
 */
function renderTemplate($template_url, $data = false) {
    // Проверка на существование url
    if (!file_exists($template_url)) {
        return "";
    }

    // Буферизация вывода с сжатием
    ob_start("ob_gzhandler");

    // Проверка на существование данных
    if ($data) {
        // Импорт переменных из массива данных в текущую таблицу символов
        extract($data);
    }

    // Вызов шаблона
    require_once ($template_url);

    // Сброс буфера вывода
    ob_get_flush();
}

function getDateDay($value) {
    switch ($value) {
        case "сегодня":
            $value = date("d/m/Y");
            break;
        case "завтра":
            $value = date("d/m/Y", strtotime("+1 day"));
            break;
        case "воскресение" || "вс":
            $value = date("d/m/Y", strtotime("Sunday"));
            break;
        case "понедельник" || "пн":
            $value = date("d/m/Y", strtotime("Monday"));
            break;
        case "вторник" || "вт":
            $value = date("d/m/Y", strtotime("Tuesday"));
            break;
        case "среда" || "ср":
            $value = date("d/m/Y", strtotime("Wednesday"));
            break;
        case "четверг" || "чт":
            $value = date("d/m/Y", strtotime("Thursday"));
            break;
        case "пятница" || "пт":
            $value = date("d/m/Y", strtotime("Friday"));
            break;
        case "суббота" || "сб":
            $value = date("d/m/Y", strtotime("Saturday"));
            break;
    }
    return $value;
}

function getDateFormat($value) {
    // формат по умолчанию
    $format = "d/m/Y";

    switch ($value) {
        case strpos($value, "/"):
            $format = "d/m/Y";
            break;
        case strpos($value, "."):
            $format = "d.m.Y";
            break;
        case strpos($value, "-"):
            $format = "d-m-Y";
            break;
    }
    return $format;
}

function getDateTimeFormat($value) {
    // убираю внешние пробелы
    $value = trim($value);
    // приравниваю строку к нижнему регистру
    $value = mb_strtolower($value);
    // убираю лишние пробелы и избавляюсь от 'в', так как пользователь может его ввести
    $value = preg_replace(["/  +/", "/ в /"]," ", $value);
    // разбиваю строку на пробелы
    $value = explode(" ", $value);

    if (count($value) === 2) {
        $format = getDateFormat(getDateDay($value))." "."h/i";
    } else if (count($value) === 1){
        $format = getDateFormat(getDateDay($value));
    }

    return $format;
}

function validateDate($value, $format) {
    $date = DateTime::createFromFormat($format, $value);
    return $date && $date -> format($format) == $value;
}

validateDate(getDateDay($value), getDateTimeFormat($value));