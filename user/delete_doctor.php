<?php
include "../templates/func.php"; // Include functions file
include "../templates/settings.php"; // Include settings file

if (!$user_data->get_auth()){ // Check if the user is authenticated
    header("Location: profile.php"); // Redirect if not authenticated
}
// Define SQL queries to delete user-to-doctor relationship and doctor data
$sql = "DELETE FROM user_to_doctor WHERE user=".$user_data->get_id();
$sql2 = "DELETE FROM doctor_data WHERE user=".$user_data->get_id();
if ($conn->query($sql) && $conn->query($sql2)){ // Attempt to execute the deletion queries
    header("Location: profile.php"); // Redirect to profile after successful deletion
}else{
    echo $conn->error; // Output error message if query execution fails
    sleep(3);
    header("Location: profile.php"); // Redirect to profile page after an error
}
