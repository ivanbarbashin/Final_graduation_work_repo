<?php
include "../templates/func.php";
include "../templates/settings.php";

if (!$user_data->get_auth() || $user_data->get_status() != "doctor")
    header("Location: profile.php");

$sportsmen = $user_data->get_sportsmen();
$item = $_GET["option"];
$id = $_GET["id"];
$user = $_GET["user"];

if (!is_numeric($user) || !is_numeric($id) || !in_array($id, $sportsmen))
    header("Location: profile.php");

$doctor_data = $user_data->get_doctor_data($conn, $user);
if ($coach_data == NULL)
    header("Location: profile.php");

switch ($item){
    case "update":
        # suka
        break;
    case "delete":
        if ($doctor_data["medicines"] == NULL)
            header("Location: profile.php");
        $doctor_data["medicines"] = json_decode($doctor_data["medicines"]);
        $new_medicines = array();
        foreach ($doctor_data["medicines"] as $medicine_id)
            if ($medicine_id != $id)
                array_push($new_medicines, $medicine_id);
        $doctor_data["medicines"] = json_encode($new_medicines, 256);
        $user_data->update_doctor_data($conn, $doctor_data);
        $sql = "DELETE FROM medicines WHERE id=$id";
        break;
}
if ($conn->query($sql)){
    header("Location: doctor.php?user=$user");
}else{
    echo $conn->error;
    sleep(3);
    header("Location: profile.php");
}
