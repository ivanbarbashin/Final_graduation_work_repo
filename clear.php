<?php
session_start(); // Starting a session or resuming an existing one
unset($_SESSION['user']); // Removing the 'user' variable from the session array if it was set
header("Location: index.php"); // Redirecting the user to the index.php page
?>