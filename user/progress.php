<?php
include "../templates/func.php";
include "../templates/settings.php";

if (isset($_GET["user"]) && is_numeric($_GET["user"]))
    if ($_GET["user"] == $user_data->get_id())
        $user = $user_data;
    else
        $user = new User($conn, $_GET["user"]);
else
    $user = $user_data;

if ($user->get_auth() && isset($_POST["height"]) && isset($_POST["weight"]) && is_numeric($_POST["height"]) && is_numeric($_POST["weight"]) && $_POST["height"] >= 0 && $_POST["weight"] >= 0){
    $user->update_phys($conn, $_POST["height"], $_POST["weight"]);
}
$user->get_workout_history($conn);
$muscles = array(
    "arms" => 0,
    "legs" => 0,
    "press" => 0,
    "back" => 0,
    "chest" => 0,
    "cardio" => 0,
    "cnt" => 0
);

$exercise_cnt = 0;
$time_cnt = 0;

foreach ($user->workout_history as $item){
    $exercises = json_decode($item["exercises"]);
    $time_cnt += $item["time_spent"];
    foreach ($exercises as $exercise){
        foreach (get_exercise_muscles($conn, $exercise) as $muscle){
            $muscles["cnt"]++;
            $muscles[$muscle]++;
        }
        $exercise_cnt++;
    }
}
$time_cnt = round($time_cnt/60, 0);

$user->get_phys_updates($conn);
$height_array = array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0);
$weight_array = array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0);
$date_start = mktime(0, 0, 0, 1, 1, date("Y"));

if (count($user->phys_updates) != 0){
    $month_array = array($date_start);
    for ($i = 2; $i <= 13; $i++){
        array_push($month_array, mktime(0, 0, 0, $i, 1, date("Y")));
    }
    foreach ($user->phys_updates as $key=>$value){
        if ((int)$key < $month_array[0])
            break;
        for ($i = 0; $i < 12; $i++){
            if (($height_array[$i] == 0 and $weight_array[$i] == 0) and $month_array[$i] <= (int)$key && (int)$key < $month_array[$i + 1]){
                $height_array[$i] = $value["height"];
                $weight_array[$i] = $value["weight"];
                break;
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<?php inc_head(); ?>
<body>
	<?php include "../templates/header.php" ?>

	<main class="progress-block">
		<div class="container">
			<!-- First part of statistic(trainings diagram & lasr trainings) -->
			<section class="progress-block__trainings">
				<!-- Count of trainings chart -->
				<section class="progress-block__trainings-chart">
                    <form class="progress-block__physical-data-form" method="post">
                        <select class="progress-block__physical-data-select" name="" id="">
                            <option value="year">Год</option>
                            <option value="month">Месяц</option>
                        </select>
                    </form>
					<canvas id="trainingStatisticChart"></canvas>
				</section>
				<!-- Last trainings block -->
                <?php $user->print_workout_history($conn); ?>
			</section>

			<!-- Second part of statistic(training info and muscles & physical diagram) -->
			<section class="progress-block__info">
				<!-- Training info -->
				<section class="progress-block__workouts">
					<!-- Muscle groups diagram -->
					<div class="progress-block__workouts-info">
						<section class="progress-block__muscle-groups">
							<h2 class="progress-block__muscle-groups-title">Группы мышц</h2>
							<canvas class="progress-block__muscle-groups-chart" id="muscleGroupsChart"></canvas>
						</section>
						<!-- Statistic info -->
						<section class="progress-block__workouts-statistic">
                            <h3 class="progress-block__workouts-statistic-title">Всего:</h3>
							<p class="progress-block__workouts-statistic-item">Тренировок: <span><?php echo count($user->workout_history); ?></span></p>
							<p class="progress-block__workouts-statistic-item">Программ: <span><?php echo $user->get_program_amount($conn); ?></span></p>
							<p class="progress-block__workouts-statistic-item">Упражнений: <span><?php echo $exercise_cnt; ?></span></p>
                            <p class="progress-block__workouts-statistic-item">Затрачено минут:<span><?php echo $time_cnt; ?></span></p>
						</section>
					</div>
					<!-- Current info -->
                    <div class="progress-block__line"></div>
					<section class="progress-block__physical-info">
						<p class="progress-block__physical-info-item">Вес:  кг</p>
						<p class="progress-block__physical-info-item">Рост: 0 см</p>
                        <?php if ($user->get_auth()){ ?>
						    <button class="button-text progress-block__physical-info-button">Добавить данные<img src="../img/add.svg" alt=""></button>
                        <?php } ?>
					</section>
				</section>
				<!-- Physical block -->
				<section class="progress-block__physical-data">
					<!-- Navigation -->
					<nav class="progress-block__physical-data-navigation">
						<!-- Button to other physic(weight or length) -->
						<input type="button" value="РОСТ" class="button-text progress-block__physical-data-button">
					</nav>
					
					<!-- Diagram swiper -->
					<section class="progress-block__physical-data-swiper">
                        <div class="progress-block__physical-data-chart progress-block__physical-data-chart--weight">
                            <canvas id="physicalDataChart_weight"></canvas>
                        </div> 
                        <div class="progress-block__physical-data-chart progress-block__physical-data-chart--height">
                            <canvas id="physicalDataChart_height"></canvas>
                        </div>
					</section>
				</section>
			</section>
			<section class="progress-block__programm">
				<!-- Progress line and count of percent -->
				<div class="progress-block__programm-progress">
				  <h2 class="progress-block__programm-title">Моя программа</h2>
				  <div class="progress-block__programm-info">
					<div class="progress-block__programm-line">
						<p class="progress-block__programm-percents"><?php
                            if ($user->set_program($conn)){
                                $user->program->set_additional_data($conn, $user->get_id());
                                $cnt_workouts_per_week = 0;
                                foreach ($user->program->program as $workout)
                                    if ($workout != 0)
                                        $cnt_workouts_per_week++;

                                $cnt_all_workouts = $cnt_workouts_per_week * $user->program->weeks;
                                $cnt_done = 0;
                                $progress = (time() - $user->program->date_start) / ($user->program->weeks * 604800) * 100;
                                if ($cnt_all_workouts == 0){
                                    echo 0;
                                }else{
                                    $sql = "SELECT id FROM workout_history WHERE user=".$user->get_id()." AND date_completed>=".$user->program->date_start;
                                    if ($result = $conn->query($sql)){
                                        foreach ($result as $item) $cnt_done++;
                                        echo round(($cnt_done / $cnt_all_workouts) * 100, 0);
                                    } else
                                        echo 0;
                                }
                                }else{
                                echo 0;
                            }
                            ?>%</p>
						<div class="progress-block__programm-finish" class="finish"></div>
					</div>
					<a class="progress-block__programm-button" href="my_program.php?user=<?php echo $user->get_id(); ?>"><img src="../img/my_programm_black.svg" alt=""></a>
				  </div>
				</div>
			</section>
		</div>



        <!-- Physics data edit -->
		<section class="popup-exercise popup-exercise--physics-data">
			<form method="post" class="popup-exercise__content">
				<button type="button" class="popup-exercise__close-button"><img src="../img/close.svg" alt=""></button>
				<div class="popup-physics-data__item">
                    <p class="popup-physics-data__item-name">Укажите текущий рост (см)</p>
                    <input name="height" class="popup-physics-data__item-input popup-physics-data__item-input--height" type="number">
                </div>
                <div class="popup-physics-data__item">
                    <p class="popup-physics-data__item-name">Укажите текущий вес (кг)</p>
                    <input name="weight" class="popup-physics-data__item-input popup-physics-data__item-input--weight" type="number">
                </div>
				<button class="button-text popup-exercise__submit-button popup-exercise__submit-button--physic">Добавить</button>
			</form>
		</section>
	</main>


	<?php include "../templates/footer.html"; ?>
	<script src="https://cdn.jsdelivr.net/npm/swiper@10/swiper-element-bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Choose period to see progress data
        let periodSelects = document.querySelectorAll('.progress-block__physical-data-select');
        let periodForms = document.querySelectorAll('.progress-block__physical-data-form');

        if(localStorage.getItem('trainingDataPeriod')){
            periodSelects[0].value = localStorage.getItem('trainingDataPeriod');
        }
        else{
            localStorage.setItem('trainingDataPeriod', periodSelects[0].value);
        }

        for(let i = 0; i < periodSelects.length; i++) {
            periodSelects[i].addEventListener('change', function(){
                if(i == 0){
                    localStorage.setItem('trainingDataPeriod', periodSelects[i].value);
                }
                periodForms[i].submit();
            });
        }


        // Count of training chart
        const ctx1 = document.getElementById('trainingStatisticChart');

        trainingPeriodArray = [];
        trainingPeriodData = [];
        if(periodSelects[0].value == 'year'){
            trainingPeriodArray = ['Январь', 'Февраль', 'Март', 'Апрель', 'Май', 'Июнь', 'Июль', 'Август', 'Сентябрь', 'Октябрь', 'Ноябрь', 'Декабрь'];
            trainingPeriodData = <?php echo json_encode(get_graph_workout_data_year($user->workout_history)); ?>;
        }
        if(periodSelects[0].value == 'month'){
            trainingPeriodArray = ['1ая неделя', '2ая неделя', '3я неделя', '4ая неделя', '5ая неделя'];
            trainingPeriodData = <?php echo json_encode(get_graph_workout_data_month($user->workout_history)); ?>;
        }

        new Chart(ctx1, {
            type: 'line',
            data: {
            labels:  trainingPeriodArray,
            datasets: [{
                label: 'Кол-во тренировок',
                data: trainingPeriodData,
                borderWidth: 3,
                backgroundColor: '#00C91D',
                color: '#000000',
                borderColor: '#000000'
            }]
            },
            options: {
                responsive: true,
                responsive: true,
                scales: {
                    x: {
                        grid: {
                            display: true,
                            color: 'rgba(231, 231, 231, 1)'
                        },
                        title: {
                            display: true,
                            text: 'Месяца',
                            font: {
                                size: 16,
                                family: 'Open Sans',
                            }
                        }
                    },
                    y: {
                        grid: {
                            display: true,
                            color: 'rgba(231, 231, 231, 1)'
                        },
                        title: {
                            display: true,
                            text: 'Количество тренировок',
                            font: {
                                size: 16,
                                family: 'Open Sans',
                            }
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false,
                    },
                    title: {
                        display: true,
                        text: 'Количество тренировок',
                        font: {
                            size: 20,
                            family: 'Open Sans',
                        }               
                    }
                }
            },
        });


        // Muscle groups chart
        const ctx2 = document.getElementById('muscleGroupsChart');
        let allXerciseCount = 0;
        let currentExerciseCountArray = <?php echo json_encode(array($muscles['arms'], $muscles['legs'], $muscles['chest'], $muscles['back'], $muscles['press'], $muscles['cardio'])); ?>;
        for(let i = 0; i < currentExerciseCountArray.length; i++){
            allXerciseCount += currentExerciseCountArray[i];
        }
        for(let i = 0; i < currentExerciseCountArray.length; i++){
            currentExerciseCountArray[i] = Math.round(currentExerciseCountArray[i] / allXerciseCount * 100);
        }
        

        new Chart(ctx2, {
            type: 'pie',
            data: {
                labels: ['Руки', 'Ноги', 'Грудь', 'Спина', 'Пресс', 'Кардио'],
                datasets: [{
                    label: 'Процент упражнений',
                    data: currentExerciseCountArray,
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
                            color: '#000000',
                        },
                    },
                title: {
                    display: false,
                }
                }
            },
        });


        // Physical data chart
        const ctx3 = document.getElementById('physicalDataChart_weight');

        new Chart(ctx3, {
            type: 'line',
            data: {
            labels: ['Я', 'Ф', 'М', 'А', 'М', 'И', 'И', 'А', 'С', 'О', 'Н', 'Д'],
            datasets: [{
                label: 'Вес за год',
                data: <?php echo json_encode($weight_array); ?>,
                borderWidth: 3,
                backgroundColor: '#00C91D',
                color: '#000000',
                borderColor: '#000000'
            }]
            },
            options: {
                responsive: true,
                responsive: true,
                scales: {
                    x: {
                        grid: {
                            display: true,
                            color: 'rgba(231, 231, 231, 1)'
                        },
                        title: {
                            display: true,
                            text: 'Месяца',
                            font: {
                                size: 16,
                                family: 'Open Sans',
                            }
                        }
                    },
                    y: {
                        grid: {
                            display: true,
                            color: 'rgba(231, 231, 231, 1)'
                        },
                        title: {
                            display: true,
                            text: 'Вес в киллограмах',
                            font: {
                                size: 16,
                                family: 'Open Sans',
                            }
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false,
                    },
                    title: {
                        display: true,
                        text: 'Вес',
                        font: {
                            size: 20,
                            family: 'Open Sans',
                        }               
                    }
                }
            },
        });


        // Physical data chart
        const ctx4 = document.getElementById('physicalDataChart_height');

        new Chart(ctx4, {
            type: 'line',
            data: {
            labels: ['Я', 'Ф', 'М', 'А', 'М', 'И', 'И', 'А', 'С', 'О', 'Н', 'Д'],
            datasets: [{
                label: 'Рост за неделю',
                data: <?php echo json_encode($height_array); ?>,
                borderWidth: 3,
                backgroundColor: '#00C91D',
                color: '#000000',
                borderColor: '#000000'
            }]
            },
            options: {
                responsive: true,
                responsive: true,
                scales: {
                    x: {
                        grid: {
                            display: true,
                            color: 'rgba(231, 231, 231, 1)'
                        },
                        title: {
                            display: true,
                            text: 'Месяца',
                            font: {
                                size: 16,
                                family: 'Open Sans',
                            }
                        }
                    },
                    y: {
                        grid: {
                            display: true,
                            color: 'rgba(231, 231, 231, 1)'
                        },
                        title: {
                            display: true,
                            text: 'Рост в сантиметрах',
                            font: {
                                size: 16,
                                family: 'Open Sans',
                            }
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false,
                    },
                    title: {
                        display: true,
                        text: 'Рост',
                        font: {
                            size: 20,
                            family: 'Open Sans',
                        }               
                    }
                }
            },
        });


        let togglePhysicalDataButton = document.querySelector('.progress-block__physical-data-button');
        let weightChart = document.querySelector('.progress-block__physical-data-chart--weight');
        let heightChart = document.querySelector('.progress-block__physical-data-chart--height');
        weightChart.style.cssText = `display: none;`;
        togglePhysicalDataButton.click();

        togglePhysicalDataButton.addEventListener('click', function(){
            if(togglePhysicalDataButton.value == 'РОСТ'){
                console.log(togglePhysicalDataButton.value)
                togglePhysicalDataButton.value = 'ВЕС';
                weightChart.style.cssText = `display: none;`;
                heightChart.style.cssText = `display: block;`;
            }
            else if(togglePhysicalDataButton.value == 'ВЕС'){
                togglePhysicalDataButton.value = 'РОСТ';
                weightChart.style.cssText = `display: block;`;
                heightChart.style.cssText = `display: none;`;
            }
        });

        
		// Height of last-trainings block
        let lastTrainingsBlock = document.querySelector('.last-trainings');
        lastTrainingsBlock.style.cssText = `height: ${document.querySelector('.progress-block__trainings-chart').clientHeight}px;`;
    
    
    
        // Popup window for physic data
        let PhysicDataEditButton = document.querySelector('.progress-block__physical-info-button');

        let PhysicDataPopup = document.querySelector('.popup-exercise--physics-data');

        let PhysicDataCurrent = document.querySelectorAll('.progress-block__physical-info-item');

        let weightArray = <?php echo json_encode($weight_array) ?>;
        let heightArray = <?php echo json_encode($height_array); ?>;

        PhysicDataCurrent[0].innerHTML = `${weightArray[weightArray.length - 2]} кг`;
        PhysicDataCurrent[1].innerHTML = `${heightArray[heightArray.length - 2]} см`;

        if(PhysicDataEditButton){
            PhysicDataEditButton.addEventListener('click', function(){
                document.querySelector('.popup-physics-data__item-input--height').value = (PhysicDataCurrent[1].innerHTML).split(' ')[1];
                document.querySelector('.popup-physics-data__item-input--weight').value = (PhysicDataCurrent[0].innerHTML).split(' ')[1];
                PhysicDataPopup.classList.add("open");
            });
        }
        
        


        const closeBtn = document.querySelectorAll('.popup-exercise__close-button');
		for(let i = 0; i < closeBtn.length; i++){
			closeBtn[i].addEventListener('click', function(){
				PhysicDataPopup.classList.remove("open");
			});
		}

		window.addEventListener('keydown', (e) => {
            if(e.key == "Escape" && PhysicDataEditButton){
                PhysicDataEditButton.classList.remove("open");
            }
		});

		document.querySelector('.popup-exercise__content').addEventListener('click', event => {
			event.isClickWithInModal = true;
		});


        let progressProgrammLine = document.querySelector('.progress-block__programm-finish');
        let progressProgrammPercents = document.querySelector('.progress-block__programm-percents');

        progressProgrammLine.style.cssText = `width: ${progressProgrammPercents.innerHTML};`;
    
    </script>
</body>
</html>