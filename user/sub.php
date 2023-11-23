<?php
include "../templates/func.php"; // Include functions file
include "../templates/settings.php"; // Include settings file
$user_data->set_subscriptions($conn); // Set user's subscriptions

// Check if the 'header' parameter exists in the URL and set the header flag
if (isset($_GET["header"]) && $_GET["header"] == 0)
    $header = 0;
else
    $header = 1;

// Check for 'id' parameter in URL, user authentication, and subscription
if ($_GET["id"] && $user_data->get_auth() && !in_array($_GET["id"], $user_data->subscriptions)){
    $user = $_GET["id"]; // Assign 'id' from URL to $user
    $subscriber = $user_data->get_id(); // Get current user's ID and assign to $subscriber
    $sql = "INSERT INTO subs (user, subscriber) VALUES ($user, $subscriber)"; // SQL query to insert subscription entry into 'subs' table
    if ($conn->query($sql)){  // Execute SQL query and perform redirection based on success or failure
        if ($header)
            header("Location: profile.php?user=$user"); // Redirect to profile page if header flag is set
        else
            header("Location: search_users.php"); // Redirect to search users page if header flag is not set
    }else{
        header("Location: ../index.php"); // Redirect to index page in case of SQL query failure
    }
}else{
    header("Location: ../index.php"); // Redirect to index page
}
