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
                    $sql3 = "INSERT INTO news (message, user, date, personal) VALUES ('Пользователь начал программу. (Тренер).', ".$user.", ".time().", 0)";
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

	<main class="c-program">
		<div class="container">
			<section class="day-workouts" navigation="true">
                <section class="day-workouts__block--adaptive">
                    <?php
                    $workout_array = array();
                        for($i = 0; $i < 4; $i++){
                            $workout = new Workout($conn, $_SESSION["program"][$i], $i);
                            if ($_SESSION["program"][$i] != 0){
                                $flag = true;
                                foreach ($workout_array as $item){
                                    if ($item->get_id() == $workout->get_id()){
                                        $flag = false;
                                        break;
                                    }
                                }
                                if ($flag)
                                    array_push($workout_array, $workout);
                            }
                            $workout->set_muscles();
                            $workout->print_workout_info_block($i, 0, $user_data->get_id());
                        } ?>
                </section>
                <section class="day-workouts__block--adaptive">
                    <?php
                    $workout_array = array();
                        for($i = 4; $i < 7; $i++){
                            $workout = new Workout($conn, $_SESSION["program"][$i], $i);
                            if ($_SESSION["program"][$i] != 0){
                                $flag = true;
                                foreach ($workout_array as $item){
                                    if ($item->get_id() == $workout->get_id()){
                                        $flag = false;
                                        break;
                                    }
                                }
                                if ($flag)
                                    array_push($workout_array, $workout);
                            }
                            $workout->set_muscles();
                            $workout->print_workout_info_block($i, 0, $user_data->get_id());
                        } ?>
                </section>
                <section class="day-workouts__block">
                    <?php
                    $workout_array = array();
                        for($i = 0; $i < 7; $i++){
                            $workout = new Workout($conn, $_SESSION["program"][$i], $i);
                            if ($_SESSION["program"][$i] != 0){
                                $flag = true;
                                foreach ($workout_array as $item){
                                    if ($item->get_id() == $workout->get_id()){
                                        $flag = false;
                                        break;
                                    }
                                }
                                if ($flag)
                                    array_push($workout_array, $workout);
                            }
                            $workout->set_muscles();
                            $workout->print_workout_info_block($i, 0, $user_data->get_id());
                        } ?>
                    </section>
			</section>
			<section class="c-program__create">
				<section class="c-program__workouts">
					<section class="c-program__workouts-list">
                        <?php
                        if (count($workout_array) == 0){?>
                            <p class="c-program__workouts-list-none">Список тренировок пуст</p>
                        <?php }
                        for ($i = 0; $i < count($workout_array); $i++){ ?>
						<div class="c-program__workouts-item">
							<p class="c-program__workouts-name"><?php echo $i + 1 . ". " .  ($workout_array[$i])->name; ?></p>
							<a href="delete_workout.php?id=<?php echo ($workout_array[$i])->get_id(); ?>" class="button-img c-program__workouts-delete"><img src="../img/delete.svg" alt=""></a>
						</div>
                        <?php } ?>
					</section>
					<div class="c-program__workouts-buttons">
						<a class="button-text c-program__workouts-button c-program__workouts-button--create" href="c_workout.php"><p>Создать тренировку</p> <img src="../img/add.svg" alt=""></a>
						<a class="button-text c-program__workouts-button" href="clear.php"><p>Очистить программу</p> <img src="../img/delete.svg" alt=""></a>
					</div>
				</section>
				<form class="c-program__duration" method="post">
					<?php if ($user_data->get_status() == "coach"){ ?>
					    <h4 class="c-program__duration-title">Выберите спортсменов<br></h4>
                    <?php }?>
					<?php if ($user_data->get_status() == "user"){ ?>
						<h4 class="c-program__duration-title">Укажите продолжительность программы<br></h4>
						<div class="c-program__duration-date">
							<input class="c-program__duration-weeks" type="number" placeholder="Количество недель" name="weeks">
							<p class="c-program__duration-date-text">начать с</p>
							<input class="c-program__duration-date-start" type="date" name="date_start">
						</div>
                    <?php } ?>
                    <?php if ($user_data->get_status() == "coach"){ ?>
					    <button type="button" class="button-text c-program__add-button"><p>Добавить спортсменов</p><img src="../img/add.svg" alt=""></button>
                    <?php } else { ?>
					<button class="button-text c-program__duration-button"><p>Начать программу</p> <img src="../img/arrow_white.svg" alt=""></button>
					<?php } ?>
				</form>
			</section>
			<section class="friends-block">
				<!-- Title and button to search friends -->
				<div class="friends-block__header">
					<h1 class="friends-block__header-title">Программы друзей</h1>
					<a class="friends-block__header-button img" href="search_users.php"><img src="../img/search.svg" alt=""></a>
				</div>
				<!-- Friends' workout swiper -->
				<swiper-container class="friends-block__swiper" navigation="true">
					<!-- slide -->
                    <?php
                    $user_data->set_subscriptions($conn);
                    print_user_list($conn, $user_data->subscriptions);
                    ?>
				</swiper-container>
			</section>
		</div>

		<!-- Popup form for add users (for coach) -->
        <section class="popup-exercise popup-exercise--add-users">
			<form method="post" class="popup-exercise__content popup-exercise--add-users__form">
                <button type="button" class="popup-exercise__close-button"><img src="../img/close.svg" alt=""></button>
                <h4 class="c-program__duration-title">Укажите продолжительность программы<br></h4>
                <div class="c-program__duration-date c-program__duration-date--popup">
                    <input class="c-program__duration-weeks" type="number" placeholder="Количество недель" name="weeks">
                    <p class="c-program__duration-date-text">начать с</p>
                    <input class="c-program__duration-date-start" type="date" name="date_start">
                </div>
				<div class="workouts-card__info-line"></div>
                <?php
                $cnt = 0;
                foreach ($user_data->get_sportsmen() as $sportsman_id){
                    $sportsman = new User($conn, $sportsman_id);
                    if (!$sportsman->has_program($conn)){
                        $cnt++; ?>
                <div class="popup-exercise--add-users__item">
					<input class="popup-exercise--add-users__input" type="checkbox" id="users-list1" name="users[]" value="<?php echo $sportsman_id; ?>"/>
					<label class="popup-exercise--add-users__label" for="users-list1"><?php echo $sportsman->name." ".$sportsman->surname; ?></label>
				</div>
                <?php }
                    }
                if ($cnt != 0){ ?>
                    <button class="button-text popup-exercise--add-users__button-add" type="submit"><p>Выложить</p><img src="../img/add.svg" alt=""></button>
                <?php } else { ?>
                    <p class="c-program__duration-title-none">У Вас нет спортсменов</p>
                <?php } ?>
			</form>
		</section>
	</main>

	
	<?php include "../templates/footer.html" ?>

	<script src="https://cdn.jsdelivr.net/npm/swiper@10/swiper-element-bundle.min.js"></script>
	<script>
        let programAddButton = document.querySelector('.c-program__duration-button');
        let dateWokoutInput = document.querySelector('.c-program__duration-date-start');

        document.querySelector('.c-program__duration-weeks').addEventListener('input', function(){
            if(this.value.length == 0){
                programAddButton.type = 'button';
            }
            else{
                programAddButton.type = 'submit';
            }

            if (this.value > 16) {
                this.value = 16;
            }
        });

        programAddButton.addEventListener('click', function(){
            if (!dateWokoutInput.value) {
				// set today's date
				const todayDate = new Date();
				let year = todayDate.getFullYear();
				let month = todayDate.getMonth() + 1;
				let day = todayDate.getDate();

				if (month < 10) {
					month = `0${month}`;
				}
				if (day < 10) {
					day = `0${day}`;
				}

				const formattedDate = `${year}-${month}-${day}`;

				// set today's date in input
				dateWokoutInput.value = formattedDate;
			}
        });


		// Workout items
        let workoutItemArr = document.querySelectorAll('.day-workouts__card-content');

        let maxWorkoutItemHeight = 0;

        for(let i = 0; i < workoutItemArr.length; i++){
            if(workoutItemArr[i].clientHeight > maxWorkoutItemHeight){
                maxWorkoutItemHeight = workoutItemArr[i].clientHeight;
            }
        }

        for(let i = 0; i < workoutItemArr.length; i++){
            workoutItemArr[i].style.cssText = `height: ${maxWorkoutItemHeight}px;`;
        }

        // Info slide items' spans width
        let infoItemsSpans = document.querySelectorAll('.workouts-card__item span');
        let maxSpanWidth = 0;

        for(let i = 0; i < infoItemsSpans.length; i++){
            maxSpanWidth = Math.max(maxSpanWidth, infoItemsSpans[i].clientWidth);
        }

        for(let i = 0; i < infoItemsSpans.length; i++){
            infoItemsSpans[i].style.cssText = `width: ${maxSpanWidth}px;`;
        }



		// Popup window to add users for coach
		let StartProgramButton = document.querySelector('.c-program__add-button');
		let UsersListPopup = document.querySelector('.popup-exercise--add-users');

		if(StartProgramButton){
			StartProgramButton.addEventListener('click', function(){
				UsersListPopup.classList.add("open");
			});
		}


        // buttons to close popup windows
        const closeBtn = document.querySelectorAll('.popup-exercise__close-button');
		for(let i = 0; i < closeBtn.length; i++){
			closeBtn[i].addEventListener('click', function(){
				UsersListPopup.classList.remove("open");
			});
		}

		window.addEventListener('keydown', (e) => {
            if(e.key == "Escape"){
				UsersListPopup.classList.remove("open");
            }
		});

		document.querySelector('.popup-exercise__content').addEventListener('click', event => {
			event.isClickWithInModal = true;
		});
	</script>

    <!-- Test for clear workout buttons -->
    <!-- <script src="../tests/test_clear_program_contructor.js"></script> -->
</body>
</html>