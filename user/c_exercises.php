<?php
include "../templates/func.php";
include "../templates/settings.php";
$user_data->check_the_login();

if (empty($_SESSION['workout'])){
    $_SESSION['workout'] = array();
}

if (isset($_POST['featured'])){
    $user_data->change_featured($conn, $_POST['featured']);
}

if (isset($_POST['reps']) && isset($_POST['approaches'])){
    $user_exercise = new User_Exercise($conn, $_POST["id"], $_POST['reps'], $_POST['approaches']);
    array_push($_SESSION['workout'], $user_exercise);
}

if (isset($_GET['my']) && is_numeric($_GET['my'])){
    $my = $_GET['my'];
}else{
    $my = 1;
}
?>
<!DOCTYPE html>
<html lang="en">
<?php inc_head(); ?>
<body>
	<?php include "../templates/header.php" ?>

	<!-- Exercise navigation -->
	<nav class="exercises-nav">
		<div class="container">
            <a class="button-text exercises-nav__item exercises-nav__item--text" href="c_workout.php">Назад</a>
			<!-- Buttons to (my / all) exercises -->
            <?php if ($my) { ?>
                <a class="button-text exercises-nav__item" href="c_exercises.php?my=0">Все <img src="../img/arrow_white.svg" alt=""></a>
            <?php } else { ?>
                <a class="button-text exercises-nav__item" href="c_exercises.php?my=1">Мои <img src="../img/arrow_white.svg" alt=""></a>
            <?php } ?>
			<!-- Main search -->
			<select name="exercises-nav__select" id="">
				<option value="value1" selected>По умолчанию</option>
				<option value="value2">Избранные</option>
				<option value="value3">Рейтинг(возрастание)</option>
				<option value="value4">Рейтинг(убывание)</option>
				<option value="value5">Сложность(возрастание)</option>
				<option value="value6">Сложность(убывание)</option>
			</select>
			<!-- Exercise search -->
			<div class="exercises-nav__search">
				<input class="exercises-nav__search-input" type="text" placeholder="Искать">
				<button class="exercises-nav__search-button"><img src="../img/search_black.svg" alt=""></button>
			</div>
		</div>
	</nav>

	<main class="exercises-block">
		<!-- Exercises and filter block -->
		<div class="container">
			<!-- Filter block -->
			<form class="exercises-filter">
				<!-- Muscle groups filter -->
				<section class="exercises-filter__muscle-groups">
					<button  class="exercises-filter__button" type="button">Группы мышц <img src="../img/search_arrow.svg" alt=""></button>
					<div class="exercises-filter__content">
						<div class="exercises-filter__item">
							<input class="exercises-filter__item-input" type="checkbox" name="muscle_groups_search" id="muscle_groups_search1">
							<label class="exercises-filter__item-label" for="muscle_groups_search1">Спина</label>
						</div>
						<div class="exercises-filter__item">
							<input class="exercises-filter__item-input" type="checkbox" name="muscle_groups_search" id="muscle_groups_search2">
							<label class="exercises-filter__item-label" for="muscle_groups_search2">Ноги</label>
						</div>
						<div class="exercises-filter__item">
							<input class="exercises-filter__item-input" type="checkbox" name="muscle_groups_search" id="muscle_groups_search3">
							<label class="exercises-filter__item-label" for="muscle_groups_search3">Руки</label>
						</div>
						<div class="exercises-filter__item">
							<input class="exercises-filter__item-input" type="checkbox" name="muscle_groups_search" id="muscle_groups_search4">
							<label class="exercises-filter__item-label" for="muscle_groups_search4">Грудь</label>
						</div>
						<div class="exercises-filter__item">
							<input class="exercises-filter__item-input" type="checkbox" name="muscle_groups_search" id="muscle_groups_search5">
							<label class="exercises-filter__item-label" for="muscle_groups_search5">Пресс</label>
						</div>
						<div class="exercises-filter__item">
							<input class="exercises-filter__item-input" type="checkbox" name="muscle_groups_search" id="muscle_groups_search6">
							<label class="exercises-filter__item-label" for="muscle_groups_search6">Кардио</label>
						</div>
					</div>
				</section>
				<!-- Difficult filter -->
				<section class="exercises-filter__difficult">
					<button class="exercises-filter__button" type="button">Сложность <img src="../img/search_arrow.svg" alt=""></button>
					<div class="exercises-filter__content">
						<div class="exercises-filter__item">
							<input class="exercises-filter__item-input" type="checkbox" name="difficult_search" id="difficult_search1">
							<label class="exercises-filter__item-label" for="difficult_search1">5</label>
						</div>
						<div class="exercises-filter__item">
							<input class="exercises-filter__item-input" type="checkbox" name="difficult_search" id="difficult_search2">
							<label class="exercises-filter__item-label" for="difficult_search2">4</label>
						</div>
						<div class="exercises-filter__item">
							<input class="exercises-filter__item-input" type="checkbox" name="difficult_search" id="difficult_search3">
							<label class="exercises-filter__item-label" for="difficult_search3">3</label>
						</div>
						<div class="exercises-filter__item">
							<input class="exercises-filter__item-input" type="checkbox" name="difficult_search" id="difficult_search4">
							<label class="exercises-filter__item-label" for="difficult_search4">2</label>
						</div>
						<div class="exercises-filter__item">
							<input class="exercises-filter__item-input" type="checkbox" name="difficult_search" id="difficult_search5">
							<label class="exercises-filter__item-label" for="difficult_search5">1</label>
						</div>
					</div>
				</section>
				<!-- Rating filter -->
				<section class="exercises-filter__rating">
					<button class="exercises-filter__button" type="button">Рейтинг <img src="../img/search_arrow.svg" alt=""></button>
					<div class="exercises-filter__content">
						<div class="exercises-filter__item">
							<input class="exercises-filter__item-input" type="radio" name="rating_search" id="rating_search1">
							<label class="exercises-filter__item-label" for="rating_search1">5</label>
						</div>
						<div class="exercises-filter__item">
							<input class="exercises-filter__item-input" type="radio" name="rating_search" id="rating_search2">
							<label class="exercises-filter__item-label" for="rating_search2">от 4</label>
						</div>
						<div class="exercises-filter__item">
							<input class="exercises-filter__item-input" type="radio" name="rating_search" id="rating_search3">
							<label class="exercises-filter__item-label" for="rating_search3">от 3</label>
						</div>
						<div class="exercises-filter__item">
							<input class="exercises-filter__item-input" type="radio" name="rating_search" id="rating_search4">
							<label class="exercises-filter__item-label" for="rating_search4">от 2</label>
						</div>
						<div class="exercises-filter__item">
							<input class="exercises-filter__item-input" type="radio" name="rating_search" id="rating_search5">
							<label class="exercises-filter__item-label" for="rating_search6">от 1</label>
						</div>
						<div class="exercises-filter__item">
							<input class="exercises-filter__item-input" type="radio" name="rating_search" id="rating_search6">
							<label class="exercises-filter__item-label" for="rating_search6">любой</label>
						</div>
					</div>
				</section>
				<!-- Buttons search and clear -->
				<button class="exercises-filter__search-button" type="submit" class="clear">Искать</button>
				<button class="exercises-filter__search-button" type="button" class="clear">Очистить</button>
			</form>
			<!-- Exercises array -->
			<?php
			if ($my){
				if (count($user_data->my_exercises) > 0){
					echo "<form method='post' class='exercise-block'>";
					foreach ($user_data->my_exercises as $exercise_id){
						$exercise = new Exercise($conn, $exercise_id);
						$is_featured = $exercise->is_featured($user_data);
						$exercise->print_it($conn, $is_featured, 1, 1);
					}
					echo "</form>";
				}else{
					echo "<h1 class='exercises__none'>У Вас нет своих упражнений. Вы можете добавить их на вкладке 'Упражнения'.</h1>";
				}
			}else{
				$select_sql = "SELECT id FROM exercises";
				if ($select_result = $conn->query($select_sql)){
					echo "<form method='post' class='exercise-block'>";
					foreach ($select_result as $item){
						$exercise = new Exercise($conn, $item['id']);
						$is_featured = $exercise->is_featured($user_data);
						$is_mine = $exercise->is_mine($user_data);
						$exercise->print_it($conn, $is_featured, $is_mine, 1);
					}
					echo "</form>";
				}else{
					echo $conn->error;
				}
			}
			?>
        </div>

		<section class="popup-exercise">
			<section class="popup-exercise__content">
				<button class="popup-exercise__close-button"><img src="../img/close.svg" alt=""></button>
				<section class="exercise-item">
					
				</section>
				<form method="post" class="popup-exercise__info">
					<div class="popup-exercise__info-item">
						<label class="popup-exercise__info-label" for="c_exercise_circles">Количество подходов: </label>
						<input class="popup-exercise__info-input" type="number" id="c_exercise_circles" name="approaches">
					</div>
					<div>
						<label class="popup-exercise__info-label" for="c_exercise_reps">Количество повторений: </label>
						<input class="popup-exercise__info-input" type="number" id="c_exercise_reps" name="reps">
					</div>
                    <input class="exercise_id" name="id" type="hidden" value="">
					<button class="button-text popup-exercise__add-button"><p>Добавить в тренировку</p> <img src="../img/add.svg" alt=""></button>
				</form>
			</section>
		</section>
	</main>

	<?php include "../templates/footer.html" ?>

	<script>
        // Button to see exercise info
        let infoExerciseButton = document.querySelectorAll('.exercise-item__info-btn');
        let closeInfoExerciseButton = document.querySelectorAll('.exercise-item__info-close');
        let infoBlock = document.querySelectorAll('.exercise-item__info-content');

        for(let i = 0; i < infoExerciseButton.length; i++){
            infoExerciseButton[i].addEventListener('click', function(){
                infoBlock[i].style.cssText = `top: -1%;`;
            });
        }
        for(let i = 0; i < closeInfoExerciseButton.length; i++){
            closeInfoExerciseButton[i].addEventListener('click', function(){
                infoBlock[i].style.cssText = `top: -101%;`;
            });
        }


		// Filter buttons
		let FilterButtonsArr = document.querySelectorAll('.exercises-filter__button');
		let FilterBlocksArr = document.querySelectorAll('.exercises-filter__content');
		let FilterButtonsArrowArr = document.querySelectorAll('.exercises-filter__button img');


		for(let i = 0; i < FilterButtonsArr.length; i++){
			FilterButtonsArr[i].addEventListener('click', function(){
				if(FilterBlocksArr[i].clientHeight == 0){
					FilterBlocksArr[i].style.cssText = `height: auto;`;
                    FilterButtonsArrowArr[i].style.cssText = `transform: rotate(180deg);`;
				}
				else{
					FilterBlocksArr[i].style.cssText = `height: 0px`;
                    FilterButtonsArrowArr[i].style.cssText = `transform: rotate(0deg);`;
				}
			});
		}


		//Difficult
		let difficultCountArr = document.querySelectorAll('.exercise-item__difficult-number');
		let difficultBlockArr = document.querySelectorAll('.exercise-item__difficult');

		for(let i = 0; i < difficultCountArr.length; i++){
			difficultBlockArr[i].innerHTML = '';
            for(let j = 0; j < 5; j++){
				let newElem = document.createElement('div');
				newElem.classList.add('exercise-item__difficult-item');
				if(j > Number(difficultCountArr[i].innerHTML) - 1){
					newElem.classList.add('exercise-item__difficult-item--disabled');
				}
				difficultBlockArr[i].appendChild(newElem);
			}
        }

		FilterButtonsArr[0].click();

		// Popup exercises
		let exercisesButtons = document.querySelectorAll('.exercise-item__add');
		let popupExerciseItem = document.querySelector('.popup-exercise .exercise-item');
		let popupExerciseWindow = document.querySelector('.popup-exercise');
		let inputExerciseId = document.querySelector('.popup-exercise .exercise_id');

		let popupExerciseItemInfo = document.querySelector('.popup-exercise .exercise-item__info-btn');
		let popupExerciseItemClose = document.querySelector('.popup-exercise__close-button');
		let popupExerciseItemContent = document.querySelector('.popup-exercise .exercise-item__info-content');

		for(let i = 0; i < exercisesButtons.length; i++){
			exercisesButtons[i].addEventListener('click', function(){
				let item = exercisesButtons[i].parentElement.parentElement;
				popupExerciseItem.innerHTML = '';
				popupExerciseItem.innerHTML = item.innerHTML;
				inputExerciseId.value = exercisesButtons[i].value;
				popupExerciseItem.removeChild(popupExerciseItem.lastElementChild);

				popupExerciseWindow.classList.add("open");

				popupExerciseItemInfo = document.querySelector('.popup-exercise .exercise-item__info-btn');
				popupExerciseItemClose = document.querySelector('.popup-exercise .exercise-item__info-close');
				popupExerciseItemContent = document.querySelector('.popup-exercise .exercise-item__info-content');
			
				popupExerciseItemInfo.addEventListener('click', function(){
					popupExerciseItemContent.style.cssText = `top: -1%;`;
				});
				popupExerciseItemClose.addEventListener('click', function(){
					popupExerciseItemContent.style.cssText = `top: -101%;`;
				});
			});
		}

		
		

		const closeBtn = document.querySelector('.popup-exercise__close-button');
		closeBtn.addEventListener('click', function(){
			popupExerciseWindow.classList.remove("open");
		});

		window.addEventListener('keydown', (e) => {
		if(e.key == "Escape"){
			popupExerciseWindow.classList.remove("open");
		}
		});

		document.querySelector('.popup-exercise__content').addEventListener('click', event => {
			event.isClickWithInModal = true;
		});

	</script>
</body>
</html>