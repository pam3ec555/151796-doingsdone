<?php

require_once ("functions.php");

// проверка на параметр запроса
if (isset($_GET["tab"])) {
    // параметр `tab`
    $tab = $_GET["tab"];
    // переменная, проверяющая существование введенного `tab`
    $is_tab = false;

    foreach ($projects as $key => $value) {
        if ($value["tab"] == $tab) {
            $is_tab = true;
            break;
        }
    }

    if (!$is_tab) {
        http_response_code(404);
    }
} else {
    $tab = 0;
}

// переменная проверяющая, есть ли параметр `add`
//$add_task = isset($_GET["add"]) ? true : false;

if (isset($_GET["add"])) {
    $add_task = true;
} else {
    $add_task = false;
}

// имя задачи, отправленной на сервер
$task_name = $_POST["task-name"] ?? "";

// дата задачи, отправленной на сервер
$task_date = $_POST["task-date"] ?? "";

// проект задачи, отправленной на сервер
$task_project = $_POST["task-project"] ?? "";

// файл(-ы) задачи, отправленной(-ые) на сервер
$task_file = $_POST["task-file"] ?? "";

// массив обязательных для заполнения полей
$task_required = ["name", "project", "date"];

// массив требований для правильности заполнений
$task_rules = ["date"];

// массив ошибочных полей при отправки пользователем формы
$task_errors = $task_errors || [];


// валидация формы добавления задачи
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    foreach ($_POST as $key => $value) {
        // если поле обязательное для заполнения и оно пустое
        if (in_array($key, $task_required) && $value == "") {
            $task_errors[] = $key;
            break;
        }

        // если поле требует проверки на правильность заполнения
        if  (in_array($key, $task_rules)) {
            $date_value = getDateTimeValue($value);
            $date_format = getDateTimeFormat($value);

            $result = validateDate($date_value, $date_format);

            // если поле заполнено правильно
            if (!$result) {
                $task_errors[] = $key;
            }
        }
    }
}

// если нет ошибок
//if (!count($errors)) {
//    header("Location: index.php");
//}



renderTemplate(
    "templates/layout.php",
    [
        "projects" => $projects,
        "tasks" => $tasks,
        "title" => $title,
        "tab" => $tab,
        "add_task" => $add_task,
        "task_errors" => $task_errors,
        "task_name" => $task_name,
        "task_date" => $task_date,
        "task_project" => $task_project,
        "task_file" => $task_file
    ]
);
