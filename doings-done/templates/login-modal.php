<?php

$email = $_POST["email"] ?? "";

?>

<div class="modal">
    <button class="modal__close" type="button" name="button">Закрыть</button>

    <h2 class="modal__heading">Вход на сайт</h2>

    <form class="form" class="" action="index.php" method="POST">
        <div class="form__row">
            <label class="form__label" for="email">E-mail <sup>*</sup></label>

            <input class="form__input
                <?php if (in_array("email", $errors) || in_array("email", $wrongs)): ?>
                    form__input--error
                <?php endif; ?>"
                   type="text" name="email" id="email" value=""
                   placeholder="Введите e-mail">
            <?php if (in_array("email", $errors)): ?>
                <p class="form__message">E-mail введён некорректно</p>
            <?php elseif (in_array("email", $wrongs)): ?>
                <p class="form__message">Пользователя с таким E-mail не существует</p>
            <?php endif; ?>
        </div>

        <div class="form__row">
            <label class="form__label" for="password">Пароль <sup>*</sup></label>

            <input class="form__input" type="password" name="password" id="password" value="" placeholder="Введите пароль">

            <?php if (in_array("password", $errors)): ?>
                <p class="form__message">Пароль введён некорректно</p>
            <?php elseif (in_array("password", $wrongs)): ?>
                <p class="form__message">Вы ввели неверный пароль</p>
            <?php endif; ?>
        </div>

        <div class="form__row">
            <label class="checkbox">
                <input class="checkbox__input visually-hidden" type="checkbox" checked>
                <span class="checkbox__text">Запомнить меня</span>
            </label>
        </div>

        <div class="form__row form__row--controls">
            <input class="button" type="submit" name="" value="Войти">
        </div>
    </form>
</div>