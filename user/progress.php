<?php
include "../templates/func.php";
include "../templates/settings.php";

if (isset($_POST["height"]) && isset($_POST["weight"]) && is_numeric($_POST["height"]) && is_numeric($_POST["weight"]) && $_POST["height"] >= 0 && $_POST["weight"] >= 0){
    $user_data->update_phys($conn, $_POST["height"], $_POST["weight"]);
}
$user_data->get_workout_history($conn);
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

foreach ($user_data->workout_history as $item){
    $exercises = json_decode($item["exercises"]);
    foreach ($exercises as $exercise){
        foreach (get_exercise_muscles($conn, $exercise) as $muscle){
            $muscles["cnt"]++;
            $muscles[$muscle]++;
        }
        $exercise_cnt++;
    }
}
$user_data->get_phys_updates($conn);
$height_array = array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0);
$weight_array = array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0);
$date_start = mktime(0, 0, 0, 1, 1, date("Y"));

if (count($user_data->phys_updates) != 0){
    $month_array = array($date_start);
    for ($i = 2; $i <= 13; $i++){
        array_push($month_array, mktime(0, 0, 0, $i, 1, date("Y")));
    }
    foreach ($user_data->phys_updates as $key=>$value){
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
					<canvas id="trainingStatisticChart"></canvas>
				</section>
				<!-- Last trainings block -->
                <?php $user_data->print_workout_history($conn); ?>
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
							<p class="progress-block__workouts-statistic-item">Тренировок: <span><?php echo count($user_data->workout_history); ?></span></p>
							<p class="progress-block__workouts-statistic-item">Программ: <span><?php echo $user_data->get_program_amount($conn); ?></span></p>
							<p class="progress-block__workouts-statistic-item">Упражнений: <span><?php echo $exercise_cnt; ?></span></p>
						</section>
					</div>
					<!-- Current info -->
					<section class="progress-block__physical-info">
						<p class="progress-block__physical-info-item">Вес: 80 кг</p>
						<p class="progress-block__physical-info-item">Рост: 180 см</p>
						<button class="button-text progress-block__physical-info-button">Добавить<img src="../img/add.svg" alt=""></button>
					</section>
				</section>
				<!-- Physical block -->
				<section class="progress-block__physical-data">
					<!-- Navigation -->
					<nav class="progress-block__physical-data-navigation">
						<!-- Year & month & week -->
						<select class="progress-block__physical-data-select" name="" id="">
							<option value="value1" selected>Год</option>
							<option value="value2">Месяц</option>
							<option value="value3">Неделя</option>
						</select>
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
                            if ($user_data->set_program($conn)){
                                $user_data->program->set_additional_data($conn, $user_data->get_id());
                                $progress = (time() - $user_data->program->date_start) / ($user_data->program->weeks * 604800) * 100;
                                if ($progress < 0){
                                    echo '0';
                                }else if ($progress > 100){
                                    echo '100';
                                }else{
                                    echo round($progress);
                                }
                            }else{
                                echo '0';
                            }
                            ?>%</p>
						<div class="progress-block__programm-finish" class="finish"></div>
					</div>
					<a class="progress-block__programm-button" href="my_program.php"><img src="../img/my_programm_black.svg" alt=""></a>
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

	<?php include "../templates/footer.html" ?>

	<script src="https://cdn.jsdelivr.net/npm/swiper@10/swiper-element-bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Count of training chart
        const ctx1 = document.getElementById('trainingStatisticChart');

        new Chart(ctx1, {
            type: 'line',
            data: {
            labels: ['Январь', 'Февраль', 'Март', 'Апрель', 'Май', 'Июнь', 'Июль', 'Август', 'Сентябрь', 'Октябрь', 'Ноябрь', 'Декабрь'],
            datasets: [{
                label: 'Колво тренировок',
                data: <?php echo json_encode(get_graph_workout_data($user_data->workout_history)); ?>,
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
                        text: 'Количество тренировок - 2023',
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

        new Chart(ctx2, {
            type: 'pie',
            data: {
                labels: ['Руки', 'Ноги', 'Грудь', 'Спина', 'Пресс', 'Кардио'],
                datasets: [{
                    label: 'Количество упражнений',
                    data: <?php echo json_encode(array($muscles['arms'], $muscles['legs'], $muscles['chest'], $muscles['back'], $muscles['press'], $muscles['cardio'])); ?>,
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
                label: 'Вес за неделю',
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

        PhysicDataEditButton.addEventListener('click', function(){
            document.querySelector('.popup-physics-data__item-input--height').value = (PhysicDataCurrent[1].innerHTML).split(' ')[1];
            document.querySelector('.popup-physics-data__item-input--weight').value = (PhysicDataCurrent[0].innerHTML).split(' ')[1];
			PhysicDataPopup.classList.add("open");
		});


        const closeBtn = document.querySelectorAll('.popup-exercise__close-button');
		for(let i = 0; i < closeBtn.length; i++){
			closeBtn[i].addEventListener('click', function(){
				PhysicDataPopup.classList.remove("open");
			});
		}

		window.addEventListener('keydown', (e) => {
            if(e.key == "Escape"){
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