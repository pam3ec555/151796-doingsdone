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

// массив пользователей взятый из БД
$users = selectData($link, "SELECT * FROM users");

// массив проектов, взятый из БД
$projects = selectData($link, "SELECT * FROM projects");

$tasks = selectData($link, "SELECT * FROM tasks");