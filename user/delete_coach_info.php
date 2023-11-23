<?php
include "../templates/func.php"; // Include functions file
include "../templates/settings.php"; // Include settings file

if (!$user_data->get_auth() || $user_data->get_status() != "coach") // Check if the user is authenticated and has the role of a coach
    header("Location: profile.php");

// get sportsmen
$sportsmen = $user_data->get_sportsmen();
// get parameters from the URL
$item = $_GET["item"];
$id = $_GET["id"];
$user = $_GET["user"];

if (!is_numeric($user) || !is_numeric($id) || !in_array($id, $sportsmen)) // Check if the parameters are valid numeric values and if the ID is associated with the coach's sportsmen
    header("Location: profile.php");

// get coach data for a specific user
$coach_data = $user_data->get_coach_data($conn, $user);
if ($coach_data == NULL)
    header("Location: profile.php");// Redirect if coach data is not available for the user

// Perform actions based on the specified item type
switch ($item){
    case "goal": // Code to handle deletion of a goal
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
    case "competition": // Code to handle deletion of a competition
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
    case "info": // Code to handle deletion of coach advice or information
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
if ($conn->query($sql)){ // Execute the SQL query to delete the specified item
    header("Location: coach.php?user=$user");
}else{
    echo $conn->error; // Output error message if query execution fails
    sleep(3);
    header("Location: profile.php"); // Redirect to profile page after an error
}