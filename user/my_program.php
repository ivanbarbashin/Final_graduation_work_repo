<?php
include "../templates/func.php"; // Include functions file
include "../templates/settings.php"; // Include settings file
$user_data->check_the_login(); // Check user login status
if (isset($_GET["user"]) && is_numeric($_GET["user"])) // Determine the user based on GET parameters or current session
    if ($_GET["user"] == $user_data->get_id()) // If the GET parameter matches the current user's ID, set $user to current session user
        $user = $user_data;
    else // If a different user ID is provided, set $user to that user's data
        $user = new User($conn, $_GET["user"]);
 else // If no specific user is requested, default to current session user
     $user = $user_data;

if (!$user->set_program($conn)) // Redirect if the user does not have a set program
    header("Location: c_program_info.php");

if (isset($_POST['end']) && $user->get_auth()){ // get the 'end program' action for authenticated users
    // Update the program start date to 0 to signify program completion
    $sql = "UPDATE program_to_user SET date_start=0 WHERE user=".$user->get_id()."  AND date_start + weeks * 604800 >= ".time()." LIMIT 1";
    if ($conn->query($sql)){
        header("Refresh: 0"); // Refresh the page after the program end
    }else{
        echo $conn->error; // Display an error if encountered
    }
}

// get starting a program for another user (non-authenticated)
if (isset($_POST["weeks"]) && $_POST["weeks"] > 0 && !$user->get_auth()){
    // Determine the program start date based on user input or current date
    if (empty($_POST["date_start"]))
        $date_start = mktime(0, 0, 0, date("m"), date("d"), date("Y"));
    else
        $date_start = strtotime($_POST["date_start"]);

    // Insert program information into the database for the authenticated user
    $program_id = $user->program->get_id();

    $sql2 = "INSERT INTO program_to_user (user, program, date_start, weeks) VALUES (".$user_data->get_id().", $program_id, $date_start, ".$_POST['weeks'].")";
    $sql3 = "INSERT INTO news (message, user, date, personal) VALUES ('Пользователь начал программу друга.', ".$user_data->get_id().", ".time().", 0)";

    if ($conn->query($sql2) && $conn->query($sql3)){ // Execute SQL queries to start the program and create a news entry
        header("Location: my_program.php"); // Redirect to the user's program page after successful initiation
    }else{
        echo $conn->error; // Display an error if encountered
    }
}

// Fetch user's program workouts and additional data
$user->program->set_workouts($conn);
$user->program->set_additional_data($conn, $user->get_id());
// Initialize variables for workout statistics
$cnt_workouts_done = 0;
$cnt_workouts_all = 0;
$weekday_start = date("N", $user->program->date_start) - 1;

// Initialize an array to count muscles trained during the program
$muscles = array(
    "arms" => 0,
    "legs" => 0,
    "press" => 0,
    "back" => 0,
    "chest" => 0,
    "cardio" => 0,
    "cnt" => 0
);

//Counting muscles trained in the program's workouts
foreach ($user->program->workouts as $workout){
    foreach ($workout->set_muscles() as $key=>$value){
        $muscles[$key] += $value * $user->program->weeks;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<?php inc_head(); // print head.php ?>
<body>
    <?php include "../templates/header.php"; // print header template ?>
    <main class="my-program">
        <div class="container">
            <section class="day-workouts">
                <swiper-container class="day-workouts__swiper" navigation="true">
                    <?php if ($weekday_start){ ?>
                    <!-- first slide -->
                    <swiper-slide class="day-workouts__slide">
                        <?php
                            // Display placeholder for days before the first workout of the week
                            for ($j = 0; $j < $weekday_start; $j++){
                                echo render(array("{{ day }}" => get_day($j)), "../templates/out_of_workout.html");
                            }
                            // Display workout information for the current week
                            for ($j = $weekday_start; $j < 7; $j++){
                                // get workout information and check if it's completed
                                $workout = $user->program->workouts[$j];
                                $is_done = $workout->is_done($conn, $user->get_id(), $user->program->date_start - $weekday_start * 86400 + $j * 86400);
                                $is_workout = $workout->print_workout_info_block($j, 1, $user->get_id(), $is_done);
                                // Update counters for total and completed workouts
                                $cnt_workouts_all += (int)!$workout->holiday;
                                $cnt_workouts_done += (int)$is_done;
                            }
                        ?>
                    </swiper-slide>
                    <?php }

                    $from = 0;
                    if ($weekday_start) $from = 1;
                    for ($i = $from; $i < $user->program->weeks; $i++){ // Slides for next weeks ?>
                    <swiper-slide class="day-workouts__slide">
                        <?php
                        for ($j = 0; $j < 7; $j ++){
                            $workout = $user->program->workouts[$j];
                            $is_done = $workout->is_done($conn, $user->get_id(), $user->program->date_start - $weekday_start * 86400 + $j * 86400 + $i * 604800);
                            $is_workout = $workout->print_workout_info_block($j, 1, $user->get_id(), $is_done);
                            $cnt_workouts_all += (int)!$workout->holiday;
                            $cnt_workouts_done += (int)$is_done;                        }
                        ?>
                    </swiper-slide>
                    <?php }

                    if ($weekday_start){ ?>
                        <!-- last slide -->
                        <swiper-slide class="day-workouts__slide">
                            <?php
                            for ($j = 0; $j < $weekday_start; $j++){ // Display workouts for days after the last workout of the week
                                $workout = $user->program->workouts[$j];
                                $is_done = $workout->is_done($conn, $user->get_id(), $user->program->date_start - $weekday_start * 86400 + $j * 86400 + ($user->program->weeks - 1) * 604800);
                                $is_workout = $workout->print_workout_info_block($j, 1, $user->get_id(), $is_done);
                                $cnt_workouts_all += (int)!$workout->holiday;
                                $cnt_workouts_done += (int)$is_done;                            }

                            for ($j = $weekday_start; $j < 7; $j++){ // Display placeholder for days without workouts at the end of the last week
                                echo render(array("{{ day }}" => get_day($j)), "../templates/out_of_workout.html");
                            }
                            ?>
                        </swiper-slide>
                    <?php } ?>
                </swiper-container>
            </section>
            <?php $diagram_muscles = json_encode(array($muscles['arms'], $muscles['legs'], $muscles['chest'], $muscles['back'], $muscles['press'], $muscles['cardio'])); // set muscle groups data ?>
            <section class="my-program__info">
                <section class="my-program__statistic">
                    <section class="my-program__muscle-groups">
                        <h2 class="my-program__muscle-groups-title">Группы мышц</h2>
                        <canvas id="muscleGroupsChart"></canvas>
                    </section>
                    <section class="my-program__statistic-content">
                        <section class="my-program__statistic-all">
                            <p class="my-program__statistic-all-item">Всего тренировок: <span><?php echo $user->program->count_workouts(); // number of all trainings ?></span></p>
                            <p class="my-program__statistic-all-item">Всего упражнений: <span><?php echo $user->program->count_exercises(); // number of all exercices ?></span></p>
                        </section>
                        <section class="my-program__progress">
                            <div class="my-program__progress-item">
                                <div class="my-program__progress-percent">
                                    <?php echo round($cnt_workouts_done / $cnt_workouts_all, 2) * 100; // progress of completed part of program ?> %
                                </div>
                                <h3 class="my-program__progress-item-title">Выполнен(но)</h3>
                                <p class="my-program__progress-item-text">Тренировок: <span><?php echo $cnt_workouts_done; // percents of completed workout?></span></p>
                            </div>
                            <div class="my-program__progress-item">
                                <div class="my-program__progress-percent">
                                    <?php echo round(($cnt_workouts_all - $cnt_workouts_done) / $cnt_workouts_all, 2) * 100; // progress of remaining part of program ?> %
                                </div>
                                <h3 class="my-program__progress-item-title">Осталось(ся)</h3>
                                <p class="my-program__progress-item-text">Тренировок: <span><?php echo $cnt_workouts_all - $cnt_workouts_done; // percents of remaining workout ?></span></p>
                            </div>
                        </section>
                    </section>
                </section>
            </section>
            <form action="" method="post">
                <?php if ($user->get_auth()){ // if user is authenticated ?>
                    <input type="hidden" name="end" value="1">
                    <button type="submit" class="button-text my-program__finish">Завершить досрочно</button>
                <?php } else if (!$user->get_auth() && !$user_data->set_program($conn) && $user_data->get_status() == "user" && $user_data->get_status() != "coach" && $user_data->get_status() != "doctor") { // if the user is watching other program?>
                    <button class="button-text my-program__friend-program" type="button">Начать эту программу</button>
                <?php } ?>
            </form>
        </div>

        <!-- Start program popup window-->
		<section class="popup-exercise popup-exercise__program-add">
			<form method="post" class="popup-exercise__content">
				<button type="button" class="popup-exercise__close-button"><img src="../img/close.svg" alt=""></button>
				<div class="popup-exercise__content-item">
                    <label class="popup-exercise__info-label" for="week">Количество недель</label>
                    <input class="popup-exercise__info-input" type="number" id="date" name="weeks">
                </div>
                <div class="popup-exercise__content-item">
                    <label class="popup-exercise__info-label" for="week">Дата начала</label>
                    <input class="popup-exercise__info-input" type="date" id="date" name="date_start">
                </div>
				<button type="button" class="button-text popup-exercise__submit-button">Начать</button>
			</form>
		</section>
    </main>

    <?php include "../templates/footer.html"; ?>

    <script src="https://cdn.jsdelivr.net/npm/swiper@10/swiper-element-bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        let friendProgramAddButton = document.querySelector('.popup-exercise__program-add .popup-exercise__submit-button'); // add friend program button
        let dateWokoutInput = document.querySelector('.popup-exercise__program-add .popup-exercise__info-input[type="date"]'); // date of workout startting

        document.querySelector('.popup-exercise__program-add .popup-exercise__info-input[type="number"]').addEventListener('input', function(){ // if weeks == 0, changing the button to the type 'button'
            if(this.value.length == 0){
                friendProgramAddButton.type = 'button';
            }
            else{
                friendProgramAddButton.type = 'submit';
            }
        });

        friendProgramAddButton.addEventListener('click', function(){ // when add friend program button is clicked
            if (!dateWokoutInput.value) { // if no start date is selected
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

				const formattedDate = `${year}-${month}-${day}`; // set today's date

				// set today's date in input
				dateWokoutInput.value = formattedDate;
			}
        });


        // Add friend program
        let programAddButton = document.querySelector('.my-program__friend-program');
        let programAddPopup = document.querySelector('.popup-exercise__program-add');

        // popup window for duration of program values
        if(programAddButton){
            programAddButton.addEventListener('click', function(){ // open popup window
                programAddPopup.classList.add("open");
            });
        }

        // buttons to close popup windows
		const closeBtn = document.querySelectorAll('.popup-exercise__close-button');
		for(let i = 0; i < closeBtn.length; i++){
			closeBtn[i].addEventListener('click', function(){ // open popup window
				programAddPopup.classList.remove("open");
			});
		}

		window.addEventListener('keydown', (e) => {
            if(e.key == "Escape"){
                programAddPopup.classList.remove("open");
            }
		});


        // Workout items
        let workoutItemArr = document.querySelectorAll('.day-workouts__card-content');

        let maxWorkoutItemHeight = 0;

        for(let i = 0; i < workoutItemArr.length; i++){
            if(workoutItemArr[i].clientHeight > maxWorkoutItemHeight){
                maxWorkoutItemHeight = Math.max(maxWorkoutItemHeight, workoutItemArr[i].clientHeight);
            }
        }

        // height of workout items
        for(let i = 0; i < workoutItemArr.length; i++){
            workoutItemArr[i].style.cssText = `height: ${maxWorkoutItemHeight}px;`;
        }



        // Muscle groups chart
        const ctx2 = document.getElementById('muscleGroupsChart');

        // create muscle groups chart
        new Chart(ctx2, {
            type: 'pie',
            data: {
                labels: ['Руки', 'Ноги', 'Грудь', 'Спина', 'Пресс', 'Кардио'],
                datasets: [{
                    label: 'Количество упражнений',
                    data: <?php echo $diagram_muscles; ?>,
                    borderWidth: 1,
                    color: '#000000',
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'right',
                        align: 'center',
                        labels: {
                            font: {
                                family: 'Open Sans',
                                size: 20,
                            },
                            color: '#000000000',
                        },
                    },
                title: {
                    display: false,
                }
                }
            },
        });
    </script>
</body>
</html>