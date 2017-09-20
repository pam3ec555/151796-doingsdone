<?php

$email = $_POST["email"] ?? "";
$name = $_POST["name"] ?? "";

?>


<section class="content__side">
    <p class="content__side-info">Если у вас уже есть аккаунт, авторизуйтесь на сайте</p>

    <a class="button button--transparent content__side-button" href="#">Войти</a>
</section>

<main class="content__main">
    <h2 class="content__main-heading">Регистрация аккаунта</h2>

    <form class="form" action="index.php?register" method="post">
        <div class="form__row">
            <label class="form__label" for="email">E-mail <sup>*</sup></label>

            <input class="form__input
                <?php if (in_array("email", $errors) || in_array("email", $wrongs)): ?>
                    form__input--error
                <?php endif; ?>
                " type="email" name="email" id="email" value="<?=$email?>" placeholder="Введите e-mail">

            <?php if (in_array("email", $errors)): ?>
                <p class="form__message">E-mail введён некорректно</p>
            <?php elseif (in_array("email", $wrongs)): ?>
                <p class="form__message">Пользователь с таким E-mail уже существует</p>
            <?php endif; ?>
        </div>

        <div class="form__row">
            <label class="form__label" for="password">Пароль <sup>*</sup></label>

            <input class="form__input
                <?php if (in_array("password", $errors)): ?>
                    form__input--error
                <?php endif; ?>
                " type="password" name="password" id="password" value="" placeholder="Введите пароль">

            <?php if (in_array("password", $errors)): ?>
                <p class="form__message">Заполните пароль</p>
            <?php endif; ?>
        </div>

        <div class="form__row">
            <label class="form__label" for="name">Имя <sup>*</sup></label>

            <input class="form__input
                <?php if (in_array("name", $errors)): ?>
                    form__input--error
                <?php endif; ?>
                " type="text" name="name" id="name" value="<?=$name?>" placeholder="Введите имя">

            <?php if (in_array("name", $errors)): ?>
                <p class="form__message">Заполните имя</p>
            <?php endif; ?>
        </div>

        <div class="form__row form__row--controls">
            <?php if ($errors || $wrongs): ?>
                <p class="error-massage">Пожалуйста, исправьте ошибки в форме</p>
            <?php endif; ?>

            <input class="button" type="submit" name="submit" value="Зарегистрироваться">
        </div>
    </form>
</main>
