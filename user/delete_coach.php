<?php
include "../templates/func.php";
include "../templates/settings.php";

if (!$user_data->get_auth()){
    header("Location: profile.php");
}
$sql = "DELETE FROM user_to_coach WHERE user=".$user_data->get_id();
if ($conn->query($sql)){
    header("Location: profile.php");
}else{
    echo $conn->error;
    sleep(3);
    header("Location: profile.php");
}
