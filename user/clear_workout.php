<?php
session_start();
$_SESSION["workout"] = array();
header("Location: c_workout.php");