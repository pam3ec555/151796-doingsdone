<?php

// ресурс подключения
$link = mysqli_connect("ramil.crm.loc", "root", "123", "doingsdone");

// хранит текст об ошибке
$sql_error = "";

// проверка подключения, если false, то вывести ошибку
if (!$link) {
    $sql_error = mysqli_connect_error();
    renderTemplate("templates/error.php", [
        "sql_error" => $sql_error
    ]);
    exit();
}