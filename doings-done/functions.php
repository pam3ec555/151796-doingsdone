<?php

//// показывать или нет выполненные задачи
//$show_complete_tasks = rand(0, 1);
//
//// устанавливаем часовой пояс в Московское время
//date_default_timezone_set("Europe/Moscow");
//
//$SECONDS_PER_DAY = 86400;
//$days = rand(-3, 3);
//$task_deadline_ts = strtotime("+" . $days . " day midnight"); // метка времени даты выполнения задачи
//$current_ts = strtotime("now midnight"); // текущая метка времени
//
//// запишите сюда дату выполнения задачи в формате дд.мм.гггг
//$date_deadline = date("d.m.Y", $task_deadline_ts);
//// в эту переменную запишите кол-во дней до даты задачи
//$days_until_deadline = floor(($task_deadline_ts - $current_ts) / $SECONDS_PER_DAY);
//// переменная заголовка страницы
//$title = "Главная";
//
//// массив с проектами
//$projects = [
//    [
//        "name" => "Все",
//        "link" => "index.php"
//    ],
//    [
//        "name" => "Входящие",
//        "link" => "index.php"
//    ],
//    [
//        "name" => "Учеба",
//        "link" => "index.php"
//    ],
//    [
//        "name" => "Работа",
//        "link" => "index.php"
//    ],
//    [
//        "name" => "Домашние дела",
//        "link" => "index.php"
//    ],
//    [
//        "name" => "Авто",
//        "link" => "index.php"
//    ]
//];
//
//// двумерный массив с задачами
//$tasks = [
//    [
//        "task" => "Собеседование в IT компании",
//        "date_of_complete" => "01.06.2018",
//        "category" => "Работа",
//        "is_compete" => false
//    ],
//    [
//        "task" => "Выполнить тестовое задание",
//        "date_of_complete" => "25.05.2018",
//        "category" => "Работа",
//        "is_complete" => false
//    ],
//    [
//        "task" => "Сделать задание первого раздела",
//        "date_of_complete" => "21.04.2018",
//        "category" => "Учеба",
//        "is_complete" => true
//    ],
//    [
//        "task" => "Встреча с другом",
//        "date_of_complete" => "22.04.2018",
//        "category" => "Входящие",
//        "is_complete" => false
//    ],
//    [
//        "task" => "Купить корм для кота",
//        "date_of_complete" => "Нет",
//        "category" => "Домашние дела",
//        "is_complete" => false
//    ],
//    [
//        "task" => "Заказать пиццу",
//        "date_of_complete" => "Нет",
//        "category" => "Домашние дела",
//        "is_complete" => false
//    ]
//];

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

    // Буферизация вывода
    ob_start();

    // Проверка на существование данных
    if ($data) {
        // Импорт переменных из массива данных в текущую таблицу символов
        extract($data);
    }

    // Вызов шаблона
    require_once($template_url);

    // Сброс буфера вывода
    ob_get_flush();
}

/**
 * Метод который получает на вход первую часть строки, и если строка -> слово, то идет проверка по ключевым словам
 * @param $value // значение
 * @return false|string
 */
function getDateDay($value) {

    switch ($value) {
        case "сегодня":
            $value = date("d.m.Y");
            break;
        case "завтра":
            $value = date("d.m.Y", strtotime("+1 day"));
            break;
        case "послезавтра":
            $value = date("d.m.Y", strtotime("+2 day"));
            break;
        case "воскресение":
            $value = date("d.m.Y", strtotime("Sunday"));
            break;
        case "понедельник":
            $value = date("d.m.Y", strtotime("Monday"));
            break;
        case "вторник":
            $value = date("d.m.Y", strtotime("Tuesday"));
            break;
        case "среда":
            $value = date("d.m.Y", strtotime("Wednesday"));
            break;
        case "четверг":
            $value = date("d.m.Y", strtotime("Thursday"));
            break;
        case "пятница":
            $value = date("d.m.Y", strtotime("Friday"));
            break;
        case "суббота":
            $value = date("d.m.Y", strtotime("Saturday"));
            break;
    }

    return $value;
}

/**
 * Метод, который преобразует значение строки в нужный вид для вычислений
 * @param $value // значение
 * @return array|mixed|string
 */
function getDateValConversion($value) {
    // убираю внешние пробелы
    $value = trim($value);
    // приравниваю строку к нижнему регистру
    $value = strtolower($value);
    // убираю лишние пробелы и избавляюсь от 'в', так как пользователь может его ввести
    $value = preg_replace(["/  +/", "/ в /"]," ", $value);
    // привожу все значения к одному формату
    $value = preg_replace(["/-/", "/\//"],".", $value);
    var_dump($value);
    // разбиваю строку на пробелы
    $value = explode(" ", $value);

    return $value;
}

/**
 * Метод, принимающий на вход значение даты и приводящий его в правильный вид
 * @param $value // значение
 * @return array|false|mixed|string
 */
function getDateTimeValue($value) {
    $value = getDateValConversion($value);

    if (count($value) === 2) {
        $value = getDateDay($value[0])." ".$value[1];
    } else if (count($value) === 1){
        $value = getDateDay($value[0]);
    }

    return $value;
}

/**
 * Метод, принимающий на вход значение формата и преобразовывает его в правильный вид
 * @param $value // значение
 * @return string // правильный вид формата
 */
function getDateTimeFormat($value) {
    $value = getDateValConversion($value);
    $format = null;

    if (count($value) === 2) {
        $format = "d.m.Y H:i";
    } else if (count($value) === 1){
        $format = "d.m.Y";
    }

    return $format;
}

/**
 * Метод, проверяющий валидность даты
 * @param $value // значение
 * @param $format // формат даты
 * @return bool // валидность
 */
function validateDate($value, $format) {
    $date = DateTime::createFromFormat($format, $value);
    return $date && $date -> format($format) == $value;
}

/**
 * Метод, проверяющий валидность e-mail
 * @param $value
 * @return bool
 */
function validateEmail($value) {
    return filter_var($value, FILTER_VALIDATE_EMAIL);
}

/**
 * Метод, сравнивающий e-mail`ы
 * @param $email
 * @param $users
 * @return array|null
 */
function searchUserByEmail($email, $users) {
    $result = null;

    foreach ($users as $user) {
        if ($user["email"] === $email) {
            $result = $user;
            break;
        }
    }

    return $result;
}

/**
 * Метод, для получения данных, возвращающий массив данных из БД
 * @param $link // ресурс соединения
 * @param $sql // SQL-запрос с плейсхолдерами (знаками ?) на всех переменных значений
 * @param array $data // [необязательный аргумент] простой массив со всеми значениями для запроса.
 * @return array // массив данных из БД
 */
function selectData($link, $sql, $data = []) {
    $array = [];

    $stmt = db_get_prepare_stmt($link, $sql, $data);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $array = mysqli_fetch_all($result, MYSQLI_ASSOC);

    return $array;
}

/**
 * Метод, для вставки данных в таблицу, возвращающий id последней добавленной записи
 * @param $link // ресурс соединения
 * @param $table // имя таблицы, в которую добавляются данные
 * @param $data // [необязательный аргумент] простой массив со всеми значениями для запроса.
 * @return int // id последней добавленной записи
 */
function insertData($link, $table, $data) {
    $sql = null;
    $keys = null;
    $values = null;
    $id_of_last_entry = false;

    // формируем SQL-запрос
    if (count($data)) {
        foreach ($data as $key => $value) {
            $keys .= $key . ", ";
            $values .= "?, ";
        }

        if ($keys && $values) {
            $keys = substr($keys, 0, -2);
            $values = substr($values, 0, -2);
            $sql = "INSERT INTO " . $table . " (" . $keys . ") " . "VALUES " . "(" . $values . ")";
        }
    }

    if ($sql) {
        $stmt = db_get_prepare_stmt($link, $sql, $data);
        mysqli_stmt_execute($stmt);
        $id_of_last_entry = mysqli_insert_id($link);
    }

    return $id_of_last_entry;
}

/**
 * Метод, выполняющий произвольный запрос(UPDATE, DELETE, ...) и возвращающий true, в случае успеха и false, в случае ошибки
 * @param $link // ресурс соединения
 * @param $sql // SQL-запрос с плейсхолдерами (знаками ?) на всех переменных значений
 * @param array $data // [необязательный аргумент] простой массив со всеми значениями для запроса
 * @return bool
 */
function execQuery($link, $sql, $data = []) {
    $stmt = db_get_prepare_stmt($link, $sql, $data);
    $result = mysqli_stmt_execute($stmt);

    return $result;
}

