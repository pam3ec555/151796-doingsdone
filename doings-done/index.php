<?php

require_once ("functions.php");

// проверка на параметр запроса
if (isset($_GET["inset"])) {
    // фильтрация параметра inset
    $project_inset = filter_var($_GET["inset"], FILTER_VALIDATE_INT, ["options" => [
        "min_range" => 0,
        "max_range" => count($projects) - 1
    ]]);

    if ($project_inset || $project_inset === 0) {
        $project_name = $projects[$project_inset]["name"];
    } else {
        // возвращаем ошибку 404 если параметр inset имеет несуществующее значение
        return http_response_code(404);
    }
} else {
    $project_name = $projects[0]["name"];
    $project_inset = 0;
}

// переменная проверяющая, есть ли параметр `add`
if (isset($_GET["add"])) {
    $add_task = true;
} else {
    $add_task = false;
}

// массив обязательных для заполнения полей
$task_required = ["name", "project", "date"];

// массив требований для правильности заполнений
$task_rules = ["date"];

// массив ошибочных полей при отправки пользователем формы
$errors = [];

// валидация формы добавления задачи
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    foreach ($_POST as $key => $value) {
        // безопастность введенных данных пользователя от html тегов
        $value = htmlspecialchars($value);

        // если поле обязательное для заполнения и оно пустое или заполнено только пробелами
        if ((in_array($key, $task_required) && $value == "") || (in_array($key, $task_required) && !(trim($value)))) {
            $errors[] = $key;
            break;
        }

        // если поле требует проверки на правильность заполнения
        if  (in_array($key, $task_rules)) {
            $date_value = getDateTimeValue($value);
            $date_format = getDateTimeFormat($value);

            $result = validateDate($date_value, $date_format);

            // если поле заполнено правильно
            if (!$result) {
                $errors[] = $key;
            }
        }

        // если пользователь загрузил файл, помещаем его в папку /uploads/
        if (isset($_FILES["preview"])) {
            $file_name = basename($_FILES["preview"]["name"]);
            $file_path = __DIR__ . "/uploads/";
            $file_url = "/uploads/" . $file_name;
            $file_tmp_name = $_FILES["preview"]["tmp_name"];

            move_uploaded_file($file_tmp_name, $file_path . $file_name);
        }
    }

    //если есть ошибки, то не закрываем форму
    if (count($errors)) {
        $add_task = true;
    } else {
        // если ошибок нет, то добавляем эту задачу в список задач(первым)
        array_unshift($tasks, [
            "task" => $_POST["name"],
            "date_of_complete" => $_POST["date"],
            "category" => $_POST["project"],
            "is_complete" => false
        ]);
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
        "project_name" => $project_name
    ]
);
