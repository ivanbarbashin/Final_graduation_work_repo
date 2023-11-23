<?php
include "../templates/func.php"; // Include functions file
include "../templates/settings.php"; // Include settings file

if ($user_data->get_status() != "coach" && $user_data->get_status() != "doctor") // Checking user status; if not coach or doctor, redirect to index
    header("Location: ../index.php");

$sportsmen = $user_data->get_sportsmen_advanced($conn); // Fetching advanced sportsmen data for the logged-in user

?>
<!DOCTYPE html>
<html lang="en">
<?php inc_head(); // print head.php ?>
<body>
    <?php include "../templates/header.php"; // print header template ?>

	<nav class="users-search-nav">
		<div class="container">
			<!-- Buttons to (sub / unsub) users -->
            <a class="button-text users-search-nav__item" href="requests.php">Новые заявки (<?php echo count($user_data->get_requests()); // print number of new applications ?>)<img src="../img/application.svg" alt=""></a>
			<!-- Exercise search -->
            
			<form method="post" class="users-search-nav__search">
				<input class="users-search-nav__search-input" type="text" placeholder="Искать" name="search">
				<p class="exercises-nav__search-img"><img src="../img/search_black.svg" alt=""></p>
			</form>
			
		</div>
	</nav>

	<main class="users-list">
		<div class="container">
            <?php foreach ($sportsmen as $sportsman) print_sportsman_block($conn, $sportsman); // Loop through each sportsman and print their block using the print_sportsman_block function ?>
		</div>
        <?php if (count($sportsmen) == 0){ // If no sportsmen found, display a message indicating that no users were found ?>
            <p class="users-list__title-none">Пользователи не найдены</p>
        <?php } ?>

    <?php include "../templates/footer.html"; // print footer template ?>
    <script>
        // Exercise search
		const search_input = document.querySelector('.users-search-nav__search-input');
		search_input.addEventListener('input', function(){
			SearchItems(search_input.value);
		});

		// exercises names
		let ExerciseNames = document.querySelectorAll('.user-card__name');

		// search logic
		function SearchItems(val){
			val = val.trim().replaceAll(' ', '').toUpperCase(); // get value of search's input
			if(val != ''){ // if value not none
				ExerciseNames.forEach(function(elem){
					if(elem.innerText.trim().replaceAll(' ', '').toUpperCase().search(val) == -1){ // if name doesn't match hide block
						let cur_exercise = elem.parentNode;
						cur_exercise.classList.add('hide');
					}
					else{ // if name matches print block
						let cur_exercise = elem.parentNode;
						cur_exercise.classList.remove('hide');
					}
				});
			}
			//
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