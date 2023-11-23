<?php

# Host settings

// Define constants for database connection
define("HOSTNAME", "localhost");
define("HOSTUSER", "root");
define("HOSTPASSWORD", "");
define("HOSTDB", "sport");

date_default_timezone_set('Europe/Moscow'); // Set default timezone to 'Europe/Moscow'

$conn = new mysqli(HOSTNAME, HOSTUSER, HOSTPASSWORD, HOSTDB); // Create a MySQLi database connection
conn_check($conn); // Assuming conn_check is a function that checks the connection

session_start(); // Start the PHP session

$user_data = new User($conn); // Instantiate a User object
if (isset($_SESSION["user"])){ // Check if the session contains a "user" variable
    $user_data = new User($conn, $_SESSION["user"], true); // Create a User object with the session user ID
}
