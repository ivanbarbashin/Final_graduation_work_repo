<?php
session_start(); // Start the session
$_SESSION["c_workout"] = array(); // Initialize the session variable 'c_workout' as an empty array
header("Location: c_control_workout.php?for=". $_GET["for"]); // Redirect to 'c_control_workout.php' passing the 'for' parameter from the URL