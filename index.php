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
    <!-- Subtitle -->
    <h1 class="welcome-page__subtitle">Удобно. Просто. Эффективно</h1>

    <!-- WELCOME block second -->
    <section class="welcome-block-2">
        <!-- Title -->
        <div class="welcome-block-2__header">
            <h1>ВЫБИРАЙ</h1>
            <h2>и</h2>
            <h1>ДОБАВЛЯЙ</h1>
        </div>
        <section class="welcome-block-2__content">
            <!-- Item -->
            <section class="exercise-item">
                <!-- Exercise info -->
                <button class="exercise-item__info-btn"><img src="img/info.svg" alt=""></button>
                <div class="exercise-item__info-content">
                    <button class="exercise-item__info-close"><img src="img/close.svg" alt=""></button>
                    <p class="exercise-item__info-text">Встаньте в упор лежа, ладони в 10 см друг от друга. Сделайте отжимание с касанием грудью пола и выпрямьте руки.</p>
                </div>
                <!-- Exercise muscle groups -->
                <div class="exercise-item__muscle-groups">Руки - плечи - грудь</div>
                <!-- Exercise image -->
                <img class="exercise-item__img" src="img/biceps_4.jpg" alt="">
                <!-- Decoration line -->
                <div class="exercise-item__line"></div>
                <!-- Exercise title -->
                <h1 class="exercise-item__title">Алмазные отжимания</h1>
                <div class="exercise-item__statistic">
                    <div class="exercise-item__rating">
                        <p class="exercise-item__score">4,5</p>
                        <img class="exercise-item__star" src="img/Star.svg" alt="">
                    </div>
                    <div class="exercise-item__difficult">
                        <div class="exercise-item__difficult-item"></div>
                        <div class="exercise-item__difficult-item"></div>
                        <div class="exercise-item__difficult-item"></div>
                        <div class="exercise-item__difficult-item"></div>
                        <div class="exercise-item__difficult-item exercise-item__difficult-item--disabled"></div>
                    </div>
                </div>
                <div class="exercise-item__buttons">
                    <button class="button-text exercise-item__add"><p>Добавить</p> <img src="img/add.svg" alt=""></button>
                    <button class="button-img exercise-item__favorite"><img src="img/favorite.svg" alt=""></button>
                </div>
            </section>
            <section class="exercise-item">
                <!-- Exercise info -->
                <button class="exercise-item__info-btn"><img src="img/info.svg" alt=""></button>
                <div class="exercise-item__info-content">
                    <button class="exercise-item__info-close"><img src="img/close.svg" alt=""></button>
                    <p class="exercise-item__info-text">Встаньте в упор лежа, ладони в 10 см друг от друга. Сделайте отжимание с касанием грудью пола и выпрямьте руки.</p>
                </div>
                <!-- Exercise muscle groups -->
                <div class="exercise-item__muscle-groups">Руки - плечи - грудь</div>
                <!-- Exercise image -->
                <img class="exercise-item__img" src="img/biceps_4.jpg" alt="">
                <!-- Decoration line -->
                <div class="exercise-item__line"></div>
                <!-- Exercise title -->
                <h1 class="exercise-item__title">Алмазные отжимания</h1>
                <div class="exercise-item__statistic">
                    <div class="exercise-item__rating">
                        <p class="exercise-item__score">4,5</p>
                        <img class="exercise-item__star" src="img/Star.svg" alt="">
                    </div>
                    <div class="exercise-item__difficult">
                        <div class="exercise-item__difficult-item"></div>
                        <div class="exercise-item__difficult-item"></div>
                        <div class="exercise-item__difficult-item"></div>
                        <div class="exercise-item__difficult-item"></div>
                        <div class="exercise-item__difficult-item exercise-item__difficult-item--disabled"></div>
                    </div>
                </div>
                <div class="exercise-item__buttons">
                    <button class="button-text exercise-item__delite"><p>Удалить</p> <img src="img/delete.svg" alt=""></button>
                    <button class="button-img exercise-item__favorite"><img src="img/favorite.svg" alt=""></button>
                </div>
            </section>
        </section>
    </section>

    <!-- WELCOME block third -->
    <section class="welcome-block-3">
        <div class="container">
            <!-- Title -->
            <h1 class="welcome-block-3__title">ПОДРОБНАЯ СТАТИСТИКА</h1>
            <!-- First part of statistic(trainings diagram & lasr trainings) -->
            <section class="progress-block">
                <div class="container">
                    <!-- First part of statistic(trainings diagram & lasr trainings) -->
                    <section class="progress-block__trainings">
                        <!-- Count of trainings chart -->
                        <section class="progress-block__trainings-chart">
                            <canvas id="trainingStatisticChart"></canvas>
                        </section>
                        <!-- Last trainings block -->
                        <section class="last-trainings">
                            <h1 class="last-trainings__title">Последние тренировки</h1>
                            <div class="last-trainings__content">
                                <!-- Item -->
                                <section class="last-trainings__card">
                                    <!-- Left part of last exercise item -->
                                    <div class="last-trainings__card-left">
                                        <!-- Time of training -->
                                        <div class="last-trainings__item">
                                        <img class="last-trainings__item-img" src="../img/time.svg" alt="">
                                        <p class="last-trainings__item-text"><span>24</span> мин</p>
                                        </div>
                                        <!-- Exercise count of training -->
                                        <div class="last-trainings__item">
                                        <img class="last-trainings__item-img" src="../img/cards.svg" alt="">
                                        <p class="last-trainings__item-text"><span>24</span> упражнений</p>
                                        </div>
                                    </div>
                                    <!-- Right part of last exercise item -->
                                    <div class="last-trainings__card-right">
                                        <!-- Muscle groups count of training -->
                                        <div class="last-trainings__item">
                                        <img class="last-trainings__item-img" src="../img/cards.svg" alt="">
                                        <p class="last-trainings__item-text"><span>24</span> группы мышц</p>
                                        </div>
                                        <!-- Button 'Подробнее' for more info about exercise -->
                                        <div class="last-trainings__item">
                                        <a class="button-text last-trainings__item-button" href="">Подробнее <img src="img/other.svg" alt=""></a>
                                        </div>
                                    </div>
                                </section>
                                <section class="last-trainings__card">
                                    <!-- Left part of last exercise item -->
                                    <div class="last-trainings__card-left">
                                        <!-- Time of training -->
                                        <div class="last-trainings__item">
                                        <img class="last-trainings__item-img" src="../img/time.svg" alt="">
                                        <p class="last-trainings__item-text"><span>24</span> мин</p>
                                        </div>
                                        <!-- Exercise count of training -->
                                        <div class="last-trainings__item">
                                        <img class="last-trainings__item-img" src="../img/cards.svg" alt="">
                                        <p class="last-trainings__item-text"><span>24</span> упражнений</p>
                                        </div>
                                    </div>
                                    <!-- Right part of last exercise item -->
                                    <div class="last-trainings__card-right">
                                        <!-- Muscle groups count of training -->
                                        <div class="last-trainings__item">
                                        <img class="last-trainings__item-img" src="../img/cards.svg" alt="">
                                        <p class="last-trainings__item-text"><span>24</span> группы мышц</p>
                                        </div>
                                        <!-- Button 'Подробнее' for more info about exercise -->
                                        <div class="last-trainings__item">
                                        <a class="button-text last-trainings__item-button" href="">Подробнее <img src="img/other.svg" alt=""></a>
                                        </div>
                                    </div>
                                </section>
                                <section class="last-trainings__card">
                                    <!-- Left part of last exercise item -->
                                    <div class="last-trainings__card-left">
                                        <!-- Time of training -->
                                        <div class="last-trainings__item">
                                        <img class="last-trainings__item-img" src="../img/time.svg" alt="">
                                        <p class="last-trainings__item-text"><span>24</span> мин</p>
                                        </div>
                                        <!-- Exercise count of training -->
                                        <div class="last-trainings__item">
                                        <img class="last-trainings__item-img" src="../img/cards.svg" alt="">
                                        <p class="last-trainings__item-text"><span>24</span> упражнений</p>
                                        </div>
                                    </div>
                                    <!-- Right part of last exercise item -->
                                    <div class="last-trainings__card-right">
                                        <!-- Muscle groups count of training -->
                                        <div class="last-trainings__item">
                                        <img class="last-trainings__item-img" src="../img/cards.svg" alt="">
                                        <p class="last-trainings__item-text"><span>24</span> группы мышц</p>
                                        </div>
                                        <!-- Button 'Подробнее' for more info about exercise -->
                                        <div class="last-trainings__item">
                                        <a class="button-text last-trainings__item-button" href="">Подробнее <img src="img/other.svg" alt=""></a>
                                        </div>
                                    </div>
                                </section>
                                <section class="last-trainings__card">
                                    <!-- Left part of last exercise item -->
                                    <div class="last-trainings__card-left">
                                        <!-- Time of training -->
                                        <div class="last-trainings__item">
                                        <img class="last-trainings__item-img" src="../img/time.svg" alt="">
                                        <p class="last-trainings__item-text"><span>24</span> мин</p>
                                        </div>
                                        <!-- Exercise count of training -->
                                        <div class="last-trainings__item">
                                        <img class="last-trainings__item-img" src="../img/cards.svg" alt="">
                                        <p class="last-trainings__item-text"><span>24</span> упражнений</p>
                                        </div>
                                    </div>
                                    <!-- Right part of last exercise item -->
                                    <div class="last-trainings__card-right">
                                        <!-- Muscle groups count of training -->
                                        <div class="last-trainings__item">
                                        <img class="last-trainings__item-img" src="../img/cards.svg" alt="">
                                        <p class="last-trainings__item-text"><span>24</span> группы мышц</p>
                                        </div>
                                        <!-- Button 'Подробнее' for more info about exercise -->
                                        <div class="last-trainings__item">
                                        <a class="button-text last-trainings__item-button" href="">Подробнее <img src="img/other.svg" alt=""></a>
                                        </div>
                                    </div>
                                </section>
                            </div>
                        </section>
                    </section>

                    <!-- Second part of statistic(training info and muscles & physical diagram) -->
                    <section class="progress-block__info">
                        <!-- Training info -->
                        <section class="progress-block__workouts">
                            <!-- Muscle groups diagram -->
                            <div class="progress-block__workouts-info">
                                <section class="progress-block__muscle-groups">
                                    <h2 class="progress-block__muscle-groups-title">Группы мышц</h2>
                                    <canvas class="progress-block__muscle-groups-chart" id="muscleGroupsChart"></canvas>
                                </section>
                                <!-- Statistic info -->
                                <section class="progress-block__workouts-statistic">
                                    <p class="progress-block__workouts-statistic-item">Тренировок: <span>24</span></p>
                                    <p class="progress-block__workouts-statistic-item">Программ: <span>24</span></p>
                                    <p class="progress-block__workouts-statistic-item">Упражнений: <span>24</span></p>
                                </section>
                            </div>
                            <!-- Current info -->
                            <section class="progress-block__physical-info">
                                <p class="progress-block__physical-info-item">Вес: 80 кг</p>
                                <p class="progress-block__physical-info-item">Рост: 180 см</p>
                                <button class="button-text progress-block__physical-info-button">Добавить<img src="img/add.svg" alt=""></button>
                            </section>
                        </section>
                        <!-- Physical block -->
                        <section class="progress-block__physical-data">
                            <!-- Navigation -->
                            <nav class="progress-block__physical-data-navigation">
                                <!-- Year & month & week -->
                                <select class="progress-block__physical-data-select" name="" id="">
                                    <option value="value1" selected>Год</option>
                                    <option value="value2">Месяц</option>
                                    <option value="value3">Неделя</option>
                                </select>
                                <!-- Button to other physic(weight or length) -->
                                <button class="button-text progress-block__physical-data-button">РОСТ</button>
                            </nav>
                            
                            <!-- Diagram swiper -->
                            <swiper-container class="progress-block__physical-data-swiper" navigation="true">
                                <swiper-slide class="progress-block__physical-data-slide">
                                    <div class="progress-block__physical-data-chart">
                                        <canvas id="weightDataChart"></canvas>
                                    </div>
                                </swiper-slide>
                            </swiper-container>
                        </section>
                    </section>
                    <section class="progress-block__programm">
                        <!-- Progress line and count of percent -->
                        <div class="progress-block__programm-progress">
                        <h2 class="progress-block__programm-title">Моя программа</h2>
                        <div class="progress-block__programm-info">
                            <div class="progress-block__programm-line">
                                <p class="progress-block__programm-percents">60%</p>
                                <div class="progress-block__programm-finish progress-block__programm-finish--welcome" class="finish"></div>
                            </div>
                            <a class="progress-block__programm-button" href="reg_log.php"><img src="img/my_programm_black.svg" alt=""></a>
                        </div>
                        </div>
                    </section>
                </div>
            </section>
        </div>
    </section>

    <!-- WELCOME block fourth -->
    <section class="welcome-block-4">
        <!-- Title -->
        <h1 class="welcome-block-4__title">Следи за друзьями</h1>
        <!-- user profile -->
        <div class="profile-welcome">
            <!-- Avatar block -->
            <section class="profile-welcome__avatar">
                <!-- avatar image -->
                <img class="profile-welcome__avatar-img" src="img/man_avatar.svg" alt="">
                <!-- name -->
                <h2 class="profile-welcome__title">Иван Иванов</h2>
                <!-- last training time -->
                <button class="user-block__avatar-more user-block__avatar-more--welcome"><img src="img/info_white.svg" alt=""><p>Подробнее</p></button>
                <!-- Button 'подписаться' -->
                <button class="button-text profile-welcome__button">Подписаться <img src="img/add_black.svg" alt=""></button>
            </section>
            <!-- info block -->
            <section class="profile-welcome__info">
                <!-- text about user -->
                <div class="profile-welcome__about">
                    <h3 class="profile-welcome__about-title">О себе</h3>
                    <p class="profile-welcome__about-text">Занимаюсь спортом, жму 40. Буду рад знакомству!</p>
                </div>
                <!-- Follow & followers -->
                <div class="profile-welcome__users">
                    <a class="profile-welcome__follows" href=""><span>25</span> подписчиков</a>
                    <a class="profile-welcome__followers" href=""><span>15</span> подписок</a>
                </div>
            </section>
        </div>
    </section>

    <!-- WELOCOME block fifth -->
    <section class="welcome-block-5">
        <!-- Decoration circles -->
        <div class="welcome-block-5__circle welcome-block-5__circle--first"></div>
        <div class="welcome-block-5__circle welcome-block-5__circle--second"></div>
        <div class="welcome-block-5__circle welcome-block-5__circle--third"></div>
        <div class="welcome-block-5__circle welcome-block-5__circle--fourth"></div>
        <!-- Titles -->
        <h1 class="welcome-block-5__title">ТРЕНИРУЙСЯ</h1>
        <h1 class="welcome-block-5__title">СОВЕРШЕНСТВУЙСЯ</h1>
        <h1 class="welcome-block-5__title">ДОСТИГАЙ ЦЕЛЕЙ</h1>
    </section>

    <!-- WELOCOME block sixth -->
    <section class="welcome-block-6">
        <!-- Title -->
        <h1 class="welcome-block-6__title">Кроссплатформенность</h1>
        <!-- Content -->
        <div class="welcome-block-6__content">
            <img src="img/telephone.svg" alt="">
            <h2>ДОСТУП</h2>
            <img src="img/computer.svg" alt="">
        </div>
        <h2 class="welcome-block-6__subtitle">с любого устройства</h2>
    </section>

    <!-- WELOCOME block seventh -->
    <section class="welcome-block-7">
        <div class="container">
            <!-- Level of user -->
            <div class="welcome-block-7__level">
                <!-- Title -->
                <h1 class="welcome-block-7__level-title">Подойдёт любому</h1>
                <!-- Users level block -->
                <div class="welcome-block-7__level-users">
                    <!-- Beginner -->
                    <div class="welcome-block-7__level-item">
                        <img src="img/beginner.svg" alt="">
                        <h3>Новичок</h3>
                    </div>
                    <!-- Professional -->
                    <div class="welcome-block-7__level-item welcome-block-7__level-item--professional">
                        <img src="img/professional.svg" alt="">
                        <h3>Профессионал</h3>
                    </div>
                    <div class="welcome-block-7__level-item">
                        <img src="img/advanced.svg" alt="">
                        <h3>Продвинутый</h3>
                    </div>
                </div>
                <h2 class="welcome-block-7__level-subtitle">Уровень подготовки не важен</h2>
                <!-- Decoration -->
                <div class="welcome-block-7__line"></div>
            </div>
        <div class="welcome-block-7__chats">
            <div class="welcome-block-7__profiles">
                <div class="welcome-block-7__sportsman">
                    <img class="welcome-block-7__arrow-coach" src="img/arrow_coach.svg" alt="">
                    <img class="welcome-block-7__arrow-doctor" src="img/arrow_doctor.svg" alt="">
                    <img class="welcome-block-7__sportsman-img" src="img/sportsman.svg" alt="">
                    <p>Спортсмен</p>
                </div>
                <div class="welcome-block-7__personal">
                    <div class="welcome-block-7__coach">
                        <img src="img/coach.svg" alt="">
                        <p>Тренер</p>
                    </div>
                    <div class="welcome-block-7__doctor">
                        <img src="img/doctor.png" alt="">
                        <p>Врач</p>
                    </div>
                </div>
            </div>
            <div class="welcome-block-7__info">
                <h2>Платформа облегчает процесс взаимодействия с тренером и лечащим врачом</h2>
                <div class="welcome-block-7__list">
                    <ul>
                        <li>просмотр тренировок, расписания и программ от тренера</li>
                        <li>просмотр назначенных лекарств от врача</li>
                        <li>сравнение спортсменов</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- WELOCOME block eighth -->
    <section class="welcome-block-8">
        <h1 class="welcome-block-8__title">ИСПЫТАЙ</h1>
        <h1 class="welcome-block-8__title">ВСЕ ВОЗМОЖНОСТИ</h1>
        <h1 class="welcome-block-8__title">ПЛАТФОРМЫ</h1>
    </section>

    <!-- WELOCOME block nineth -->
    <section class="welcome-block-9">
        <!-- Title -->
        <div class="welcome-block-9__header">
            <img class="welcome-block-9__goal-img" src="img/goal.svg" alt="">
            <h1 class="welcome-block-9__title">Начни совершенствоваться прямо сейчас</h1>
        </div>
        <!-- Button to login -->
        <div class="welcome-block-9__content">
            <h2 class="welcome-block-9__text">Повышаем <span>эффективность</span> и <span>удобство</span> ваших тренировок</h2>
            <a class="button-text welcome-block-9__button" href="reg_log.php">Начать <img src="img/start_arrow.svg" alt=""></a>
        </div>
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
    <script>
        // Decorative hand
        let decorativeHand = document.querySelector('.welcome-block-1__hand-img');

        decorativeHand.addEventListener('click', function(){
            document.querySelector('.welcome-block-2').scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        });


        // Buttons for exercise info
        let infoExerciseButton = document.querySelectorAll('.exercise-item__info-btn');
        let closeInfoExerciseButton = document.querySelectorAll('.exercise-item__info-close');
        let infoBlock = document.querySelectorAll('.exercise-item__info-content');

        for(let i = 0; i < infoExerciseButton.length; i++){
            infoExerciseButton[i].addEventListener('click', function(){
                infoBlock[i].style.cssText = `top: -1%;`;
            });

            closeInfoExerciseButton[i].addEventListener('click', function(){
                infoBlock[i].style.cssText = `top: -101%;`;
            });
        }
        

        // Count of training chart
        const ctx1 = document.getElementById('trainingStatisticChart');

        new Chart(ctx1, {
            type: 'line',
            data: {
            labels: ['Январь', 'Февраль', 'Март', 'Апрель', 'Май', 'Июнь', 'Июль', 'Август', 'Сентябрь', 'Октябрь', 'Ноябрь', 'Декабрь'],
            datasets: [{
                label: 'Колво тренировок',
                data: [12, 19, 3, 5, 2, 3, 12, 19, 3, 5, 2, 3],
                borderWidth: 3,
                backgroundColor: '#ffffff',
                color: '#000000',
                borderColor: '#000000'
            }]
            },
            options: {
                responsive: true,
                responsive: true,
                scales: {
                    x: {
                        grid: {
                            display: true,
                            color: 'rgba(255, 255, 255, 0.2)'
                        },
                        title: {
                            display: true,
                            text: 'Месяца',
                            font: {
                                size: 16,
                                family: 'Open Sans',
                            }
                        }
                    },
                    y: {
                        grid: {
                            display: true,
                            color: 'rgba(255, 255, 255, 0.2)'
                        },
                        title: {
                            display: true,
                            text: 'Количество тренировок',
                            font: {
                                size: 16,
                                family: 'Open Sans',
                            }
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false,
                    },
                    title: {
                        display: true,
                        text: 'Количество тренировок - 2023',
                        font: {
                            size: 20,
                            family: 'Open Sans',
                        }               
                    }
                }
            },
        });


        // Muscle groups chart
        const ctx2 = document.getElementById('muscleGroupsChart');

        new Chart(ctx2, {
            type: 'pie',
            data: {
                labels: ['Руки', 'Ноги', 'Спина'],
                datasets: [{
                    label: 'Количество упражнений',
                    data: [12, 19, 3],
                    borderWidth: 1,
                    color: '#000000',
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'right',
                        align: 'center',
                        labels: {
                            font: {
                                family: 'Open Sans',
                                size: 20,
                            },
                            color: '#000000',
                        },
                    },
                title: {
                    display: false,
                }
                }
            },
        });


        // Physical data chart
        const ctx3 = document.getElementById('weightDataChart');

        new Chart(ctx3, {
            type: 'line',
            data: {
            labels: ['Я', 'Ф', 'М', 'А', 'М', 'И', 'И', 'А', 'С', 'О', 'Н', 'Д'],
            datasets: [{
                label: 'Вес за неделю',
                data: [80, 81, 83, 79],
                borderWidth: 3,
                backgroundColor: '#ffffff',
                color: '#00CD1E',
                borderColor: '#000000'
            }]
            },
            options: {
                responsive: true,
                responsive: true,
                scales: {
                    x: {
                        grid: {
                            display: true,
                            color: 'rgba(0, 0, 0, 0.2)'
                        },
                        title: {
                            display: true,
                            text: 'Месяц',
                            font: {
                                size: 16,
                                family: 'Open Sans',
                            }
                        }
                    },
                    y: {
                        grid: {
                            display: true,
                            color: 'rgba(0, 0, 0, 0.2)'
                        },
                        title: {
                            display: true,
                            text: 'Вес (кг)',
                            font: {
                                size: 16,
                                family: 'Open Sans',
                            }
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false,
                    },
                    title: {
                        display: true,
                        text: 'Вес - 2034',
                        font: {
                            size: 20,
                            family: 'Open Sans',
                        }               
                    }
                }
            },
        });

        // Height of last-trainings block
        let lastTrainingsBlock = document.querySelector('.last-trainings');
        lastTrainingsBlock.style.cssText = `height: ${document.querySelector('.progress-block__trainings-chart').clientHeight}px;`;
    </script>
</body>
</html>