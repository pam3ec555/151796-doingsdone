<?php

require_once ("functions.php");

// проверка на параметр запроса
if (isset($_GET["inset"])) {
    $project_inset = filter_var($_GET["inset"], FILTER_VALIDATE_INT, ["options" => [
        "min_range" => 0,
        "max_range" => count($projects) - 1
    ]]);

    if ($project_inset || $project_inset === 0) {
        $project_name = $projects[$project_inset]["name"];
    } else {
        return http_response_code(404);
    }
} else {
    $project_name = $projects[0]["name"];
    $project_inset = 0;
}

renderTemplate(
    "templates/layout.php",
    [
        "projects" => $projects,
        "tasks" => $tasks,
        "title" => $title,
        "project_inset" => $project_inset,
        "project_name" => $project_name
    ]
);

