<?php

# Host settings

define("HOSTNAME", "localhost");
define("HOSTUSER", "root");
define("HOSTPASSWORD", "");
define("HOSTDB", "sport");

date_default_timezone_set('Europe/Moscow');

$conn = new mysqli(HOSTNAME, HOSTUSER, HOSTPASSWORD, HOSTDB);
conn_check($conn);

session_start();

$user_data = new User($conn);
if (isset($_SESSION["user"])){
    $user_data = new User($conn, $_SESSION["user"], true);
}
