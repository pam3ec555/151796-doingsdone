<?php

session_start();

require_once ("functions.php");
require_once ("mysql_helper.php");
require_once ("init.php");

// проверка на параметр logout, если true, то нужно разлогинить пользователя
if (isset($_GET["logout"])) {
    unset($_SESSION["user"]);

    header("Location: index.php");
}

$project_inset = -1;

// проверка на параметр запроса
if (isset($_GET["inset"])) {
    // фильтрация параметра inset
    $project_inset = filter_var($_GET["inset"], FILTER_VALIDATE_INT, ["options" => [
        "min_range" => -1,
        "max_range" => count($projects) - 1
    ]]);

    if ($project_inset || $project_inset === 0) {
        $project_id = $projects[$project_inset]["id"];
    } else {
        pageNotFound();
    }
}

$title = "Главная";

// проверка на параметр логин
$login = null;
if (isset($_GET["login"])) {
    $login = true;
    $title = "Вход";
} else {
    $login = false;
}

// переменная проверяющая, есть ли параметр `add_task`
$add_task = null;

if (isset($_GET["add_task"])) {
    $add_task = true;
    $title = "Добавление задачи";
} else {
    $add_task = false;
}

// переменная проверяющая, есть ли параметр `add_project`
$add_project = null;

if (isset($_GET["add_project"])) {
    $add_project  = true;
    $title = "Добавление проекта";
} else {
    $add_project = false;
}

if (isset($_GET["register"])) {
    $title = "Регистрация";
}

// массив ошибочных полей при отправки формы("ПОЛЬЗОВАТЕЛЬ НЕ СУЩЕСТВУЕТ")
$wrongs = [];

// массив обязательных для заполнения полей
$required = ["name", "project", "deadline", "email", "password"];

// массив требований для правильности заполнений
$rules = ["deadline", "email", "project", "deadline"];

// массив ошибочных полей при отправки пользователем формы
$errors = [];

// валидация формы добавления задачи
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    foreach ($_POST as $key => $value) {

        // если поле обязательное для заполнения и оно пустое или заполнено только пробелами
        if ((in_array($key, $required) && $value === "") || (in_array($key, $required) && !(trim($value)))) {
            $errors[] = $key;
            break;
        }

        // если поле требует проверки на правильность заполнения
        if  (in_array($key, $rules)) {
            $result = null;

            // проверяем тип поля
            switch ($key) {
                case "deadline":
                    $date_value = getDateTimeValue($value);
                    $date_format = getDateTimeFormat($value);
                    $result = validateDate($date_value, $date_format);
                    break;
                case "email":
                    $result = validateEmail($value);
                    break;
                case "project":
                    $result = getProjectsId($value, $projects);
                    break;
            }

            // если поле заполнено правильно
            if (!$result) {
                $errors[] = $key;
            }
        }

        // если пользователь загрузил файл, помещаем его в папку /uploads/
        if (isset($_FILES["preview"])) {
            $file_name = basename($_FILES["preview"]["name"]);
            $file_path = __DIR__ . "/uploads/";
            $file_url = "/uploads/" . $file_name;
            $file_tmp_name = $_FILES["preview"]["tmp_name"];

            move_uploaded_file($file_tmp_name, $file_path . $file_name);
        }
    }

    // проверяем, какая форма отправилась
    switch ($_POST["submit"]) {
        case "Добавить задачу":
            $name = $_POST["name"];
            $deadline = $_POST["deadline"];
            $project_id = getProjectsId($_POST["project"], $projects);

            // если ошибок нет, то добавляем эту задачу в список задач(первым)
            if (!count($errors)) {
                // приводим введенную дату в нужный вид для БД
                $deadline = date("Y.m.d H:i", strtotime(getDateTimeValue($deadline)));
                insertData($link, "tasks", [
                    "task" => $name,
                    "deadline" => $deadline,
                    "project_id" => $project_id,
                    "author_id" => $_SESSION["user"]["id"],
                    "is_complete" => 0,
                    "is_delete" => 0
                ]);
                header("Location: index.php");
            }
            break;
        case "Войти":
            $email = $_POST["email"];
            $password = $_POST["password"];

            // проверяем существование введенного e-mail`а
            if ($user = searchUserByEmail($email, $users)) {
                // проверяем правильность пароля
                if (password_verify($password, $user["password"])) {
                    // аутентификация прошла успешно, сохраняем пользователя в сессии
                    $_SESSION["user"] = $user;
                    header("Location: index.php");
                } else {
                    $wrongs[] = "password";
                }
            } else {
                $wrongs[] = "email";
            }

            break;
        case "Зарегистрироваться":
            $email = $_POST["email"];
            $password = password_hash($_POST["password"], PASSWORD_DEFAULT);
            $name = $_POST["name"];

            if (!searchUserByEmail($email, $users)) {
                if (!$errors) {
                    insertData($link, "users", [
                        "email" => $email,
                        "password" => $password,
                        "name" => $name,
                        "date_of_registration" => date("Y.m.d")
                    ]);
                    header("Location: index.php");
                }
            } else {
                $wrongs[] = "email";
            }
            break;
        case "Добавить проект":
            $name = $_POST["name"];

            if (!$errors) {
                insertData($link, "projects", [
                    "project" => $name,
                    "author_id" => $_SESSION["user"]["id"],
                    "is_delete" => 0
                ]);
                header("Location: index.php");
            }
            break;
        case "Искать":
            $search = trim($_POST["search"]);

            if (!$errors) {
                $tasks = selectData($link, "SELECT * FROM tasks WHERE author_id = "
                    . $_SESSION["user"]["id"]
                    . " AND is_delete = 0 "
                    . $task_deadline_sql
                    . $show_complete_tasks_sql
                    . " AND task LIKE '%" . $search . "%'"
                );
            }

    }
}

renderTemplate(
    "templates/layout.php",
    [
        "projects" => $projects,
        "tasks" => $tasks,
        "title" => $title,
        "add_task" => $add_task,
        "errors" => $errors,
        "project_inset" => $project_inset,
        "project_id" => $project_id,
        "login" => $login,
        "wrongs" => $wrongs,
        "show_complete_tasks" => $show_complete_tasks,
        "task_deadline" => $task_deadline,
        "add_project" => $add_project
    ]
);

