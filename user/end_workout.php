<?php
include "../templates/func.php"; // Include functions file
include "../templates/settings.php"; // Include settings file

$user_data->check_the_login(); // Checking user login status
// Getting the current weekday and setting the user's program
$weekday = date("N") - 1;
$user_data->set_program($conn);
// Getting user and workout IDs, as well as the current date and time
$user_id = $user_data->get_id();
$workout_id = $user_data->program->program[$weekday];
$date_completed = time();
// get the time spent on the workout
if (empty($_POST["time"]) || !is_numeric($_POST["time"]))
    $time = 0; // If time isn't set or not numeric, default to 0
else
    $time = (int)$_POST["time"]; // Otherwise, parse and set the time spent

// SQL query to insert workout history into the database
$sql = "INSERT INTO workout_history (user, workout, date_completed, time_spent) VALUES ($user_id, $workout_id, $date_completed, $time)";
if ($conn->query($sql)){ // Executing the SQL query and redirecting based on success
    header("Location: my_program.php");
}else{
    echo $conn->error;
}
