<?php
include "../templates/func.php";
include "../templates/settings.php";

if (empty($_SESSION['c_workout']))
    $_SESSION['c_workout'] = array();


if ($user_data->get_status() != "coach" || empty($_GET["for"]) || !is_numeric($_GET["for"]))
    header("Location: coach.php");

$user_id = $_GET["for"];

$flag = isset($_SESSION["c_workout"]);

if (isset($_POST["name"]) && isset($_POST["date"])){
    $name = $_POST["name"];
    $loops = $_POST["loops"];
    $exercises = [];
    $date = strtotime($_POST["date"]);
    foreach ($_SESSION["c_workout"] as $exercise){
        array_push($exercises, $exercise->get_id());
    }

    $sql = "INSERT INTO control_workouts (creator, user, name, exercises, date) VALUES (".$user_data->get_id().", $user_id, '$name', '".json_encode($exercises)."', $date)";
    echo $sql;
    if ($conn->query($sql)){
        $_SESSION["c_workout"] = array();
        header("Location: control_workouts.php?user=".$user_id);
    }else{
        echo $conn->error;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<?php inc_head(); ?>
<body>
    <?php include "../templates/header.php" ?>

	<main class="c-workout">
		<div class="container">
			<!-- Content of workout -->
			<section class="workouts-card workouts-card--c">
				<!-- Exercises array -->
				<section class="workouts-card__exercises-cover">
					<!-- Exercise items -->
                    <?php
                    if ($flag) {
                        foreach ($_SESSION["c_workout"] as $exercise){
                            $exercise->print_control_exercise($conn, false);
                        }
                    }
                    ?>
				</section>
				<!-- Info about day workout -->
				<section class="workouts-card__info">
					<!-- Muscle groups -->
                    <?php print_workout_info_function($_SESSION["c_workout"]); ?>
					<!-- Decorative line -->
					<div class="workouts-card__info-line"></div>
					<!-- Exercise info -->
                    <p class="workouts-card__item">Упражнений: <span><?php if ($flag) echo count($_SESSION["c_workout"]); else echo 0; ?></span></p>
					<!-- Decorative line -->
					<div class="workouts-card__info-line"></div>
					<!-- Buttons edit and start -->
					<div class="day-workouts__card-buttons day-workouts__card-buttons--c">
						<a class="button-text day-workouts__card-button day-workouts__card-button--add" href="c_control_exercises.php?for=<?php echo $user_id; ?>"><p>Добавить упражнение</p> <img src="../img/add.svg" alt=""></a>
						<a href="c_clear.php?for=<?php echo $user_id; ?>" class="button-text day-workouts__card-button"><p>Очистить</p> <img src="../img/delete.svg" alt=""></a>
					</div>
				</section>
			</section>
			<form method="post" class="c-workout__info">
				<section class="c-workout__info-header c-workout__info-header--control">
					<div class="c-workout__info-header-item">
						<h1 class="c-workout__info-title">Название:</h1>
						<input class="c-workout__info-name" type="text" placeholder="Название тренировки" name="name">
					</div>
					<div class="c-workout__info-header-item">
						<h1 class="c-workout__info-title">Дата:</h1>
						<input class="c-workout__info-name" type="date" placeholder="Название тренировки" name="date">
					</div>
				</section>
				<button class="button-text c-workout__days-add" type="submit"><p>Добавить тренировку</p> <img src="../img/add.svg" alt=""></button>
                <a class="button-text c-workout__back-button" href="control_workouts.php?user=<?php echo $user_id; ?>">Назад</a>
			</form>
		</div>

	</main>

    <?php include "../templates/footer.html" ?>
	<script>
		// Button to see exercise info
        let	InfoExerciseButton = document.querySelectorAll('.exercise-item__info-btn');
        let closeInfoExerciseButton = document.querySelectorAll('.exercise-item__info-close');
        let infoBlock = document.querySelectorAll('.exercise-item__info-content');

        for(let i = 0; i < InfoExerciseButton.length; i++){
            InfoExerciseButton[i].addEventListener('click', function(){
                infoBlock[i].style.cssText = `top: -1%;`;
            });
        }
        for(let i = 0; i < closeInfoExerciseButton.length; i++){
            closeInfoExerciseButton[i].addEventListener('click', function(){
                infoBlock[i].style.cssText = `top: -101%;`;
            });
        }


        // Button submit
		let addToPragramButton = document.querySelector('.c-workout__days-add');
		let workoutNameInput = document.querySelector('.c-workout__info-name');
		addToPragramButton.addEventListener('click', function(){
			if(workoutNameInput.value == ''){
				workoutNameInput.value = "Без названия";
			}
		});
	</script>
</body>
</html>