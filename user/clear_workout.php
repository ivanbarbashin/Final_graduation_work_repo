<?php
session_start();// Start a session to manage session variables
$_SESSION["workout"] = array(); // Initialize session variables as empty array
header("Location: c_workout.php"); // Redirect the user to the c_workout.php page