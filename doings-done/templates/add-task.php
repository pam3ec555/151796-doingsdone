<div class="modal">
    <button class="modal__close" type="button" name="button">Закрыть</button>

    <h2 class="modal__heading">Добавление задачи</h2>

    <form class="form" action="index.php" method="POST" enctype="multipart/form-data">
        <div class="form__row">
            <label class="form__label" for="name">Название <sup>*</sup></label>

            <input class="form__input <?php if (in_array("name", $task_errors)): ?>form__input--error<?php endif;?>"
                   type="text" name="name" id="name" value="<?=$name ?>"
                   placeholder="Введите название" required>
        </div>

        <div class="form__row">
            <label class="form__label" for="project">Проект <sup>*</sup></label>

            <select class="form__input form__input--select
                    <?php if (in_array("project", $task_errors)): ?>form__input--error<?php endif; ?>"
                    name="project" id="project" required>
                <option value="incoming" <?php if ($project === "incoming"): ?>selected<? endif; ?>>Входящие</option>
            </select>
        </div>

        <div class="form__row">
            <label class="form__label" for="date">Дата выполнения <sup>*</sup></label>

            <input class="form__input form__input--date
                   <?php if (in_array("date", $task_errors)): ?>form__input--error<?php endif;?>"
                   type="text" name="date" id="date" value="<?=$date ?>"
                   placeholder="Введите дату в формате ДД.ММ.ГГГГ" required>
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
            <input class="button" type="submit" name="" value="Добавить">
        </div>
    </form>
</div>