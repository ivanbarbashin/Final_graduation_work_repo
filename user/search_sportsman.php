<?php
include "../templates/func.php";
include "../templates/settings.php";

if ($user_data->get_status() != "coach" && $user_data->get_status() != "doctor")
    header("Location: ../index.php");

$sportsmen = $user_data->get_sportsmen_advanced($conn);

?>
<!DOCTYPE html>
<html lang="en">
<?php inc_head(); ?>
<body>
    <?php include "../templates/header.php" ?>

	<nav class="users-search-nav">
		<div class="container">
			<!-- Buttons to (sub / unsub) users -->
            <a class="button-text users-search-nav__item" href="requests.php">Новые заявки (<?php echo count($user_data->get_requests()); ?>)<img src="../img/application.svg" alt=""></a>
			<!-- Exercise search -->
            
			<form method="post" class="users-search-nav__search">
				<input class="users-search-nav__search-input" type="text" placeholder="Искать" name="search">
				<p class="exercises-nav__search-img"><img src="../img/search_black.svg" alt=""></p>
			</form>
			
		</div>
	</nav>

	<main class="users-list">
		<div class="container">
            <?php foreach ($sportsmen as $sportsman) print_sportsman_block($conn, $sportsman); ?>
		</div>
        <?php if (count($sportsmen) == 0){ ?>
            <p class="users-list__title-none">Пользователи не найдены</p>
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