<?php
include "../templates/func.php";
include "../templates/settings.php";

if (isset($_GET["subs"]) && $_GET["subs"] == 1)
    $subs = 1;
else
    $subs = 0;

$user_data->set_subscriptions($conn);

$users_array = array();
if (isset($_POST['search']) && trim($_POST['search']) != ""){
    $searches = explode(' ', $_POST['search']);
    foreach ($searches as $search){
        $sql = "SELECT users.name, users.surname, users.id, avatars.file FROM users INNER JOIN avatars ON users.avatar=avatars.id WHERE users.id!=".$user_data->get_id()." AND (users.login LIKE '%$search%' OR users.name LIKE '%$search%' OR users.surname LIKE '%$search%')";
        $result = $conn->query($sql);
        foreach ($result as $item){
            if (!in_array($item, $users_array) && $subs == 0)
                array_push($users_array, $item);
            else if (!in_array($item, $users_array) && $subs == 1 && in_array($item["id"], $user_data->subscriptions))
                array_push($users_array, $item);
        }
    }
}else {
    $sql = "SELECT users.name, users.surname, users.id, avatars.file FROM users INNER JOIN avatars ON users.avatar=avatars.id WHERE users.id!=".$user_data->get_id();
    $result = $conn->query($sql);
    foreach ($result as $item){
        if (!$subs)
            array_push($users_array, $item);
        else if ($subs == 1 && in_array($item["id"], $user_data->subscriptions))
            array_push($users_array, $item);
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
                    if ($user_data->get_auth())
                        if ($subs)
                            print_user_block($user['name'], $user['surname'], $user['file'], $user['id'], 1);
                        else
                            print_user_block($user['name'], $user['surname'], $user['file'], $user['id'], in_array($user['id'], $user_data->subscriptions));
                    else
                        print_user_block($user['name'], $user['surname'], $user['file'], $user['id'], false);
                }
            }?>
		</div>
        <?php if (count($users_array) == 0){ ?>
            <p class="users-list__title-none">Пользователь не найден</p>
        <?php } ?>

    <?php include "../templates/footer.html" ?>
    <script>
        // Exercise search
		const search_input = document.querySelector('.users-search-nav__search-input');
		search_input.addEventListener('input', function(){
			SearchItems(search_input.value);
		});

		// ===SEARCH===
		let ExerciseNames = document.querySelectorAll('.user-card__name');

		function SearchItems(val){
			val = val.trim().replaceAll(' ', '').toUpperCase();
			if(val != ''){
				ExerciseNames.forEach(function(elem){
					if(elem.innerText.trim().replaceAll(' ', '').toUpperCase().search(val) == -1){
						let cur_exercise = elem.parentNode;
						cur_exercise.classList.add('hide');
					}
					else{
						let cur_exercise = elem.parentNode;
						cur_exercise.classList.remove('hide');
					}
				});
			}
			//
			else{
				ExerciseNames.forEach(function(elem){
					let cur_exercise = elem.parentNode;
					cur_exercise.classList.remove('hide');
				});
			}
		}
    </script>
</body>
</html>