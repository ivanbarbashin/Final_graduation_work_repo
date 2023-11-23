<?php
include "../templates/func.php"; // Include functions file
include "../templates/settings.php"; // Include settings file

if (empty($_GET["id"]) || $_GET["id"] == ""){ // Redirect if the ID is empty or not provided
    header("Location: requests.php"); 
}
// Construct a SQL query to select the user associated with the provided ID and the current user's receiver ID
$sql = "SELECT user FROM requests WHERE id=".$_GET["id"]." AND receiver=".$user_data->get_id();
if ($result = $conn->query($sql)){ // Execute the SQL query
    if ($result->num_rows != 0){ // Check if the result contains any rows
        foreach ($result as $item){
            $user = $item['user']; // Retrieve the user ID from the result
        }
        $sql2 = "DELETE FROM requests WHERE user=$user"; // Construct SQL queries based on the user's status (coach or doctor)
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
        // Execute the SQL queries to delete the request
        $conn->query($sql2);
        $conn->query($sql3);
        if ($sql4 != NULL)
            $conn->query($sql4);
    }
}else{
    echo $conn->error; // Output any database errors
}

header("Location: requests.php"); // Redirect to the requests page after processing the request
