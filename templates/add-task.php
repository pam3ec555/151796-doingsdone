<?php

// имя задачи, отправленной на сервер
$name = $_POST["name"] ?? "";

// дата задачи, отправленной на сервер
$date = $_POST["date"] ?? "";

// проект задачи, отправленной на сервер
$project = $_POST["project"] ?? "";

?>

<div class="modal">
    <button class="modal__close" type="button" name="button">Закрыть</button>

    <h2 class="modal__heading">Добавление задачи</h2>

    <form class="form" action="index.php?add_task" method="POST" enctype="multipart/form-data">
        <div class="form__row">
            <label class="form__label" for="name">Название <sup>*</sup></label>

            <input class="form__input <?php if (in_array("name", $errors)): ?>form__input--error<?php endif; ?>"
                   type="text" name="name" id="name" value="<?=$name ?>"
                   placeholder="Введите название" required>
            <?php if (in_array("name", $errors)): ?>
                <span class="form__error">Заполните поле имя</span>
            <?php endif; ?>

        </div>

        <div class="form__row">
            <label class="form__label" for="project">Проект <sup>*</sup></label>

            <select class="form__input form__input--select
                    <?php if (in_array("project", $errors)): ?>form__input--error<?php endif; ?>"
                    name="project" id="project" required>
                <?php foreach ($projects as $key => $value): ?>
                    <option value="<?=$value["id"]?>"
                        <?php if ($project === $value["project"]): ?>
                            selected
                        <?php endif; ?>
                        ><?=htmlspecialchars($value["project"])?></option>
                <?php endforeach;?>
            </select>
            <?php if (in_array("project", $errors)): ?>
                <span class="form__error">Выберите проект</span>
            <?php endif; ?>

        </div>

        <div class="form__row">
            <label class="form__label" for="date">Дата выполнения <sup>*</sup></label>

            <input class="form__input form__input--date
                   <?php if (in_array("deadline", $errors)): ?>form__input--error<?php endif;?>"
                   type="text" name="deadline" id="date" value="<?=$date ?>"
                   placeholder="Введите дату в формате ДД.ММ.ГГГГ" required>
            <?php if (in_array("deadline", $errors)): ?>
                <span class="form__error">Заполните дату в нужном формате</span>
            <?php endif;?>
        </div>

        <div class="form__row">
            <label class="form__label">Файл</label>

            <div class="form__input-file">
                <input class="visually-hidden" type="file" name="preview" id="preview" value="">

                <label class="button button--transparent" for="preview">
                    <span>Выберите файл</span>
                </label>
            </div>
        </div>

        <div class="form__row form__row--controls">
            <input class="button" type="submit" name="submit" value="Добавить задачу">
        </div>
    </form>
</div>