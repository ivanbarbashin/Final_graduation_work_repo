<?php
include "../templates/func.php";
include "../templates/settings.php";

$users_array = array();
if (isset($_POST['search']) && trim($_POST['search']) != ""){
    $searches = explode(' ', $_POST['search']);
    foreach ($searches as $search){
        $sql = "SELECT users.name, users.surname, users.id, avatars.file FROM users INNER JOIN avatars ON users.avatar=avatars.id WHERE users.login LIKE '%$search%' OR users.name LIKE '%$search%' OR users.surname LIKE '%$search%'";
        $result = $conn->query($sql);
        foreach ($result as $item){
            if (!in_array($item, $users_array))
                array_push($users_array, $item);
        }
    }
}

$user_data->set_subscriptions($conn);
?>
<!DOCTYPE html>
<html lang="en">
<?php inc_head(); ?>
<body>
    <?php include "../templates/header.php" ?>

	<nav class="users-search-nav">
		<div class="container">
			<!-- Buttons to (sub / unsub) users -->
            <?php if ($user_data->get_status() == "coach" || $user_data->get_status() == "doctor"){ ?>
            <a class="button-text users-search-nav__item" href="requests.php">Новые заявки (<?php echo count($user_data->get_requests()); ?>)<img src="../img/application.svg" alt=""></a>
            <?php } ?>
			<a class="button-text users-search-nav__item" href="c_exercises.php?my=1">Подписчики <img src="../img/arrow_white.svg" alt=""></a>
			<!-- Exercise search -->
			<form method="post" class="users-search-nav__search">
				<input class="users-search-nav__search-input" type="text" placeholder="Искать" name="search">
				<button class="users-search-nav__search-button"><img src="../img/search_black.svg" alt=""></button>
			</form>
		</div>
	</nav>

	<main class="users-list">
		<div class="container">
            <?php
            if (count($users_array) != 0){
                foreach ($users_array as $user){
                    if ($user_data->get_auth())
                        print_user_block($user['name'], $user['surname'], $user['file'], $user['id'], array_search($user['id'], $user_data->subscriptions));
                    else
                        print_user_block($user['name'], $user['surname'], $user['file'], $user['id'], false);
                }
            }else{ ?>
                <p>Пользователь не найден</p>
            <?php } ?>
		</div>
	</main>

    <?php include "../templates/footer.html" ?>
</body>
</html>