<?php

require_once ("functions.php");

// проверка на параметр запроса
if (isset($_GET["index"])) {
    $index = filter_var($_GET["index"], FILTER_VALIDATE_INT, ["options" => [
        "min_range" => 0,
        "max_range" => count($projects) - 1
    ]]);

    if ($index) {
        $project_name = $projects[$index]["name"];
    } else if ($index === 0) {
        $project_name = null;
    } else {
        http_response_code(404);
    }
} else {
    $project_name = null;
}

renderTemplate(
    "templates/layout.php",
    [
        "projects" => $projects,
        "tasks" => $tasks,
        "title" => $title,
        "index" => $index,
        "project_name" => $project_name
    ]
);

