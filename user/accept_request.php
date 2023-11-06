<?php
include "../templates/func.php";
include "../templates/settings.php";

if (empty($_GET["id"]) || $_GET["id"] == ""){
    header("Location: requests.php");
}
$sql = "SELECT user FROM requests WHERE id=".$_GET["id"]." AND receiver=".$user_data->get_id();
if ($result = $conn->query($sql)){
    if ($result->num_rows != 0){
        foreach ($result as $item){
            $user = $item['user'];
        }
        $sql2 = "DELETE FROM requests WHERE user=$user";
        switch ($user_data->get_status()){
            case "coach":
                $sql3 = "INSERT INTO user_to_coach(user, coach) values ($user, ".$user_data->get_id().")";
                $sql4 = "INSERT INTO coach_data(user, coach) values ($user, ".$user_data->get_id().")";
                break;
            case "doctor":
                $sql3 = "INSERT INTO user_to_doctor(user, doctor) values ($user, ".$user_data->get_id().")";
                $sql4 = "INSERT INTO doctor_data(user, doctor) values ($user, ".$user_data->get_id().")";
                break;
        }
        $conn->query($sql2);
        $conn->query($sql3);
        if ($sql4 != NULL)
            $conn->query($sql4);
    }
}else{
    echo $conn->error;
}

header("Location: requests.php");
