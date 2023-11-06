<?php
session_start();
$_SESSION["c_workout"] = array();
header("Location: c_control_workout.php?for=". $_GET["for"]);