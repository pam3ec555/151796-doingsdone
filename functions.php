<?php

/**
 * Метод задающий кол-во проектов определенного типа
 * @param $tasks // Массив с задачами
 * @param $name_of_project // Имя проекта
 * @return int // Кол-во проектов определенного типа
 */
function setProjectsCount($tasks, $project_id) {
    // Счетчик
    $count = 0;

    // Перебираем массив и находим кол-во проектов определенной категории
    foreach ($tasks as $key => $value) {
        if ($value["project_id"] == $project_id) {
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
    $value = mb_convert_case($value, MB_CASE_LOWER, "UTF-8");
    // убираю лишние пробелы и избавляюсь от 'в', так как пользователь может его ввести
    $value = preg_replace(["/  +/", "/ в /"]," ", $value);
    // привожу все значения к одному формату
    $value = preg_replace(["/-/", "/\//"],".", $value);
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
 * @return string|null // правильный вид формата
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
    if ($stmt) {
        if (mysqli_stmt_execute($stmt)) {
            $result = mysqli_stmt_get_result($stmt);

            if ($result) {
                $array = mysqli_fetch_all($result, MYSQLI_ASSOC);
            }
        }
    }

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
    $sql = "";
    $keys = "";
    $values = "";
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
        if ($stmt) {
            if (mysqli_stmt_execute($stmt)) {
                $id_of_last_entry = mysqli_insert_id($link);
            }
        }
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
    $result = false;

    $stmt = db_get_prepare_stmt($link, $sql, $data);
    if ($stmt) {
        $result = mysqli_stmt_execute($stmt);
    }

    return $result;
}

/**
 * Метод, проверяющий существования проекта в массиве и возвращающий его id
 * @param $project string // выбранный/введенный проект
 * @param $projects array // массив проектов
 * @return null|int // id проекта
 */
function getProjectsId($project, $projects) {
    $result = null;

    foreach ($projects as $key => $value) {
        if ($project === $value["project"]) {
            $result = $value["id"];
        }
    }

    return $result;
}

/**
 * Метод, прерывающий загрузку страницы, возвращая ошибку 404
 */
function pageNotFound() {
    http_response_code(404);
    renderTemplate("templates/error.php");
    exit();
}

/**
 * Метод, принимающий на вход значение выбранного дедлайна и выводящий готовый sql запрос для вывода нужного дедлайна
 * @param $task_deadline
 * @return string
 */
function getTaskDeadline($task_deadline) {
    $result = "";

    switch ($task_deadline) {
        case "today":
            $result = "AND deadline < DATE_SUB(NOW(), INTERVAL 0 DAY) AND deadline > DATE_SUB(NOW(), INTERVAL 1 DAY)";
            break;
        case "tomorrow":
            $result = "AND deadline < DATE_SUB(NOW(), INTERVAL -1 DAY) AND deadline > DATE_SUB(NOW(), INTERVAL 0 DAY)";
            break;
        case "past":
            $result = "AND deadline < DATE_SUB(NOW(), INTERVAL 1 DAY)";
            break;
    }

    return $result;
}

