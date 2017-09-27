<h2 class="content__main-heading">Список задач</h2>

<form class="search-form" action="index.php" method="post">
    <input class="search-form__input" type="text" name="search" value="" placeholder="Поиск по задачам">

    <input class="search-form__submit" type="submit" name="submit" value="Искать">
</form>

<div class="tasks-controls">
    <div class="radio-button-group">
        <label class="radio-button">
            <input class="radio-button__input visually-hidden" type="radio" name="radio" value="all"
                <?php if($task_deadline == "all"): ?>checked<?php endif;?>>
            <span class="radio-button__text">Все задачи</span>
        </label>

        <label class="radio-button">
            <input class="radio-button__input visually-hidden" type="radio" name="radio" value="today"
                <?php if($task_deadline == "today"): ?>checked<?php endif;?>>
            <span class="radio-button__text">Повестка дня</span>
        </label>

        <label class="radio-button">
            <input class="radio-button__input visually-hidden" type="radio" name="radio" value="tomorrow"
                   <?php if($task_deadline == "tomorrow"): ?>checked<?php endif;?>>
            <span class="radio-button__text">Завтра</span>
        </label>

        <label class="radio-button">
            <input class="radio-button__input visually-hidden" type="radio" name="radio" value="past"
                   <?php if($task_deadline == "past"): ?>checked<?php endif;?>>
            <span class="radio-button__text">Просроченные</span>
        </label>
    </div>

    <label class="checkbox">
        <input id="show-complete-tasks" class="checkbox__input visually-hidden" type="checkbox"
            <?php if ($show_complete_tasks == 1): ?>checked<?php endif; ?>>
        <span class="checkbox__text">Показывать выполненные</span>
    </label>
</div>

<table class="tasks">
    <?php foreach ($tasks as $key => $value): ?>
        <?php if (
                     ($project_id === $value["project_id"] || $project_inset === -1) &&
                     ((!$value["is_complete"] && $show_complete_tasks == 0) || $show_complete_tasks == 1)
                 ):
        ?>
        <tr class="tasks__item <?php if ($value["is_complete"] === 1): ?>task--completed<?php endif; ?>">
            <td class="task__select">
            <label class="checkbox task__checkbox">
              <input class="checkbox__input checkbox__input--task visually-hidden" type="checkbox"
                     id="task<?=$value['id']?>"
                     <?php if ($value["is_complete"] === true): ?>checked<?php endif; ?>>
              <span class="checkbox__text"><?=htmlspecialchars($value["task"]); ?></span>
            </label>
            </td>
            <td class="task__file">
                <?php if ($value["file_url"]): ?>
                    <a class="download-link" href="<?=$value["file_url"]?>"><?=$value["file_name"]?></a>
                <?php endif; ?>
            </td>
            <td class="task__date"><?=date("d.m.Y", strtotime($value["deadline"])); ?></td>
            <td class="task__controls">
                <button class="expand-control" type="button" name="button">Открыть список комманд</button>

                <ul class="expand-list hidden">
                    <?php if ($value["is_complete"] === 0): ?>
                        <li class="expand-list__item">
                            <a href="?task_complete=<?=$value['id']?>">Выполнить</a>
                        </li>
                    <?php endif; ?>

                    <li class="expand-list__item">
                        <a href="?task_delete=<?=$value['id']?>">Удалить</a>
                    </li>

                    <li class="expand-list__item">
                        <a href="?task_copy=<?=$value['id']?>">Дублировать</a>
                    </li>
                </ul>
            </td>
        </tr>
        <?php endif; ?>
    <?php endforeach; ?>
</table>

