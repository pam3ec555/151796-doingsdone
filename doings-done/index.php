<?php

session_start();

require_once ("functions.php");
require_once ("../vendor/autoload.php");
require_once ("mysql_helper.php");
require_once ("init.php");

// проверка на параметр logout, если true, то нужно разлогинить пользователя
if (isset($_GET["logout"])) {
    unset($_SESSION["user"]);

    header("Location: index.php");
}

$show_complete_tasks = null;

if (isset($_COOKIE["show_complete_tasks"])) {
    $show_complete_tasks = $_COOKIE["show_complete_tasks"];
}

// проверка на галочку(показывать выполненные задания)
if (isset($_GET["show_completed"])) {
    $show_complete_tasks = filter_var($_GET["show_completed"], FILTER_VALIDATE_INT);

    if ($show_complete_tasks == 1) {
        setcookie("show_complete_tasks", true, strtotime("+30 day"), "/");
    } else if ($show_complete_tasks == 0) {
        setcookie("show_complete_tasks", false, strtotime("+30 day"), "/");
    } else {
        pageNotFound();
    }
}

$show_complete_tasks_sql = "";

if ($show_complete_tasks == 0) {
    $show_complete_tasks_sql = " AND is_complete = 0";
}

$task_deadline_sql = "";

if (isset($_COOKIE["task_deadline"])) {
    $task_deadline = $_COOKIE["task_deadline"];
    $task_deadline_sql = getTaskDeadline($task_deadline);
} else {
    $task_deadline = "all";
}

if (isset($_GET["task_deadline"])) {
    $task_deadlines = [
        "all",
        "today",
        "tomorrow",
        "past"
    ];

    if (in_array($_GET["task_deadline"], $task_deadlines)) {
        $task_deadline = $_GET["task_deadline"];
        setcookie("task_deadline", $task_deadline, strtotime("+30 day"), "/");

        $task_deadline_sql = getTaskDeadline($task_deadline);
    } else {
        pageNotFound();
    }
}

// проверка на смену статуса задачи выполнена/не выполнена
if (isset($_GET["task_complete"])) {
    $task_complete = filter_var($_GET["task_complete"], FILTER_VALIDATE_INT);

    if ($task_complete) {
        $is_complete = selectData($link, "SELECT is_complete FROM tasks WHERE author_id = "  . $_SESSION["user"]["id"] . " AND id = " . $task_complete);

        // если до нажатия на чекбокс, задача была выполнена, то убираем дату выполнения с бд
        if ($is_complete[0]["is_complete"]) {
            $date_complete = "NULL";
        } else {
            $date_complete = "NOW()";
        }

        $sql = "UPDATE tasks SET is_complete = !is_complete, date_complete = " . $date_complete . " WHERE id = " . $task_complete;

        execQuery($link, $sql);

        header("Location: index.php");
    } else {
        pageNotFound();
    }
}

if (isset($_GET["task_delete"])) {
    $task_delete = filter_var($_GET["task_delete"], FILTER_VALIDATE_INT);

    if ($task_delete) {
        $sql = "UPDATE tasks SET is_delete = 1 WHERE id = " . $task_delete;

        execQuery($link, $sql);

        header("Location: index.php");
    } else {
        pageNotFound();
    }
}

if (isset($_GET["task_copy"])) {
    $task_copy = filter_var($_GET["task_copy"], FILTER_VALIDATE_INT);

    if ($task_copy) {
        // формируем запрос для получения выбранной задачи
        $sql = "SELECT task, file_url, file_name, deadline, project_id FROM tasks WHERE id = " . $task_copy;

        $cur_task = selectData($link, $sql);
        $cur_task = $cur_task[0];

        if ($cur_task && $cur_task["file_url"] && $cur_task["file_name"]) {
            insertData($link, "tasks", [
                "date_create" => date("Y.m.d H:i"),
                "task" => $cur_task["task"],
                "file_url" => $cur_task["file_url"],
                "file_name" => $cur_task["file_name"],
                "deadline" => $cur_task["deadline"],
                "project_id" => $cur_task["project_id"],
                "author_id" => $_SESSION["user"]["id"],
                "is_complete" => 0,
                "is_delete" => 0
            ]);
        } else if ($cur_task) {
            insertData($link, "tasks", [
                "date_create" => date("Y.m.d H:i"),
                "task" => $cur_task["task"],
                "deadline" => $cur_task["deadline"],
                "project_id" => $cur_task["project_id"],
                "author_id" => $_SESSION["user"]["id"],
                "is_complete" => 0,
                "is_delete" => 0
            ]);
        }
    } else {
        pageNotFound();
    }
}

// массив пользователей взятый из БД
$users = selectData($link, "SELECT * FROM users");

// массив проектов, взятый из БД
$projects = selectData($link, "SELECT * FROM projects WHERE author_id = "
    . $_SESSION["user"]["id"]
    . " AND is_delete = 0 "
);

// массив задач конкретного пользователя взятый из БД
$tasks = selectData($link, "SELECT * FROM tasks WHERE author_id = "
    . $_SESSION["user"]["id"]
    . " AND is_delete = 0 "
    . $task_deadline_sql
    . $show_complete_tasks_sql
);

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

// ссылка для скачивания файла
$file_url = null;

// имя файла
$file_name = null;

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
            $file_name = $_FILES["preview"]["name"];
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

                if ($file_url && $file_name) {
                    insertData($link, "tasks", [
                        "date_create" => date("Y.m.d H:i"),
                        "task" => $name,
                        "file_url" => $file_url,
                        "file_name" => $file_name,
                        "deadline" => $deadline,
                        "project_id" => $project_id,
                        "author_id" => $_SESSION["user"]["id"],
                        "is_complete" => 0,
                        "is_delete" => 0
                    ]);
                } else {
                    insertData($link, "tasks", [
                        "date_create" => date("Y.m.d H:i"),
                        "task" => $name,
                        "deadline" => $deadline,
                        "project_id" => $project_id,
                        "author_id" => $_SESSION["user"]["id"],
                        "is_complete" => 0,
                        "is_delete" => 0
                    ]);
                }
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

