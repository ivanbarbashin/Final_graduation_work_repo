<?php
session_start(); // Start a session to manage session variables
// Initialize session variables as empty arrays
$_SESSION["workout"] = array();
$_SESSION["program"] = array();
header("Location: c_program.php"); // Redirect the user to the c_program.php page