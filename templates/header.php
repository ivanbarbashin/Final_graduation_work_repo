<header class="main-header">
    <!-- Logo -->
    <a href="../index.php" class="header__item header__item--logo">
        <img src="../img/logo.svg" alt="">
        <p>Training</p>
    </a>
    <?php
    if ($user_data->get_auth()){ ?>
        <?php
        if ($user_data->get_auth() && ($user_data->get_status() == "user" || $user_data->get_status() == "admin")){ ?>
        <a class="header__item" href="../user/workout.php">
            <img src="../img/workout_black.svg" alt="">
            <p>Тренировки</p>
        </a>
        <a class="header__item" href="../user/progress.php">
            <img src="../img/progress_black.svg" alt="">
            <p>Прогресс</p>
        </a>
        <?php } ?>
        <?php
        if ($user_data->get_auth() && ($user_data->get_status() == "user" || $user_data->get_status() == "coach")){ ?>
        <a class="header__item" href="../user/exercises.php">
            <img src="../img/exercises_black.svg" alt="">
            <p>Упражнения</p>
        </a>
        <?php } ?>
        <?php
        if ($user_data->get_auth() && ($user_data->get_status() == "user" || $user_data->get_status() == "admin")){ ?>
        <!-- Для спортсмена -->
        <a class="header__item" href="../user/staff.php">
            <img src="../img/coach_header.svg" alt="">
            <p>Персонал</p>
        </a>
        <?php } else if ($user_data->get_auth() && $user_data->get_status() == "coach") { ?>
        <a class="header__item" href="../user/coach.php">
            <img src="../img/sportsman_header.svg" alt="">
            <p>Спортсмен</p>
        </a>
        <?php } else if ($user_data->get_status() == "doctor"){ ?>
        <a class="header__item" href="../user/doctor.php">
            <img src="../img/sportsman_header.svg" alt="">
            <p>Спортсмен</p>
        </a>
        <?php } ?>
        <a class="header__item" href="search_users.php">
            <img src="../img/friends.svg" alt="">
            <p>Пользователи</p>
        </a>
        <a class="header__item" href="all_news.php">
            <img src="../img/news_black.svg" alt="">
            <p>Новости</p>
        </a>
        <a class="header__item" href="profile.php">
            <img src="../img/profile_black.svg" alt="">
            <p>Профиль</p>
        </a>
        <a class="header__item" href="other.php">
            <img class="other_img" src="../img/other_black.svg" alt="">
            <p>Другое</p>
        </a>
    <?php } else { ?>
        <a class="welcome-header__item welcome-header__item--log" href="../reg_log.php">Войти</a>
    <?php }?>
</header>

<header class="sub-header">
    <!-- Logo -->
    <a href="../index.php" class="header__item header__item--logo">
        <img src="../img/logo.svg" alt="">
        <p>Training</p>
    </a>
    <button href="../index.php" class="header__item header__item--open">
        <img src="../img/menu.svg" alt="">
    </button>
</header>

<div class="sub-header__content-cover">
        <div class="sub-header__content-block">
        <button class="header__item--close"><img src="../img/close.svg" alt=""></button>
        <div class="sub-header__content">
            <?php
            if ($user_data->get_auth()){ ?>
                <?php
                if ($user_data->get_auth() && ($user_data->get_status() == "user" || $user_data->get_status() == "admin")){ ?>
                <a class="header__item" href="../user/workout.php">
                    <img src="../img/workout_black.svg" alt="">
                    <p>Тренировки</p>
                </a>
                <a class="header__item" href="../user/progress.php">
                    <img src="../img/progress_black.svg" alt="">
                    <p>Прогресс</p>
                </a>
                <?php } ?>
                <?php
                if ($user_data->get_auth() && ($user_data->get_status() == "user" || $user_data->get_status() == "coach")){ ?>
                <a class="header__item" href="../user/exercises.php">
                    <img src="../img/exercises_black.svg" alt="">
                    <p>Упражнения</p>
                </a>
                <?php } ?>
                <?php
                if ($user_data->get_auth() && ($user_data->get_status() == "user" || $user_data->get_status() == "admin")){ ?>
                <!-- Для спортсмена -->
                <a class="header__item" href="../user/staff.php">
                    <img src="../img/coach_header.svg" alt="">
                    <p>Персонал</p>
                </a>
                <?php } else if ($user_data->get_auth() && $user_data->get_status() == "coach") { ?>
                <a class="header__item" href="../user/coach.php">
                    <img src="../img/sportsman_header.svg" alt="">
                    <p>Спортсмен</p>
                </a>
                <?php } else if ($user_data->get_status() == "doctor"){ ?>
                <a class="header__item" href="../user/doctor.php">
                    <img src="../img/sportsman_header.svg" alt="">
                    <p>Спортсмен</p>
                </a>
                <?php } ?>
                <a class="header__item" href="search_users.php">
                    <img src="../img/friends.svg" alt="">
                    <p>Пользователи</p>
                </a>
                <a class="header__item" href="all_news.php">
                    <img src="../img/news_black.svg" alt="">
                    <p>Новости</p>
                </a>
                <a class="header__item" href="profile.php">
                    <img src="../img/profile_black.svg" alt="">
                    <p>Профиль</p>
                </a>
                <a class="header__item" href="other.php">
                    <img class="other_img" src="../img/other_black.svg" alt="">
                    <p>Другое</p>
                </a>
            <?php } else { ?>
                <a class="welcome-header__item welcome-header__item--log" href="../reg_log.php">Войти</a>
            <?php }?>
        </div>
        </div>
    </div>