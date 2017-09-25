<?php

// ресурс подключения
$link = mysqli_connect("ramil.crm.loc", "root", "123", "doingsdone");

// проверка подключения, если false, то вывести ошибку
if (!$link) {
    $sql_error = mysqli_connect_error();
    renderTemplate("templates/error.php", [
        "sql_error" => $sql_error
    ]);
    exit();
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

        $sql = "UPDATE tasks SET is_complete = !is_complete, date_complete = " . $date_complete . " WHERE author_id = " . $_SESSION["user"]["id"] . " AND id = " . $task_complete;

        execQuery($link, $sql);

        header("Location: index.php");
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