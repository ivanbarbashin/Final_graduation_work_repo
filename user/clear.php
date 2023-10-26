<?php
session_start();
$_SESSION["workout"] = array();
$_SESSION["program"] = array();
header("Location: c_program.php");