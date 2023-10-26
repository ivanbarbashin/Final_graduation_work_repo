<?php
include "../templates/func.php";
include "../templates/settings.php";

$user_data->check_the_login();
$flag = isset($_SESSION["workout"]);

if (isset($_POST["week_days"])){
    $name = $_POST["name"];
    $loops = $_POST["loops"];
    $exercises = [];
    $reps = [];
    $approaches = [];
    foreach ($_SESSION["workout"] as $exercise){
        array_push($exercises, $exercise->get_id());
        array_push($reps, $exercise->reps);
        array_push($approaches, $exercise->approaches);
    }
    $user_id = $user_data->get_id();
    $sql = "INSERT INTO workouts (creator, name, exercises, reps, approaches, loops) VALUES ($user_id, '$name', '".json_encode($exercises)."', '".json_encode($reps)."', '".json_encode($approaches)."', $loops)";
    if ($conn->query($sql)){
        $lid = mysqli_insert_id($conn);

        foreach ($_POST["week_days"] as $week_day) {
            $_SESSION["program"][(int)$week_day] = $lid;
        }
        $_SESSION["workout"] = array();
        header("Location: c_program.php");

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
                        foreach ($_SESSION["workout"] as $exercise){
                            $exercise->print_it($conn);
                        }
                    }
                    ?>
				</section>
				<!-- Info about day workout -->
				<section class="workouts-card__info">
					<!-- Muscle groups -->
                    <?php print_workout_info_function($_SESSION["workout"]); ?>
					<!-- Decorative line -->
					<div class="workouts-card__info-line"></div>
					<!-- Exercise info -->
                    <p class="workouts-card__item">Упражнений: <span><?php if ($flag) echo count($_SESSION["workout"]); else echo 0; ?></span></p>
					<!-- Decorative line -->
					<div class="workouts-card__info-line"></div>
					<!-- Buttons edit and start -->
					<div class="day-workouts__card-buttons">
						<a class="button-text day-workouts__card-button day-workouts__card-button--add" href="c_exercises.php"><p>Добавить</p> <img src="../img/add.svg" alt=""></a>
						<button class="button-text day-workouts__card-button"><p>Очистить</p> <img src="../img/delete.svg" alt=""></button>
					</div>
				</section>
			</section>
			<form method="post" class="c-workout__info">
				<section class="c-workout__info-header">
					<h1 class="c-workout__info-title">Название:</h1>
                    <input class="c-workout__info-name" type="text" placeholder="Название тренировки" value="" name="name">
				</section>
				<button class="button-text c-workout__days-add" type="submit"><p>Добавить тренировку</p> <img src="../img/add.svg" alt=""></button>
                <a class="button-text c-workout__back-button" href="c_program.php">Назад</a>
			</form>
		</div>

	</main>

    <?php include "../templates/footer.html" ?>

</body>
</html>