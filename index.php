<?php

require_once ('functions.php');

// проверка на параметр запроса
if (isset($_GET['tab'])) {
    $tab = $_GET['tab'];
    $is_tab = false;

    foreach ($projects as $key => $value) {
        if ($value['tab'] == $tab) {
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

//if ($tab <)

renderTemplate(
    'templates/layout.php',
    [
        'projects' => $projects,
        'tasks' => $tasks,
        'title' => $title,
        'tab' => $tab
    ]
);
