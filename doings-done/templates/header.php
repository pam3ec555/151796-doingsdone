<a href="#">
    <img src="img/logo.png" width="153" height="42" alt="Логитип Дела в порядке">
</a>

<div class="main-header__side">
    <?php if (isset($_SESSION["user"])): ?>
        <a class="main-header__side-item button button--plus" href="?add">Добавить задачу</a>
        <div class="main-header__side-item user-menu">
            <div class="user-menu__image">
                <img src="img/user-pic.jpg" width="40" height="40" alt="Пользователь">
            </div>

            <div class="user-menu__data">
                <p><?=$_SESSION["user"]["name"]?></p>

                <a href="?logout">Выйти</a>
            </div>
        </div>
    <?php else: ?>
        <div class="main-header__side">
            <a class="main-header__side-item button button--transparent" href="?login">Войти</a>
        </div>
    <?php endif; ?>
</div>