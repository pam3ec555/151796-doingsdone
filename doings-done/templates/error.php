<h1>Ошибка</h1>

<?php if ($sql_error): ?><p><?=$sql_error?></p><?php endif; ?>
<?php if (http_response_code(404)): ?><p>Страница не найдена </p><?php endif; ?>

