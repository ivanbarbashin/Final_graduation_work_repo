<?php
include "../templates/func.php";
include "../templates/settings.php";

if (empty($_POST["id"]) || $_POST["id"] == '')
    header("Location: coach.php");
$workout = new Control_Workout($conn, $_GET["id"]);
/*if ($workout->creator != $user_data->get_id())
    header("Location: coach.php");*/

foreach ($_POST["reps"] as $rep)
    if ($rep == '')
        $rep = 0;

$reps = json_encode($_POST["reps"]);
$sql = "UPDATE control_workouts SET is_done=1, reps='$reps' WHERE id=".$_POST["id"];

if (!$conn->query($sql))
    echo $conn->error;
header("Location: coach.php");