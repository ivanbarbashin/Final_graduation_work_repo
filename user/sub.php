<?php
include "../templates/func.php";
include "../templates/settings.php";

if ($_GET["id"] && $user_data->get_auth() && !in_array($_GET["id"], $user_data->subscriptions)){
    $user = $_GET["id"];
    $subscriber = $user_data->get_id();
    $sql = "INSERT INTO subs (user, subscriber) VALUES ($user, $subscriber)";
    if ($conn->query($sql)){
        header("Location: profile.php?user=$user");
    }else{
        header("Location: ../index.php");
    }
}else{
    header("Location: ../index.php");
}
