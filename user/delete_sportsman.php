<?php
include "../templates/func.php";
include "../templates/settings.php";

if (!$user_data->get_auth() || ($user_data->get_status() != "coach" && $user_data->get_status() != "doctor") || empty($_GET["user"]) || $_GET["user"] == ''){
    header("Location: profile.php");
}
$sql2 = "DELETE FROM user_to_coach WHERE coach=".$user_data->get_id()." AND user=".$_GET["user"];
$sql1 = "DELETE FROM user_to_doctor WHERE doctor=".$user_data->get_id()." AND user=".$_GET["user"];
$sql3 = "DELETE FROM doctor_data WHERE doctor=".$user_data->get_id()." AND user=".$_GET["user"];
$sql4 = "DELETE FROM coach_data WHERE coach=".$user_data->get_id()." AND user=".$_GET["user"];

switch ($user_data->get_status()){
    case "coach":
        $conn->query($sql2);
        $conn->query($sql4);
        header("Location: coach.php");
        break;
    case "doctor":
        $conn->query($sql1);
        $conn->query($sql3);
        header("Location: doctor.php");
        break;
    default:
        header("Location: profile.php");
}
