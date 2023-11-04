<?php
include "templates/func.php";
include "templates/settings.php";
$user_data->redirect_logged();
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<?php inc_head("Welcome!", true);  ?>
<body class="welcome-page">
    <!-- Welcome block first -->
    <section class="welcome-block-1">
        <!-- Welcome header -->
        <header class="main-header welcome-header">
            <!-- Logo -->
            <a href="index.php" class="welcome-header__item  welcome-header__item--logo">
                <img src="img/logo.svg" alt="">
                <p>Training</p>
            </a>
            <a class="welcome-header__item" href="reg_log.php">
                <img src="img/workout.svg" alt="">
                <p>Тренировки</p>
            </a>
            <a class="welcome-header__item" href="reg_log.php">
                <img src="img/progress.svg" alt="">
                <p>Прогресс</p>
            </a>
            <a class="welcome-header__item" href="reg_log.php">
                <img src="img/exercises.svg" alt="">
                <p>Упражнения</p>
            </a>
            <a class="welcome-header__item" href="reg_log.php">
                <img class="other_img" src="img/other.svg" alt="">
                <p>Другое</p>
            </a>
            <a class="welcome-header__item" href="reg_log.php">Войти</a>
        </header>
        <!-- Title -->
        <div class="welcome-block-1__title">
            <h1>OpenDoor Training</h1>
            <h2>Твои цели - наши цели</h2>
        </div>
        <!-- Decoration images -->
        <button class="welcome-block-1__hand-img" ><img src="img/welcome_hand.svg" alt=""></button>
        <img class="welcome-block-1__biceps-left" src="img/welcome_biceps_1.svg" alt="">
        <img class="welcome-block-1__biceps-right" src="img/welcome_biceps_2.svg" alt="">
    </section>

    <footer class="main_footer">
        <!-- Contacts -->
        <div class="footer__contacts">
            <div class="footer__social-media">
                <p class="footer__contacts-title">Контакты:</p>
                <a class="footer__contact-btn" href="https://t.me/Xcvbnmzd"><img src="img/tg.svg" alt=""></a>
                <a class="footer__contact-btn" href="https://vk.com/id497007918"><img src="img/vk.svg" alt=""></a>
            </div>
            <div class="footer__email">
                <p class="footer__email-title">Email:</p>
                <a class="footer__email-btn" href="mailto:ivanbarbash06@gmail.com?subject=Вопрос по сайту">ivanbarbash06@gmail.com</a>
            </div>
        </div>
        <!-- About -->
        <div class="footer__about">
            <p class="footer__about-item">Все права защищены &#169;</p> 
            <p class="footer__about-item">Иван Барбашин</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/swiper@10/swiper-element-bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</body>
</html>