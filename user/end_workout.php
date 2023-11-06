<?php
include "../templates/func.php";
include "../templates/settings.php";

$user_data->check_the_login();
$weekday = date("N") - 1;
$user_data->set_program($conn);
$user_id = $user_data->get_id();
$workout_id = $user_data->program->program[$weekday];
$date_completed = time();
if (empty($_POST["time"]) || !is_numeric($_POST["time"]))
    $time = 0;
else
    $time = (int)$_POST["time"];

$sql = "INSERT INTO workout_history (user, workout, date_completed, time_spent) VALUES ($user_id, $workout_id, $date_completed, $time)";
if ($conn->query($sql)){
    header("Location: my_program.php");
}else{
    echo $conn->error;
}
