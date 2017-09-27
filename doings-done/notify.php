<?php

require_once 'mysql_helper.php';
require_once 'init.php';
require_once 'functions.php';
require_once ("vendor/autoload.php");

$sql = "SELECT email, users.id as u_id, users.name, DATE_FORMAT(deadline, '%d.%m.%Y %H:%i') as deadline, task, is_complete, is_delete"
        . " FROM tasks"
        . " LEFT JOIN users ON users.id = tasks.author_id"
        . " WHERE is_complete = 0 AND is_delete = 0 AND TIMESTAMPDIFF(MINUTE, now(), deadline) > 0"
        . " AND TIMESTAMPDIFF(MINUTE, now(), deadline) <= 60";

$data = selectData($link, $sql);
$mails = [];
$transport = (new Swift_SmtpTransport('smtp.mail.ru', 465, 'ssl'))
    ->setUsername('doingsdone@mail.ru')
    ->setPassword('rds7BgcL');
$mailer = new Swift_Mailer($transport);

foreach ($data as $key => $value) {
    if (!array_key_exists($value["u_id"], $mails)) {
        $mails[$value["u_id"]]["body"] = "Уважаемый, " . $value["name"] . ". У вас запланирована задача " . $value["task"] . " на " . $value["deadline"];
        $mails[$value["u_id"]]["head"] = "Уведомление от сервиса «Дела в порядке»";
        $mails[$value["u_id"]]["email"] = $value["email"];
        $mails[$value["u_id"]]["name"] = $value["name"];
    } else {
        $mails[$value["u_id"]]["body"] .= " задача " . $value["task"] . " на  " . $value["deadline"];
    }
}

foreach ($mails as $mail => $value) {
    $message = new Swift_Message();
    $message->setTo([$value["email"] => $value["name"]]);
    $message->setSubject($value["head"]);
    $message->setBody($value["body"]);
    $message->setFrom(['doingsdone@mail.ru' => 'DoingsDone']);

    $mailer->send($message);
}