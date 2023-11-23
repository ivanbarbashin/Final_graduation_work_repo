<?php
include "../templates/func.php"; // Include functions file
include "../templates/settings.php"; // Include settings file

if (!$user_data->get_auth() || $user_data->get_status() != "doctor") // Check user authentication and status
    header("Location: profile.php"); // Redirect if not authenticated or not a doctor

$sportsmen = $user_data->get_sportsmen();
$item = $_GET["option"];
$id = $_GET["id"];
$user = $_GET["user"];

if (!is_numeric($user) || !is_numeric($id) || !in_array($id, $sportsmen)) // Check the validity of parameters
    header("Location: profile.php"); // Redirect if parameters are invalid

$doctor_data = $user_data->get_doctor_data($conn, $user);
if ($coach_data == NULL) // Redirect if doctor data is not found
    header("Location: profile.php");

switch ($item){
    case "update": // get update functionality
        break;
    case "delete": // get delete functionality for medicines
        if ($doctor_data["medicines"] == NULL)
            header("Location: profile.php");

        // update medicines data
        $doctor_data["medicines"] = json_decode($doctor_data["medicines"]);
        $new_medicines = array();
        foreach ($doctor_data["medicines"] as $medicine_id)
            if ($medicine_id != $id)
                array_push($new_medicines, $medicine_id);
        $doctor_data["medicines"] = json_encode($new_medicines, 256);
        $user_data->update_doctor_data($conn, $doctor_data); // Update doctor data with the modified medicines list
        $sql = "DELETE FROM medicines WHERE id=$id"; // Delete the medicine record from the database
        break;
}
if ($conn->query($sql)){ // Execute the SQL query and get redirection or error
    header("Location: doctor.php?user=$user");
}else{
    echo $conn->error; // Output error if query execution fails
    sleep(3);
    header("Location: profile.php"); // Redirect to profile page after an error
}
