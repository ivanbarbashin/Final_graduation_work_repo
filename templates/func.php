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

function log_warning($if, $error){
    if ($if){
        echo "<p class='reg-form__warning'>".$error."</p>";
    }
}

function reg_warning($if, $error){
    if ($if){
        echo "<p class='reg-form__warning'>".$error."</p>";
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
        if (count($id_list) == $i)
            break;
        $user_id = $id_list[$i];
        if ($user_id == 0 || $user_id == NULL)
            break;
        echo "<section class='friends-block__slide'>";
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

function get_graph_workout_data_year($history){
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

function get_graph_workout_data_month($timestamps) {
    $currentMonth = date('n'); // Получаем текущий месяц
    $weeksData = array_fill(0, 5, 0);
    foreach ($timestamps as $timestamp) {
        $timestamp = $timestamp["date_completed"];
        $dateMonth = date('n', $timestamp);
        if ($dateMonth == $currentMonth) {
            $weekOfMonth = (int)date('W', $timestamp) - (int)date('W', strtotime(date('Y-m-01', $timestamp))) + 1;
            if ($weekOfMonth >= 1 && $weekOfMonth <= 5) {
                $weeksData[$weekOfMonth - 1]++;
            }
        }
    }

    return $weeksData;
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
        $button = '<a class="button-text user-block__sub-button" href="unsub.php?header=0&id='.$id.'">Отписаться</a>';
    }else {
        $button = '<a class="button-text user-block__sub-button" href="sub.php?header=0&id='.$id.'"><p>Подписаться</p><img src="../img/add.svg" alt=""></a>';
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

function print_medicine($conn, $id, $user_id, $edit=true){
    $sql = "SELECT name, caption FROM medicines WHERE id=$id";
    if ($result = $conn->query($sql)){
        foreach ($result as $item){
            $update = '';
            if ($edit)
                $delete = '<a href="delete_medicine.php?option=delete&user='.$user_id.'&id='.$id.'" class="button-img staff-block__item-button"><img src="../img/delete.svg" alt=""></a>';
            else
                $delete = '';
            $reps = array("{{ name }}" => $item["name"], "{{ caption }}" => $item["caption"], "{{ update }}" => $update, "{{ delete }}" => $delete);
            echo render($reps, "../templates/medicine.html");
        }
    }else{
        echo $conn->error;
    }
}

function print_goal($conn, $id, $user_id, $edit=true){
    $sql = "SELECT name, done FROM goals WHERE id=$id";
    if ($result = $conn->query($sql)){
        foreach ($result as $item){
            if ((int)$item["done"]){
                $checkmark = "../img/green_check_mark.svg";
                if ($edit)
                    $done = '<a class="staff-block__goal-button--text" href="goal_done.php?id='.$id.'&val=0&user='.$user_id.'">Не выполнена</a>';
                else
                    $done = '';
            }else{
                $checkmark = "../img/blue_question_mark.svg";
                if ($edit)  
                    $done = '<a class="staff-block__goal-button--text" href="goal_done.php?id='.$id.'&val=1&user='.$user_id.'">Выполнена</a>';
                else
                    $done = '';
            }
            if ($edit)
                $delete = '<a href="delete_coach_info.php?item=goal&user='.$user_id.'&id='.$id.'" class="button-img staff-block__item-button"><img src="../img/delete.svg" alt=""></a>';
            else
                $delete = '';
            $reps = array("{{ name }}" => $item["name"], "{{ done }}" => $done, "{{ checkmark }}" => $checkmark, "{{ delete }}" => $delete);
            echo render($reps, "../templates/goal.html");
        }
    }else{
        echo $conn->error;
    }
}

function print_competition($conn, $id, $user_id, $edit=true){
    $sql = "SELECT name, link, date FROM competitions WHERE id=$id";
    if ($result = $conn->query($sql)){
        foreach ($result as $item){
            if ($item["link"] == NULL)
                $link = "";
            else
                $link = '<a class="staff-block__link-button staff-block__link-button--competitions-link" href="'.$item["link"].'"><img src="../img/link.svg" alt=""></a>';

            if ($item["date"] == NULL)
                $date = "Дата не указана";
            else
                $date = date("d.m.Y", $item["date"]);
            if ($edit)
                $delete = '<a href="delete_coach_info.php?item=competition&user='.$user_id.'&id='.$id.'" class="button-img staff-block__item-button"><img src="../img/delete.svg" alt=""></a>';
            else
                $delete = '';
            $reps = array("{{ name }}" => $item["name"], "{{ link }}" => $link, "{{ date }}" => $date, "{{ delete }}" => $delete);
            echo render($reps, "../templates/competition.html");
        }
    }else{
        echo $conn->error;
    }
}

function print_advice($conn, $id, $user_id, $edit=true){
    $sql = "SELECT name, link FROM coach_advice WHERE id=$id";
    if ($result = $conn->query($sql)){
        foreach ($result as $item){
            if ($item["link"] == NULL)
                $link = "";
            else
                $link = '<a class="staff-block__link-button staff-block__link-button--info-link" href="'.$item["link"].'"><img src="../img/link.svg" alt=""></a>';
            if ($edit)
                $delete = '<a href="delete_coach_info.php?item=info&user='.$user_id.'&id='.$id.'" class="button-img staff-block__item-button"><img src="../img/delete.svg" alt=""></a>';
            else
                $delete = '';
            $reps = array("{{ name }}" => $item["name"], "{{ link }}" => $link, "{{ delete }}" => $delete);
            echo render($reps, "../templates/advice.html");
        }
    }else{
        echo $conn->error;
    }
}

function get_reps_for_comparison($user, $conn, $user_number, $second_user_id){
    $phys_data = $user->get_current_phys_data($conn);
    $tg = '';
    if ($user->vk != NULL)
        $tg = '<a href="'.$user->vk.'" class="staff-block__button staff-block__button--img"><img src="../img/vk.svg" alt=""></a>';
    $vk = '';
    if ($user->tg != NULL)
        $vk = '<a href="'.$user->tg.'" class="staff-block__button staff-block__button--img"><img src="../img/tg.svg" alt=""></a>';
    if ($user_number == 1)
        $delete = "users_comparison.php?user1=&user2=$second_user_id";
    else
        $delete = "users_comparison.php?user1=$second_user_id&user2=";
    $reps = array(
        "{{ name }}" => $user->name,
        "{{ surname }}" => $user->surname,
        "{{ avatar }}" => $user->get_avatar($conn),
        "{{ vk_button }}" => $vk,
        "{{ tg_button }}" => $tg,
        "{{ delete_link }}" => $delete,
        "{{ weight }}" => $phys_data["weight"],
        "{{ height }}" => $phys_data["height"],
        "{{ id }}" => $user->get_id()
    );

    return $reps;
}

function print_sportsman_block($conn, $sportsman){
    $replacements = array(
        "{{ name }}" => $sportsman->name,
        "{{ image }}" => $sportsman->get_avatar($conn),
        "{{ button }}" => "<a class='button-text user-card__button' href='coach.php?user=".$sportsman->get_id()."'>Выбрать</a>",
        "{{ link }}" => "profile.php?user=".$sportsman->get_id()
    );
    echo render($replacements, "../templates/user_block.html");
}

function in_workout($workout, $id){
    $flag = false;
    foreach ($workout as $user_exercise){
        if ($user_exercise->get_id() == $id){
            $flag = true;
            break;
        }
    }
    return $flag;
}
