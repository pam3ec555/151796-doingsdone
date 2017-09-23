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

$task_deadline_sql = "";
$task_deadline = "all";

if (isset($_GET["task_deadline"])) {
    $task_deadlines = [
        "all",
        "today",
        "tomorrow",
        "past"
    ];

    if (in_array($_GET["task_deadline"], $task_deadlines)) {
        $task_deadline = $_GET["task_deadline"];
        switch ($task_deadline) {
            case "today":
                $task_deadline_sql = "AND deadline < DATE_SUB(NOW(), INTERVAL 0 DAY) AND deadline > DATE_SUB(NOW(), INTERVAL 1 DAY)";
                break;
            case "tomorrow":
                $task_deadline_sql = "AND deadline < DATE_SUB(NOW(), INTERVAL -1 DAY) AND deadline > DATE_SUB(NOW(), INTERVAL 0 DAY)";
                break;
            case "past":
                $task_deadline_sql = "AND deadline < DATE_SUB(NOW(), INTERVAL 1 DAY)";
                break;
        }
    } else {
        return http_response_code(404);
    }
}

// массив пользователей взятый из БД
$users = selectData($link, "SELECT * FROM users");

// массив проектов, взятый из БД
$projects = selectData($link, "SELECT * FROM projects");

// массив задач конкретного пользователя взятый из БД
$tasks = selectData($link, "SELECT * FROM tasks WHERE author_id = "
    . $_SESSION["user"]["id"]
    . " AND is_delete = 0 "
    . $task_deadline_sql);