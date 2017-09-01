<?php

require_once ('functions.php');

renderTemplate(
    'templates/layout.php',
    [
        'projects' => $projects,
        'tasks' => $tasks,
        'title' => $title
    ]
);
