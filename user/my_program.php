<?php
include "../templates/func.php";
include "../templates/settings.php";
$user_data->check_the_login();
if (!$user_data->set_program($conn)){
    header("Location: c_program_info.php");
}

if (isset($_POST['end'])){
    $sql = "UPDATE program_to_user SET date_start=0 WHERE user=".$user_data->get_id()."  AND date_start + weeks * 604800 >= ".time()." LIMIT 1";
    if ($conn->query($sql)){
        header("Refresh: 0");
    }else{
        echo $conn->error;
    }
}

$user_data->program->set_workouts($conn);
$user_data->program->set_additional_data($conn, $user_data->get_id());
$cnt_workouts_done = 0;
$cnt_workouts_all = 0;
$weekday_start = date("N", $user_data->program->date_start) - 1;

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
foreach ($user_data->program->workouts as $workout){
    foreach ($workout->set_muscles() as $key=>$value){
        $muscles[$key] += $value * $user_data->program->weeks;
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
                <section class="day-workouts__date">
                    <h1 class="day-workouts__date-title">Выберите дату:</h1>
                    <input class="day-workouts__date-input" type="week">
                </section>
                <swiper-container class="day-workouts__swiper" navigation="true">
                    <?php if ($weekday_start){ ?>
                    <!-- first slide -->
                    <swiper-slide class="day-workouts__slide">
                        <?php
                            for ($j = 0; $j < $weekday_start; $j++){
                                echo render(array("{{ day }}" => get_day($j)), "../templates/out_of_workout.html");
                            }
                            for ($j = $weekday_start; $j < 7; $j++){
                                $workout = $user_data->program->workouts[$j];
                                $is_done = $workout->is_done($conn, $user_data->get_id(), $user_data->program->date_start - $weekday_start * 86400 + $j * 86400);
                                $is_workout = $workout->print_workout_info_block($j, 1, $user_data->get_id(), $is_done);
                                $cnt_workouts_all += (int)!$workout->holiday;
                                $cnt_workouts_done += (int)$is_done;
                            }
                        ?>
                    </swiper-slide>
                    <?php }

                    $from = 0;
                    if ($weekday_start) $from = 1;
                    for ($i = $from; $i < $user_data->program->weeks; $i++){ ?>
                    <swiper-slide class="day-workouts__slide">
                        <?php
                        for ($j = 0; $j < 7; $j ++){
                            $workout = $user_data->program->workouts[$j];
                            $is_done = $workout->is_done($conn, $user_data->get_id(), $user_data->program->date_start - $weekday_start * 86400 + $j * 86400 + $i * 604800);
                            $is_workout = $workout->print_workout_info_block($j, 1, $user_data->get_id(), $is_done);
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
                                $workout = $user_data->program->workouts[$j];
                                $is_done = $workout->is_done($conn, $user_data->get_id(), $user_data->program->date_start - $weekday_start * 86400 + $j * 86400 + ($user_data->program->weeks - 1) * 604800);
                                $is_workout = $workout->print_workout_info_block($j, 1, $user_data->get_id(), $is_done);
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
                            <p class="my-program__statistic-all-item">Всего тренировок: <span><?php echo $user_data->program->count_workouts(); ?></span></p>
                            <p class="my-program__statistic-all-item">Всего упражнений: <span><?php echo $user_data->program->count_exercises(); ?></span></p>
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
                <section class="friends-block">
                    <!-- Title and button to search friends -->
                    <div class="friends-block__header">
                        <h1 class="friends-block__header-title">Прграммы друзей</h1>
                        <a href="search_users.php" class="friends-block__header-button" href=""><img src="../img/search.svg" alt=""></a>
                    </div>
                    <!-- Friends' workout swiper -->
                    <section class="friends-block__cover friends-block__cover--program">
                        <?php
                        $user_data->set_subscriptions($conn);
                        print_user_list_vert($conn, $user_data->subscriptions);
                        ?>
                    </section>
                </section>
            </section>
            <form action="" method="post">
                <input type="hidden" name="end" value="1">
                <button type="submit" class="button-text my-program__finish">Завершить досрочно</button>
            </form>
        </div>
    </main>

    <?php include "../templates/footer.html" ?>

    <script src="https://cdn.jsdelivr.net/npm/swiper@10/swiper-element-bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
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
        let friendsBlock = document.querySelector('.friends');
        friendsBlock.style.cssText = `height: ${document.querySelector('.my_program .info .statistic').clientHeight}px;`;
    </script>
</body>
</html>