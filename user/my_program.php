<?php
include "../templates/func.php";
include "../templates/settings.php";
$user_data->check_the_login();
if (isset($_GET["user"]) && is_numeric($_GET["user"]))
    if ($_GET["user"] == $user_data->get_id())
        $user = $user_data;
    else
        $user = new User($conn, $_GET["user"]);
 else
     $user = $user_data;

if (!$user->set_program($conn)){
    if ($user->get_auth())
        header("Location: c_program_info.php");
    else
        header("Location: profile.php");
}

if (isset($_POST['end']) && $user->get_auth()){
    $sql = "UPDATE program_to_user SET date_start=0 WHERE user=".$user->get_id()."  AND date_start + weeks * 604800 >= ".time()." LIMIT 1";
    if ($conn->query($sql)){
        header("Refresh: 0");
    }else{
        echo $conn->error;
    }
}

if (isset($_POST["weeks"]) && $_POST["weeks"] > 0 && !$user->get_auth()){
    if (empty($_POST["date_start"]))
        $date_start = mktime(0, 0, 0, date("m"), date("d"), date("Y"));
    else
        $date_start = strtotime($_POST["date_start"]);

    $program_id = $user->program->get_id();

    $sql2 = "INSERT INTO program_to_user (user, program, date_start, weeks) VALUES (".$user_data->get_id().", $program_id, $date_start, ".$_POST['weeks'].")";
    $sql3 = "INSERT INTO news (message, user, date, personal) VALUES ('Пользователь начал программу друга.', ".$user_data->get_id().", ".time().", 0)";

    if ($conn->query($sql2) && $conn->query($sql3)){
        header("Location: my_program.php");
    }else{
        echo $conn->error;
    }
}

$user->program->set_workouts($conn);
$user->program->set_additional_data($conn, $user->get_id());
$cnt_workouts_done = 0;
$cnt_workouts_all = 0;
$weekday_start = date("N", $user->program->date_start) - 1;

$muscles = array(
    "arms" => 0,
    "legs" => 0,
    "press" => 0,
    "back" => 0,
    "chest" => 0,
    "cardio" => 0,
    "cnt" => 0
);

#counting muscles
foreach ($user->program->workouts as $workout){
    foreach ($workout->set_muscles() as $key=>$value){
        $muscles[$key] += $value * $user->program->weeks;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<?php inc_head(); ?>
<body>
    <?php include "../templates/header.php" ?>
    <main class="my-program">
        <div class="container">
            <section class="day-workouts">
                <swiper-container class="day-workouts__swiper" navigation="true">
                    <?php if ($weekday_start){ ?>
                    <!-- first slide -->
                    <swiper-slide class="day-workouts__slide">
                        <?php
                            for ($j = 0; $j < $weekday_start; $j++){
                                echo render(array("{{ day }}" => get_day($j)), "../templates/out_of_workout.html");
                            }
                            for ($j = $weekday_start; $j < 7; $j++){
                                $workout = $user->program->workouts[$j];
                                $is_done = $workout->is_done($conn, $user->get_id(), $user->program->date_start - $weekday_start * 86400 + $j * 86400);
                                $is_workout = $workout->print_workout_info_block($j, 1, $user->get_id(), $is_done);
                                $cnt_workouts_all += (int)!$workout->holiday;
                                $cnt_workouts_done += (int)$is_done;
                            }
                        ?>
                    </swiper-slide>
                    <?php }

                    $from = 0;
                    if ($weekday_start) $from = 1;
                    for ($i = $from; $i < $user->program->weeks; $i++){ ?>
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
                            for ($j = 0; $j < $weekday_start; $j++){
                                $workout = $user->program->workouts[$j];
                                $is_done = $workout->is_done($conn, $user->get_id(), $user->program->date_start - $weekday_start * 86400 + $j * 86400 + ($user->program->weeks - 1) * 604800);
                                $is_workout = $workout->print_workout_info_block($j, 1, $user->get_id(), $is_done);
                                $cnt_workouts_all += (int)!$workout->holiday;
                                $cnt_workouts_done += (int)$is_done;                            }

                            for ($j = $weekday_start; $j < 7; $j++){
                                echo render(array("{{ day }}" => get_day($j)), "../templates/out_of_workout.html");
                            }
                            ?>
                        </swiper-slide>
                    <?php } ?>
                </swiper-container>
            </section>
            <?php $diagram_muscles = json_encode(array($muscles['arms'], $muscles['legs'], $muscles['chest'], $muscles['back'], $muscles['press'], $muscles['cardio'])); ?>
            <section class="my-program__info">
                <section class="my-program__statistic">
                    <section class="my-program__muscle-groups">
                        <h2 class="my-program__muscle-groups-title">Группы мышц</h2>
                        <canvas id="muscleGroupsChart"></canvas>
                    </section>
                    <section class="my-program__statistic-content">
                        <section class="my-program__statistic-all">
                            <p class="my-program__statistic-all-item">Всего тренировок: <span><?php echo $user->program->count_workouts(); ?></span></p>
                            <p class="my-program__statistic-all-item">Всего упражнений: <span><?php echo $user->program->count_exercises(); ?></span></p>
                        </section>
                        <section class="my-program__progress">
                            <div class="my-program__progress-item">
                                <div class="my-program__progress-percent">
                                    <?php echo round($cnt_workouts_done / $cnt_workouts_all, 2) * 100; ?> %
                                </div>
                                <h3 class="my-program__progress-item-title">Выполнен(но)</h3>
                                <p class="my-program__progress-item-text">Тренировок: <span><?php echo $cnt_workouts_done; ?></span></p>
                            </div>
                            <div class="my-program__progress-item">
                                <div class="my-program__progress-percent">
                                    <?php echo round(($cnt_workouts_all - $cnt_workouts_done) / $cnt_workouts_all, 2) * 100; ?> %
                                </div>
                                <h3 class="my-program__progress-item-title">Осталось(ся)</h3>
                                <p class="my-program__progress-item-text">Тренировок: <span><?php echo $cnt_workouts_all - $cnt_workouts_done; ?></span></p>
                            </div>
                        </section>
                    </section>
                </section>
            </section>
            <form action="" method="post">
                <?php if ($user->get_auth()){ ?>
                    <input type="hidden" name="end" value="1">
                    <button type="submit" class="button-text my-program__finish">Завершить досрочно</button>
                <?php } else if (!$user->get_auth() && !$user_data->set_program($conn)) { ?>
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
				<button class="button-text popup-exercise__submit-button">Начать</button>
			</form>
		</section>
    </main>

    <?php include "../templates/footer.html"; ?>

    <script src="https://cdn.jsdelivr.net/npm/swiper@10/swiper-element-bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Add friend program
        let programAddButton = document.querySelector('.my-program__friend-program');
        let programAddPopup = document.querySelector('.popup-exercise__program-add');

        if(programAddButton){
            programAddButton.addEventListener('click', function(){
                programAddPopup.classList.add("open");
            });
        }

		const closeBtn = document.querySelectorAll('.popup-exercise__close-button');
		for(let i = 0; i < closeBtn.length; i++){
			closeBtn[i].addEventListener('click', function(){
				programAddPopup.classList.remove("open");
			});
		}

		window.addEventListener('keydown', (e) => {
            if(e.key == "Escape"){
                programAddPopup.classList.remove("open");
            }
		});

		document.querySelector('.popup-exercise__content').addEventListener('click', event => {
			event.isClickWithInModal = true;
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



        // Muscle groups chart
        const ctx2 = document.getElementById('muscleGroupsChart');

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


        // Height of friends block
        let friendsBlock = document.querySelector('.friends-block');
        friendsBlock.style.cssText = `height: ${document.querySelector('.my-program__statistic').clientHeight}px;`;
    </script>
</body>
</html>