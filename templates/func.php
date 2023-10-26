<?php
date_default_timezone_set("Europe/Moscow");
require_once "User.php";
require_once "Report.php";
require_once "Exercise.php";
require_once "Program.php";
require_once "Workout.php";

function conn_check($conn){
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }else{
        return true;
    }
}

function render($replaces, $tpl_filename){
    $tpl = file_get_contents($tpl_filename);
    $tpl = strtr($tpl, $replaces);
    return $tpl;
}

function inc_head($title="OpenDoor", $main_dir=false){
    if ($main_dir){
        $way = '';
    }else{
        $way = '../';
    }
    include "head.php";
}

function log_warning($if, $warning){
    if (isset($_POST['log_done'])){
        if ($if){
            echo "<p class='warning'>".$warning."</p>";
        }
    }
}

function reg_warning($if, $warning){
    if (isset($_POST['reg_done'])){
        if ($if){
            echo "<p class='warning'>".$warning."</p>";
        }
    }
}

function translate_group ($group){
    switch ($group){
        case "arms":
            return "Руки";
        case "legs":
            return "Ноги";
        case "press":
            return "Пресс";
        case "back":
            return "Спина";
        case "chest":
            return "Грудь";
        case "cardio":
            return "Кардио";
    }
}

function get_day($day_number) {
    // Create an array with day names
    $days_of_week = array("Понедельник", "Вторник", "Среда", "Четверг", "Пятница", "Суббота", "Воскресенье");
    // Check if the day number is within a valid range (1-7)
    // Get the corresponding day of the week
    $day_of_week = $days_of_week[$day_number];
    return $day_of_week;
}

function insert_news($conn, $message, $user_id, $is_personal){
    $date = time();
    $is_personal = (int)$is_personal;
    $sql = "INSERT INTO news (message, user, date, personal) VALUES ('$message', $user_id, $date, $is_personal)";
    if ($conn->query($sql)){
        return mysqli_insert_id($conn);
    }else{
        echo $conn->error;
        return false;
    }
}

function print_user_list($conn, $id_list){
    if (count($id_list) == 0){
        echo "<p class='friends-block__no-friends'>Нет подписок</p>";
        return 0;
    }
    for ($i = 0; $i < 4; $i++){
        echo "<section class='friends-block__slide'>";
        $user_id = $id_list[$i];
        $user = new User($conn, $user_id);
        $replacements = array(
            "{{ id }}" => $user->get_id(),
            "{{ avatar }}" => $user->get_avatar($conn),
            "{{ name }}" => $user->name
        );
        echo render($replacements, "../templates/user_card.html");
        echo "</section>";
    }
}


function print_user_list_vert($conn, $id_list){
    if (count($id_list) == 0){
        echo "<p class='friends-block__no-friends'>Нет подписок</p>";
        return 0;
    }
    for ($i = 0; $i < 2; $i++){
        echo "<section class='friends-block__slide'>";
        $user_id = $id_list[$i];
        $user = new User($conn, $user_id);
        $replacements = array(
            "{{ id }}" => $user->get_id(),
            "{{ avatar }}" => $user->get_avatar($conn),
            "{{ name }}" => $user->name
        );
        echo render($replacements, "../templates/user_card.html");
        echo "</section>";
    }
}

function print_workout_info_function($workout){
    $muscles = array(
        "arms" => 0,
        "legs" => 0,
        "press" => 0,
        "back" => 0,
        "chest" => 0,
        "cardio" => 0,
        "cnt" => 0
    );
    foreach ($workout as $exercise){
        foreach ($exercise->muscles as $muscle){
            $muscles[$muscle]++;
            $muscles['cnt']++;
        }
    }
    foreach ($muscles as $muscle=>$value){
        if ($value != 0){
            $muscles[$muscle] = round($value / $muscles['cnt'] * 100, 0);
        }
    }
    ?>
    <div class="muscle_groups">
        <p class="workouts-card__item">Руки: <span><?php echo $muscles["arms"]; ?>%</span></p>
        <p class="workouts-card__item">Ноги: <span><?php echo $muscles["legs"]; ?>%</span></p>
        <p class="workouts-card__item">Грудь: <span><?php echo $muscles["chest"]; ?>%</span></p>
        <p class="workouts-card__item">Спина: <span><?php echo $muscles["back"]; ?>%</span></p>
        <p class="workouts-card__item">Пресс: <span><?php echo $muscles["press"]; ?>%</span></p>
        <p class="workouts-card__item">Кардио: <span><?php echo $muscles["cardio"]; ?>%</span></p>
    </div>
<?php
}

function busy_or_free($id){
    if ($id == 0)
        echo "free";
    else
        echo "busy";
}

function get_graph_workout_data($history){
    $currentYear = date("Y"); // Get the current year
    $result = array_fill(0, 12, 0); // Initialize an array with 12 zeros for each month

    foreach ($history as $workout) {
        $timestamp = $workout["date_completed"];
        $month = date("n", $timestamp); // Get the month (1 to 12) of the timestamp

        // Check if the workout is in the current year
        if (date("Y", $timestamp) == $currentYear) {
            $result[$month - 1]++; // Increment the count for the corresponding month
        }
    }

    return $result;
}

function get_exercise_muscles($conn, $exercise_id){
    $sql = "SELECT muscles FROM exercises WHERE id=$exercise_id";
    if ($result = $conn->query($sql)){
        foreach ($result as $item){
            return json_decode($item['muscles']);
        }
    }
}

function print_user_block($name, $surname, $file, $id, $is_subscribed=false){
    if ($is_subscribed){
        $button = '<button class="button-text user-card__button user-card__button--add"><p>У вас в друзьях</p><img src="../img/add.svg" alt=""></button>';
    }else{
        $button = '<button class="button-text user-card__button user-card__button--add"><p>Добавить в друзья</p><img src="../img/add.svg" alt=""></button>';
    }
    $replacements = array(
        "{{ name }}" => $name." ".$surname,
        "{{ image }}" => $file,
        "{{ button }}" => $button,
        "{{ link }}" => "profile.php?user=".$id
    );
    echo render($replacements, "../templates/user_block.html");
}

function print_user_block_request($name, $surname, $file, $id, $request_id){
    $replacements = array(
        "{{ name }}" => $name." ".$surname,
        "{{ image }}" => $file,
        "{{ link }}" => "profile.php?user=".$id,
        "{{ button_accept }}" => "<a class='button-text user-card__button user-card__button--except' href='accept_request.php?id=$request_id'>Принять</a>",
        "{{ button_deny }}" => "<a class='button-text user-card__button user-card__button--reject' href='deny_request.php?id=$request_id'>Отклонить</a>"
    );
    echo render($replacements, "../templates/user_block_request.html");
}

