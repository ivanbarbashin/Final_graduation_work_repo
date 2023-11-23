<?php
include "../templates/func.php"; // Include functions file
include "../templates/settings.php"; // Include settings file

if (empty($_GET["id"]) || $_GET["id"] == ""){ // Redirect if the 'id' is empty or not provided
    header("Location: profile.php");
}
$user_data->set_staff($conn); // Set or update staff information in the user data object using the database connection
$user = new User($conn, $_GET["id"]); // Create a new User object based on the provided 'id' from the GET parameters
$request_flag = $user_data->check_request($conn, $user->get_id()); // Check if a request has already been sent

// Condition to send a request:
// - The user is not authenticated
// - The current user is of 'user' status
// - The target user is either a 'coach' without a current coach or a 'doctor' without a current doctor
// - There's no existing request sent
if (!$user->get_auth() && $user_data->get_status() == "user" && (($user->get_status() == "coach" && $user_data->coach == NULL) || ($user->get_status() == "doctor" && $user_data->doctor == NULL)) && !$request_flag){
    $sql = "INSERT INTO requests (user, receiver) VALUES (".$user_data->get_id().", ".$user->get_id().")";
    if ($conn->query($sql)){ // Attempt to execute the SQL query to send the request
        header("Location: profile.php?user=".$user->get_id());
    }else{ // If there's an error, display the error and redirect after 3 seconds
        echo $conn->error;
        sleep(3);
        header("Location: profile.php?user=".$user->get_id());
    }
}
