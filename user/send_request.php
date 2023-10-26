<?php
include "../templates/func.php";
include "../templates/settings.php";

if (empty($_GET["id"]) || $_GET["id"] == ""){
    header("Location: profile.php");
}
$user_data->set_staff($conn);
$user = new User($conn, $_GET["id"]);
$request_flag = $user_data->check_request($conn, $user->get_id());
if (!$user->get_auth() && $user_data->get_status() == "user" && (($user->get_status() == "coach" && $user_data->coach == NULL) || ($user->get_status() == "doctor" && $user_data->doctor == NULL)) && !$request_flag){
    $sql = "INSERT INTO requests (user, receiver) VALUES (".$user_data->get_id().", ".$user->get_id().")";
    if ($conn->query($sql)){
        header("Location: profile.php?user=".$user->get_id());
    }else{
        echo $conn->error;
        sleep(3);
        header("Location: profile.php?user=".$user->get_id());
    }
}