<?php

// имя задачи, отправленной на сервер
$name = $_POST["name"] ?? "";

?>

<div class="modal">
    <button class="modal__close" type="button" name="button">Закрыть</button>

    <h2 class="modal__heading">Добавление проекта</h2>

    <form class="form" action="index.php?add_project" method="POST">
        <div class="form__row">
            <label class="form__label" for="name">Название <sup>*</sup></label>

            <input class="form__input <?php if (in_array("name", $errors)): ?>form__input--error<?php endif; ?>"
                   type="text" name="name" id="name" value="<?=$name ?>"
                   placeholder="Введите название" required>
            <?php if (in_array("name", $errors)): ?>
                <span class="form__error">Заполните поле имя</span>
            <?php endif; ?>
            <?php if (in_array("name", $wrongs)): ?>
                <span class="form__error">Проект с таким именем уже существует</span>
            <?php endif; ?>

        </div>

        <div class="form__row form__row--controls">
            <input class="button" type="submit" name="submit" value="Добавить проект">
        </div>
    </form>
</div>