<?php
include "../templates/func.php";
include "../templates/settings.php";
$user_data->check_the_login();

if (empty($_SESSION["program"])){
    $_SESSION["program"] = array(0, 0, 0, 0, 0, 0, 0);
}

if (isset($_POST["weeks"]) && $_POST["weeks"] > 0){
    if (empty($_POST["date_start"])){
        $date_start = mktime(0, 0, 0, date("m"), date("d"), date("Y"));
    }else{
        $date_ex = explode('-', $_POST["date_start"]);
        $date_start = mktime(0, 0, 0, $date_ex[1], $date_ex[2], $date_ex[0]);
    }

    // insert_program and user_to_program and news

    $sql = "INSERT INTO programs (name, program, creator) VALUES ('".$_POST["name"]."', '".json_encode($_SESSION["program"])."', ".$user_data->get_id().")";
    if ($conn->query($sql)){
        $program_id = mysqli_insert_id($conn);
        if ($user_data->get_status() == "user") {
            $sql2 = "INSERT INTO program_to_user (user, program, date_start, weeks) VALUES (".$user_data->get_id().", $program_id, $date_start, ".$_POST['weeks'].")";
            $sql3 = "INSERT INTO news (message, user, date, personal) VALUES ('Пользователь начал программу.', ".$user_data->get_id().", ".time().", 0)";
            if ($conn->query($sql2) && $conn->query($sql3)){
                $_SESSION["workout"] = array();
                $_SESSION["program"] = array();
                header("Location: my_program.php");
            }else{
                echo $conn->error;
            }
        } else if ($user_data->get_status() == "coach") {
            $users = $_POST["users"];
            if (count($users) > 0){
                foreach ($users as $user){
                    $sql2 = "INSERT INTO program_to_user (user, program, date_start, weeks) VALUES (".$user.", $program_id, $date_start, ".$_POST['weeks'].")";
                    $sql3 = "INSERT INTO news (message, user, date, personal) VALUES ('Пользователь начал программу.', ".$user.", ".time().", 0)";
                    if (!$conn->query($sql2) || !$conn->query($sql3)){
                        echo $conn->error;
                    }
                }
                $_SESSION["workout"] = array();
                $_SESSION["program"] = array();
                header("Location: profile.php");
            }else{
                echo "Ошибка: вы не выбрали пользователя";
            }
        }
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

	<main class="c-control-workout">
		<div class="container">
			<section class="c-program__create">
				<section class="c-program__workouts">
					<section class="c-program__workouts-list">
						<div class="c-program__workouts-item">
							<p class="c-program__workouts-name">1. День рук</p>
							<button class="button-img c-program__workouts-more"><img src="../img/more_white.svg" alt=""></button>
							<button class="button-img c-program__workouts-delete"><img src="../img/delete.svg" alt=""></button>
						</div>
						<div class="c-program__workouts-item">
						<p class="c-program__workouts-name">2. ффффффя</p>
							<button class="button-img c-program__workouts-more"><img src="../img/more_white.svg" alt=""></button>
							<button class="button-img c-program__workouts-delete"><img src="../img/delete.svg" alt=""></button>
						</div>
						<div class="c-program__workouts-item">
							<p class="c-program__workouts-name">3. Без названия</p>
							<button class="button-img c-program__workouts-more"><img src="../img/more_white.svg" alt=""></button>
							<button class="button-img c-program__workouts-delete"><img src="../img/delete.svg" alt=""></button>
						</div>
					</section>
					<div class="c-program__workouts-buttons">
						<a class="button-text c-program__workouts-button c-program__workouts-button--create" href="c_control_workout.php"><p>Создать тренировку</p> <img src="../img/add.svg" alt=""></a>
						<a class="button-text c-program__workouts-button" href="clear.php"><p>Очистить список</p> <img src="../img/delete.svg" alt=""></a>
					</div>
				</section>
				<form class="c-program__duration" method="post">
					<h4 class="c-program__duration-title">Укажите дату тренировки<br></h4>
					<div class="c-program__duration-date">
						<p class="c-program__duration-date-text c-program__duration-date-text--control">Дата:</p>
                        <input class="c-program__duration-date-start" type="date" name="date_start">
					</div>
					<a href="" class="button-text c-program__duration-button">Далее <img src="../img/arrow_white.svg" alt=""></a>
				</form>
			</section>
		</div>
	</main>

	
	<?php include "../templates/footer.html" ?>
</body>
</html>