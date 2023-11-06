<?php
include "../templates/func.php";
include "../templates/settings.php";

if (!$user_data->get_auth()){
    header("Location: profile.php");
}
$sql = "DELETE FROM user_to_doctor WHERE user=".$user_data->get_id();
$sql2 = "DELETE FROM doctor_data WHERE user=".$user_data->get_id();
if ($conn->query($sql) && $conn->query($sql2)){
    header("Location: profile.php");
}else{
    echo $conn->error;
    sleep(3);
    header("Location: profile.php");
}
