<?php
session_start(); // Start or resume the current session
// Check if 'program' is set in the session and 'id' is provided in the URL
if (isset($_SESSION["program"]) && isset($_GET["id"])){
    for ($i = 0; $i < 7; $i ++){ // Loop through each day of the week (7 days)
        if ($_SESSION["program"][$i] == $_GET["id"]){ // Check if the 'id' from the URL matches any day's program ID in the session
            $_SESSION["program"][$i] = 0; // If found, set the matched day's program ID to 0
        }
    }
}

header("Location: c_program.php"); // Redirect the user to 'c_program.php' page after modifying session data