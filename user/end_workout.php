<?php
include "../templates/func.php";
include "../templates/settings.php";

$user_data->check_the_login();
$weekday = date("N") - 1;
$user_data->set_program($conn);
$user_id = $user_data->get_id();
$workout_id = $user_data->program->program[$weekday];
$date_completed = time();
$sql = "INSERT INTO workout_history (user, workout, date_completed) VALUES ($user_id, $workout_id, $date_completed)";
if ($conn->query($sql)){
    header("Location: my_program.php");
}else{
    echo $conn->error;
}
