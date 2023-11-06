<?php
include "../templates/func.php";
include "../templates/settings.php";

if (!$user_data->get_auth() || $user_data->get_status() != "coach")
    header("Location: profile.php");

$sportsmen = $user_data->get_sportsmen();
$done = (int)$_GET["val"];
$id = $_GET["id"];
$user = $_GET["user"];

if (!is_numeric($user) || !is_numeric($id) || !in_array($id, $sportsmen) || ($done != 0 && $done != 1))
    header("Location: profile.php");

$sql = "UPDATE goals SET done=$done WHERE id=$id";
if ($conn->query($sql)){
    header("Location: coach.php?user=$user");
}else{
    echo $conn->error;
    sleep(3);
    header("Location: profile.php");
}