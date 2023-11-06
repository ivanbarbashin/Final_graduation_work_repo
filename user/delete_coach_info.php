<?php
include "../templates/func.php";
include "../templates/settings.php";

if (!$user_data->get_auth() || $user_data->get_status() != "coach")
    header("Location: profile.php");

$sportsmen = $user_data->get_sportsmen();
$item = $_GET["item"];
$id = $_GET["id"];
$user = $_GET["user"];

if (!is_numeric($user) || !is_numeric($id) || !in_array($id, $sportsmen))
    header("Location: profile.php");

$coach_data = $user_data->get_coach_data($conn, $user);
if ($coach_data == NULL)
    header("Location: profile.php");

switch ($item){
    case "goal":
        if ($coach_data["goals"] == NULL)
            header("Location: profile.php");
        $coach_data["goals"] = json_decode($coach_data["goals"]);
        $new_goals = array();
        foreach ($coach_data["goals"] as $goal_id)
            if ($goal_id != $id)
                array_push($new_goals, $goal_id);
        $coach_data["goals"] = json_encode($new_goals, 256);
        $user_data->update_coach_data($conn, $coach_data);
        $sql = "DELETE FROM goals WHERE id=$id";
        break;
    case "competition":
        if ($coach_data["competitions"] == NULL)
            header("Location: profile.php");
        $coach_data["competitions"] = json_decode($coach_data["competitions"]);
        $new_competitions = array();
        foreach ($coach_data["competitions"] as $competition_id)
            if ($competition_id != $id)
                array_push($new_competitions, $competition_id);
        $coach_data["competitions"] = json_encode($new_competitions, 256);
        $user_data->update_coach_data($conn, $coach_data);
        $sql = "DELETE FROM competitions WHERE id=$id";
        break;
    case "info":
        if ($coach_data["info"] == NULL)
            header("Location: profile.php");
        $coach_data["info"] = json_decode($coach_data["info"]);
        $new_info = array();
        foreach ($coach_data["info"] as $info_id)
            if ($info_id != $id)
                array_push($new_info, $info_id);
        $coach_data["info"] = json_encode($new_info, 256);
        $user_data->update_coach_data($conn, $coach_data);
        $sql = "DELETE FROM coach_advice WHERE id=$id";
        break;
}
if ($conn->query($sql)){
    header("Location: coach.php?user=$user");
}else{
    echo $conn->error;
    sleep(3);
    header("Location: profile.php");
}