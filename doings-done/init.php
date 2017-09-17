<?php

$con = mysqli_connect("ramil.crm.loc", "root", "123", "doingsdone");

if (!$con) {
    $sql_error = mysqli_connect_error();
    renderTemplate("templates/error.php", [
        "sql_error" => $sql_error
    ]);
    exit();
}