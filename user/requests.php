<?php
include "../templates/func.php"; // Include functions file
include "../templates/settings.php"; // Include settings file

if ($user_data->get_status() != "coach" && $user_data->get_status() != "doctor"){ // Redirect if the user status is not coach and not doctor
    header("Location: search_users.php");
}
$requests = $user_data->get_requests(); // Fetch and store requests for the current user
?>
<!DOCTYPE html>
<html lang="en">
<?php inc_head(); // print head.php ?>
<body>
    <?php include "../templates/header.php"; // print header template ?>

	<main class="users-list">
		<h1 class="users-list__title">Входящие заявки</h1>
		<div class="container">
            <?php
            if (count($requests) != 0){
                foreach ($requests as $user_block){ // Loop through the requests and display user blocks for each request
                    $user = new User($conn, $user_block['user']); // Create a User object based on the user ID in the request
                    print_user_block_request($user->name, $user->surname, $user->get_avatar($conn), $user->get_id(), $user_block["id"]); // Print a user block for the request
                }
            }else{ // Display a message if there are no requests ?>
                </div>
                <p class="users-list__no-users">Нет заявок</p>
            <?php }
            ?>
        </div>
	</main>

    <?php include "../templates/footer.html"; // print footer template ?>
</body>
</html>