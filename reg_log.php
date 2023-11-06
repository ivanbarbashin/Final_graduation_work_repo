<?php
include "templates/func.php";
include "templates/settings.php";

$user_data->redirect_logged();
$error_array = array(
    "reg_fill_all_input_fields" => false,
    "reg_login_is_used" => false,
    "reg_passwords_are_not_the_same" => false,
    "reg_conn_error" => false,
    "reg_success" => false,
    "too_long_string" => false,
    "adding_stats" => false,
    "log_conn_error" => false,
    "log_fill_all_input_fields" => false,
    "log_incorrect_login_or_password" => false
);

if (isset($_POST['reg'])){
    $reg_stat = NULL;
    if (isset($_POST['reg_status'])){
        $reg_stat = $_POST['reg_status'];
    }
    $error_array = $user_data->reg($conn, $_POST['reg_login'], $reg_stat, $_POST['reg_password'], $_POST['reg_password2'], $_POST['reg_name'], $_POST['reg_surname']);
}

if (isset($_POST['log'])){
    $error_array = $user_data->authenticate($conn, $_POST['log_login'], $_POST['log_password']);
}
?>
<!DOCTYPE html>
<html lang="en">
<?php inc_head("OpenDoor", true); ?>
<body class="log-reg">
    <div class="container">
        <!-- Log and reg logo -->
        <a href="index.php" class="header__item--logo reg-log__logo">
            <img src="img/logo_reg_log.svg" alt="">
            <p>Training</p>
        </a>

        <!-- Content of log & reg -->
        <section class="log-reg__content">
            <!-- title -->
            <h1 class="log-reg__title">ЛИЧНЫЙ КАБИНЕТ</h1>
            <!-- Switch buttons (login or registration) -->
            <div class="log-reg__switch-buttons">
                <button class="log-reg__switch-button log-reg__switch-button--log log-reg__switch-button--active">Вход</button>
                <button class="log-reg__switch-button log-reg__switch-button--reg">Регистрация</button>
            </div>
            <!-- Login form -->
            <form class="log-form" action="" method="post">
                <label class="log-form__label" for="login_entry">Логин</label>
                <input class="log-form__input" name="log_login" type="text" id="login_entry">
                <label class="log-form__label" for="password_entry">Пароль</label>
                <input class="log-form__input" name="log_password" type="password" id="password_entry">
                <button class="button-text log-form__submit" type="submit" name="log" value="1">Войти</button>
                <?php
                log_warning($error_array['log_incorrect_login_or_password'], "Неправильный логин или пароль");
                log_warning($error_array['log_fill_all_input_fields'], "Заполните все поля");
                if ($error_array['log_conn_error']){ log_warning($error_array['log_conn_error'], "Ошибка: " . $conn->error); };
                if (isset($_GET['please_log'])){ echo "<p class=''> Пожалуйста авторизуйтесь</p>"; }
                if (isset($_GET['reg'])){ echo "<p class=''>Регистрация прошла успешно, пожалуйста авторизуйтесь</p>"; }
                ?>
            </form>
            <!-- Registration form -->
            <form class="reg-form" action="" method="post">
                <label class="reg-form__label" for="name">Имя</label>
                <input class="reg-form__input" name="reg_name" type="text" id="name">
                <label class="reg-form__label" for="surname">Фамилия</label>
                <input class="reg-form__input" name="reg_surname" type="text" id="surname">
                <h2 class="reg-form__profile-title">Выберите профиль</h2>
                <!-- User's profile -->
                <div class="reg-form__profiles">
                    <div class="reg-form__profile">
                        <input class="reg-form__profile-input" type="radio" name="reg_status" id="sportsman" value="user">
                        <label class="reg-form__profile-label" for="sportsman">Спортсмен</label>
                    </div>
                    <div class="reg-form__profile">
                        <input class="reg-form__profile-input" type="radio" name="reg_status" id="coach" value="coach">
                        <label class="reg-form__profile-label" for="coach">Тренер</label>
                    </div>
                    <div class="reg-form__profile">
                        <input class="reg-form__profile-input" type="radio" name="reg_status" id="doctor" value="doctor">
                        <label class="reg-form__profile-label" for="doctor">Врач</label>
                    </div>
                </div>
                <label class="reg-form__label" for="login">Логин</label>
                <input class="reg-form__input" name="reg_login" type="text" id="login">
                <label class="reg-form__label" for="password">Пароль</label>
                <input class="reg-form__input" name="reg_password" type="password" id="password">
                <label class="reg-form__label" for="check_password">Подтвердите пароль</label>
                <input class="reg-form__input" name="reg_password2" type="password" id="check_password">
                <button class="button-text reg-form__submit" type="submit" name="reg" value="1">Зарегистрироваться</button>
                <?php
                reg_warning($error_array['reg_login_is_used'], "Логин занят");
                reg_warning($error_array['reg_passwords_are_not_the_same'], "Пароли не совпадают");
                reg_warning($error_array['reg_fill_all_input_fields'], "Заполните все поля");
                reg_warning($error_array["too_long_string"], "Превышел лимит символов");
                if ($error_array['reg_conn_error']){ reg_warning($error_array['reg_conn_error'], "Error: " . $conn->error); }
                $conn->close();
                ?>
            </form>
        </section>
    </div>

    <script>

        // Switch buttons (login or registration)
        let logButton = document.querySelector('.log-reg__switch-button--log');
        let regButton = document.querySelector('.log-reg__switch-button--reg');
        let logForm = document.querySelector('.log-form');
        let regForm = document.querySelector('.reg-form');
        let regDoneButton = document.querySelector('.reg-form__warning');


        logButton.addEventListener('click', function(){
            if (logButton.classList.contains('log-reg__switch-button--active') == false){
                logButton.classList.add('log-reg__switch-button--active');
                regButton.classList.remove('log-reg__switch-button--active');
                logForm.style.cssText = `display: flex;`;
                regForm.style.cssText = `display: none;`;
                localStorage.setItem('SwitchRegLogButton', 'log')
            }
        });

        regButton.addEventListener('click', function(){
            if (regButton.classList.contains('log-reg__switch-button--active') == false){
                logButton.classList.remove('log-reg__switch-button--active');
                regButton.classList.add('log-reg__switch-button--active');
                regForm.style.cssText = `display: flex;`;
                logForm.style.cssText = `display: none;`;
                localStorage.setItem('SwitchRegLogButton', 'reg')
            }
        });

        if(regDoneButton.length == 0){
            localStorage.setItem('SwitchRegLogButton', 'log');
        }

        //local storage buttons data
        if(localStorage.getItem('SwitchRegLogButton')){
            if(localStorage.getItem('SwitchRegLogButton') == 'reg'){
                regButton.click();
            }
            if(localStorage.getItem('SwitchRegLogButton') == 'log'){
                logButton.click();
            }
        }
        else{
            localStorage.setItem('SwitchRegLogButton', 'log');
        }

        
    </script>
</body>
</html>