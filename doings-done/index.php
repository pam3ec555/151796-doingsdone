<?php

require_once ("userdata.php");
require_once ("functions.php");
require_once ("mysql_helper.php");
require_once ("init.php");

session_start();

// проверка на параметр logout, если true, то нужно разлогинить пользователя
if (isset($_GET["logout"])) {
    unset($_SESSION["user"]);

    header("index.php");
}

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

// проверка на параметр логин
if (isset($_GET["login"])) {
    $login = true;
} else {
    $login = false;
}

// переменная проверяющая, есть ли параметр `add`
if (isset($_GET["add"])) {
    $add_task = true;
} else {
    $add_task = false;
}

// проверка на галочку(показывать выполненные задания)
if (isset($_GET["show_completed"])) {
    $show_complete_tasks = filter_var($_GET["show_completed"], FILTER_VALIDATE_INT);
    if ($show_complete_tasks == 1) {
        setcookie("show_complete_tasks", true, strtotime("+30 day"), "/");
    } else if ($show_complete_tasks == 0) {
        setcookie("show_complete_tasks", false, strtotime("+30 day"), "/");
    } else {
        return http_response_code(404);
    }
}

// массив ошибочных полей при отправки формы("ПОЛЬЗОВАТЕЛЬ НЕ СУЩЕСТВУЕТ")
$wrongs = [];

// массив обязательных для заполнения полей
$required = ["name", "project", "date", "email", "password"];

// массив требований для правильности заполнений
$rules = ["date", "email"];

// массив ошибочных полей при отправки пользователем формы
$errors = [];

// валидация формы добавления задачи
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    foreach ($_POST as $key => $value) {
        // безопастность введенных данных пользователя от html тегов
        $value = htmlspecialchars($value);

        // если поле обязательное для заполнения и оно пустое или заполнено только пробелами
        if ((in_array($key, $required) && $value === "") || (in_array($key, $required) && !(trim($value)))) {
            $errors[] = $key;
            break;
        }

        // если поле требует проверки на правильность заполнения
        if  (in_array($key, $rules)) {
            // проверяем тип поля
            switch ($key) {
                case "date":
                    $date_value = getDateTimeValue($value);
                    $date_format = getDateTimeFormat($value);
                    $result = validateDate($date_value, $date_format);
                    break;
                case "email":
                    $result = validateEmail($value);
                    break;
            }

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

    // проверяем, какая форма отправилась
    switch ($_POST["submit"]) {
        case "add-task":
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
            break;
        case "login":
            $email = $_POST["email"];
            $password = $_POST["password"];

            // проверяем существование введенного e-mail`а
            if ($user = searchUserByEmail($email, $users)) {
                // проверяем правильность пароля
                if (password_verify($password, $user["password"])) {
                    // аутентификация прошла успешно, сохраняем пользователя в сессии
                    $_SESSION["user"] = $user;
                    header("Location: index.php");
                } else {
                    $wrongs[] = "password";
                }
            } else {
                $wrongs[] = "email";
            }

            // если есть ошибки, то оставляем pop-up и предлагаем исправить ошибки
            if (count($errors) || count($wrongs)) {
                $login = true;
            }
            break;
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
        "project_name" => $project_name,
        "login" => $login,
        "wrongs" => $wrongs,
        "show_complete_tasks" => $show_complete_tasks
    ]
);

