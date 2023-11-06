<?php
include "../templates/func.php";
include "../templates/settings.php";
$user_data->set_subscriptions($conn);

if (isset($_GET["header"]) && $_GET["header"] == 0)
    $header = 0;
else
    $header = 1;

if ($_GET["id"] && $user_data->get_auth() && !in_array($_GET["id"], $user_data->subscriptions)){
    $user = $_GET["id"];
    $subscriber = $user_data->get_id();
    $sql = "INSERT INTO subs (user, subscriber) VALUES ($user, $subscriber)";
    if ($conn->query($sql)){
        if ($header)
            header("Location: profile.php?user=$user");
        else
            header("Location: search_users.php");
    }else{
        header("Location: ../index.php");
    }
}else{
    header("Location: ../index.php");
}
