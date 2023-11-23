<?php
include "../templates/func.php"; // Include functions file
include "../templates/settings.php"; // Include settings file

if (isset($_GET["subs"]) && $_GET["subs"] == 1) // Check if the 'subs' parameter is set and assign a value based on its presence
    $subs = 1;
else
    $subs = 0;

$user_data->set_subscriptions($conn); // Set or update user subscriptions using the provided database connection

$users_array = array(); // Initialize an empty array to store user information
if (isset($_POST['search']) && trim($_POST['search']) != ""){ // Check if a search has been initiated
    $searches = explode(' ', $_POST['search']); // Split the search query into individual words for search
    foreach ($searches as $search){
        // Execute SQL query to get users' information based on search criteria
        $sql = "SELECT users.name, users.surname, users.id, avatars.file FROM users INNER JOIN avatars ON users.avatar=avatars.id WHERE users.id!=".$user_data->get_id()." AND (users.login LIKE '%$search%' OR users.name LIKE '%$search%' OR users.surname LIKE '%$search%')";
        $result = $conn->query($sql);
        foreach ($result as $item){
            // Check and add unique user information based on subscription status and duplicates
            if (!in_array($item, $users_array) && $subs == 0)
                array_push($users_array, $item);
            else if (!in_array($item, $users_array) && $subs == 1 && in_array($item["id"], $user_data->subscriptions))
                array_push($users_array, $item);
        }
    }
}else {
    //get all users' information when no search query is provided
    $sql = "SELECT users.name, users.surname, users.id, avatars.file FROM users INNER JOIN avatars ON users.avatar=avatars.id WHERE users.id!=".$user_data->get_id();
    $result = $conn->query($sql);
    foreach ($result as $item){
        // Check and add unique user information based on subscription status
        if (!$subs)
            array_push($users_array, $item);
        else if ($subs == 1 && in_array($item["id"], $user_data->subscriptions))
            array_push($users_array, $item);
    }
}

$user_data->set_subscriptions($conn); // Refresh user subscriptions data based on the provided database connection
?>
<!DOCTYPE html>
<html lang="en">
<?php inc_head(); // print head.php ?>
<body>
    <?php include "../templates/header.php" // include header template ?>

	<nav class="users-search-nav">
		<div class="container">
			<!-- Buttons to (sub / all) users -->
            <?php if ($subs){ ?>
                <a class="button-text users-search-nav__item" href="search_users.php?subs=0"><p>Все</p><img src="../img/arrow_white.svg" alt=""></a>
            <?php } else { ?>
                <a class="button-text users-search-nav__item" href="search_users.php?subs=1"><p>Подписки</p><img src="../img/arrow_white.svg" alt=""></a>
            <?php } ?>
			<!-- users search -->
			<form method="post" class="users-search-nav__search">
				<input class="users-search-nav__search-input" type="text" placeholder="Искать" name="search">
				<p class="exercises-nav__search-img"><img src="../img/search_black.svg" alt=""></p>
			</form>
		</div>
	</nav>

	<main class="users-list">
		<div class="container">
            <?php
            if (count($users_array) != 0){
                foreach ($users_array as $user){
                    if ($user_data->get_auth()) // Check authentication status and display user blocks accordingly
                        if ($subs)
                            print_user_block($user['name'], $user['surname'], $user['file'], $user['id'], 1); // Print user block with subscription status
                        else
                            print_user_block($user['name'], $user['surname'], $user['file'], $user['id'], in_array($user['id'], $user_data->subscriptions)); // Print user block based on subscription existence
                    else // Print user block without subscription status (unauthenticated user)
                        print_user_block($user['name'], $user['surname'], $user['file'], $user['id'], false);
                }
            }?>
		</div>
        <?php if (count($users_array) == 0){ // Display message when no user is found ?>
            <p class="users-list__title-none">Пользователь не найден</p>
        <?php } ?>

    <?php include "../templates/footer.html" ?>
    <script>
        // Exercises search
		const search_input = document.querySelector('.users-search-nav__search-input');
		search_input.addEventListener('input', function(){
			SearchItems(search_input.value);
		});

		// names of exercises
		let ExerciseNames = document.querySelectorAll('.user-card__name');

        // search logic
		function SearchItems(val){
			val = val.trim().replaceAll(' ', '').toUpperCase(); // get value of search's input
			if(val != ''){ // if value not none
				ExerciseNames.forEach(function(elem){
					if(elem.innerText.trim().replaceAll(' ', '').toUpperCase().search(val) == -1){
						let cur_exercise = elem.parentNode;
						cur_exercise.classList.add('hide');
					}
					else{ // if name matches print block
						let cur_exercise = elem.parentNode;
						cur_exercise.classList.remove('hide');
					}
				});
			}
			else{ // if value none print all cards
				ExerciseNames.forEach(function(elem){
					let cur_exercise = elem.parentNode;
					cur_exercise.classList.remove('hide');
				});
			}
		}
    </script>
</body>
</html>