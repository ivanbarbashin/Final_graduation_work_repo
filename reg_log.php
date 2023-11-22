<?php
// 
include "templates/func.php"; // Include functions file
include "templates/settings.php"; // Include settings file

// Redirect the user if already logged in
$user_data->redirect_logged();

// list of values for registration and login errors
$error_array = array(
    "reg_fill_all_input_fields" => false,
    "reg_login_is_used" => false,
    "reg_login_too_short" => false,
    "reg_passwords_are_not_the_same" => false,
    "reg_password_not_fit" => false,
    "reg_password_too_short" => false,
    "reg_conn_error" => false,
    "reg_success" => false,
    "too_long_string" => false,
    "adding_stats" => false,
    "log_conn_error" => false,
    "log_fill_all_input_fields" => false,
    "log_incorrect_login_or_password" => false
);

// Process the submission of the registration form
if (isset($_POST['reg'])){
    $reg_stat = NULL;
    if (isset($_POST['reg_status'])){
        $reg_stat = $_POST['reg_status'];
    }

    $error_array = $user_data->reg($conn, $_POST['reg_login'], $reg_stat, $_POST['reg_password'], $_POST['reg_password2'], $_POST['reg_name'], $_POST['reg_surname']); // Call the registration method and update the error array based on registration attempt
}

// Process the submission of the дщпшт form
if (isset($_POST['log'])){
    $error_array = $user_data->authenticate($conn, $_POST['log_login'], $_POST['log_password']); // Call the authentication method and update the error array based on login attempt
}
?>
<!DOCTYPE html>
<html lang="en">
<?php inc_head("OpenDoor", true); // print head.php ?>
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
                // Login warnings
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
                // Registration warnings
                reg_warning($error_array["reg_login_too_short"], "Слишком короткий логин");
                reg_warning($error_array["reg_password_too_short"], "Слишком короткий пароль");
                reg_warning($error_array["reg_password_not_fit"], "Пароль должен содержать буквы");
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


    <!-- TESTS -->
    <!-- data for testing resistration -->
    <!-- <script src="tests/test_registration.js"></script> -->
    <!-- data for testing login -->
    <!-- <script src="tests/test_login.js"></script> -->


    <script>
        // profile type inputs
        let regProfileInputsCheked = document.querySelectorAll('.reg-form__profile-input');

        // get localstorage data (all values of registrtion)
        if(localStorage.getItem('regName')){
            document.querySelector('.reg-form__input[name="reg_name"]').value = localStorage.getItem('regName');
        }
        if(localStorage.getItem('regSurname')){
            document.querySelector('.reg-form__input[name="reg_surname"]').value = localStorage.getItem('regSurname')
        }
        if(localStorage.getItem('regProfileInputs')){
            let regProfileInputs = localStorage.getItem('regProfileInputs').split(',')
            for(let i = 0; i < 3; i++){
                if(regProfileInputs[i] == 'true'){
                    regProfileInputsCheked[i].checked = true;
                }
            }
        }
        if(localStorage.getItem('regLogin')){
            document.querySelector('.reg-form__input[name="reg_login"]').value = localStorage.getItem('regLogin');
        }

        if(localStorage.getItem('regPassword')){
            document.querySelector('.reg-form__input[name="reg_password"]').value = localStorage.getItem('regPassword');
        }

        // checking that the name contains only letters
        document.querySelector('.reg-form__input[name="reg_name"]').addEventListener('input', function() {
            this.value = this.value.replace(/[^А-Яа-яЁёA-Za-z]/g, ''); // if the value is not a letter, then we change it to an empty string
            localStorage.setItem('regName', this.value);
        });

        // checking that the surname contains only letters
        document.querySelector('.reg-form__input[name="reg_surname"]').addEventListener('input', function() {
            this.value = this.value.replace(/[^А-Яа-яЁёA-Za-z]/g, ''); // if the value is not a letter, then we change it to an empty string
            localStorage.setItem('regSurname', this.value);
        });

        // setting values of the form inputs in the localstorage
        document.querySelector('.reg-form__input[name="reg_login"]').addEventListener('input', function() {
            localStorage.setItem('regLogin', this.value);
        });

        document.querySelector('.reg-form__input[name="reg_password"]').addEventListener('input', function() {
            localStorage.setItem('regPassword', this.value);
        });

        // check profile inputs changes and set data in localstorage
        for(let i = 0; i < regProfileInputsCheked.length; i++){
            regProfileInputsCheked[i].addEventListener('change', function(){
                let regProfileInputs = [false, false, false];
                for(let j = 0; j < 3; j++){
                    if(regProfileInputsCheked[j].checked){
                        regProfileInputs[i] = true;
                    }
                }
                localStorage.setItem('regProfileInputs', regProfileInputs); // set array of radio button values(checked or not) in localstorage
            })
        }

        // Switch buttons (login or registration)
        let logButton = document.querySelector('.log-reg__switch-button--log');
        let regButton = document.querySelector('.log-reg__switch-button--reg');
        let regForm = document.querySelector('.reg-form');
        let logForm = document.querySelector('.log-form');
        let regWarning = document.querySelector('.reg-form__warning');


        // if the login button was pressed, then hide the registration form and open the login form
        logButton.addEventListener('click', function(){
            if (logButton.classList.contains('log-reg__switch-button--active') == false){  // checking if the login button is already pressed
                logButton.classList.add('log-reg__switch-button--active');
                regButton.classList.remove('log-reg__switch-button--active');
                logForm.style.cssText = `display: flex;`;
                regForm.style.cssText = `display: none;`;
                localStorage.setItem('SwitchRegLogButton', 'log');
            }
        });

         // if the registrtion button was pressed, then hide the login form and open the registration form
        regButton.addEventListener('click', function(){
            if (regButton.classList.contains('log-reg__switch-button--active') == false){ // checking if the login button is already pressed
                logButton.classList.remove('log-reg__switch-button--active');
                regButton.classList.add('log-reg__switch-button--active');
                regForm.style.cssText = `display: flex;`;
                logForm.style.cssText = `display: none;`;
                localStorage.setItem('SwitchRegLogButton', 'reg');
            }
        });

        // if there are no errors, then we transfer them to registration
        if(!regWarning){
            localStorage.setItem('SwitchRegLogButton', 'log');
        }

        // take the data from the localstorage and open the corresponding forms
        if(localStorage.getItem('SwitchRegLogButton')){
            if(localStorage.getItem('SwitchRegLogButton') == 'reg'){
                regButton.click();
            }
            if(localStorage.getItem('SwitchRegLogButton') == 'log'){
                logButton.click();
            }
        }
        else{ //if there is no data in the localstorage, set the value of the redirect to login
            localStorage.setItem('SwitchRegLogButton', 'log');
        }
    </script>
</body>
</html>