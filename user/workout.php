<?php
include "../templates/func.php"; // Include functions file
include "../templates/settings.php"; // Include settings file

$user_data->set_program($conn); // Setting the program for the user based on the connection to the database
$weekday = date("N") - 1; // Getting the current day of the week

?>
<!DOCTYPE html>
<html lang="en">
<?php inc_head(); // print head.php  ?>
<body>
    <?php include "../templates/header.php" // print header template  ?>

    <main class="workouts-block">
        <div class="container">
            <?php if ($user_data->program->get_id() > 0){ // Checks if the id is greater than zero ?>
            <!-- Day's workout swiper -->
            <swiper-container class="workouts-swiper" navigation="true">
                <swiper-slide class="workouts-slide">
                <!-- Slide -->
                <?php
                    $workout = new Workout($conn, $user_data->program->program[$weekday], $weekday); // Creating a new Workout object based on the user's program for the current weekday
                    if ($workout->holiday){ // Checking if it's a holiday workout
                        include "../templates/holiday.html"; // print holiday template
                    }else{ $workout->set_muscles(); // If it's not a holiday workout setting muscles for the workout ?>
                        <!-- slide(no arrows) -->
                        <section class="workouts-card">
                            <!-- Title -->
                            <form method="post" class="workouts-card__header">
                                <h2 class="workouts-card__date"><?php echo date("d.m.Y"); // print date of workout ?></h2>
                            </form>
                            <!-- Content of workout -->
                            <section class="workouts-card__content">
                                <!-- Exercises array -->
                                <section class="workouts-card__exercises-cover">
                                    <!-- Exercise items -->
                                    <?php $workout->print_exercises($conn); // print exercises array ?>
                                </section>
                                <!-- Info about day workout -->
                                <?php
                                // Calculating timestamps for the current day (if the training is today, then we will allow the passage)
                                $date1 = mktime(0, 0, 0, date("m"), date("d"), date("Y"));
                                $date2 = mktime(23, 59, 59, date("m"), date("d"), date("Y"));
                                $sql = "SELECT id FROM workout_history WHERE date_completed > $date1 AND date_completed < $date2 AND user=".$user_data->get_id();
                                if ($result = $conn->query($sql)){
                                    if ($result->num_rows == 0){ // If no workout completed for the day
                                        $workout->print_workout_info(2, $user_data->get_id(), 1);
                                    }else{ // If workout completed for the day
                                        $workout->print_workout_info(2, $user_data->get_id());
                                    }
                                }else{ // If there's an issue with the query, print workout info as a new workout
                                    $workout->print_workout_info(2, $user_data->get_id());
                                }
                                ?>
                            </section>
                        </section>
                    <?php } ?>
                </swiper-slide>
                <swiper-slide class="workouts-slide">
                    <?php
                    // Incrementing the weekday; if it's Sunday (6), resetting to Monday (0)
                    if ($weekday == 6){
                        $weekday = 0;
                    }else{
                        $weekday++;
                    }
                    $workout = new Workout($conn, $user_data->program->program[$weekday], $weekday); // Creating a new Workout object based on the incremented weekday
                    if ($workout->holiday){ // Checking if it's a holiday
                        include "../templates/holiday.html"; // Including a holiday template
                    }else{ $workout->set_muscles(); // Setting muscles for the workout ?>
                    <section class="workouts-card">
                        <div class="workouts-card__header">
                            <!-- Displaying the workout date (tomorrow's date) -->
                            <h2 class="workouts-card__date"><?php echo date("d.m.Y", time() + 86400); ?></h2>
                        </div>
                        <section class="workouts-card__content">
                            <section class="workouts-card__exercises-cover">
                                <?php $workout->print_exercises($conn); // print exercises array  ?>
                            </section>
                            <?php $workout->print_workout_info(0, $user_data->get_id()); // print tomorrow workout info ?>
                        </section>
                    </section>
                    <?php } ?>
                </swiper-slide>
            </swiper-container>
            <?php } else { // if the user does not have a program ?>
                <div class="workouts-card__no-program">
                    <p class="workouts-card__no-program-title">Нет программы</p>
                </div>
            <?php } ?>
            <section class="workout-other">
                <?php $user_data->print_workout_history($conn); // print workout history ?>
                <!-- Button to program page -->
                <section class="workout-other__buttons">
                    <a class="button-text workout-other__button" href="my_program.php"><p>Моя программа</p> <img src="../img/my_programm.svg" alt=""></a>
                </section>
            </section>
        </div>


        <!-- Timer edit(work and rest) -->
		<section class="popup-exercise popup-exercise--timer-edit">
			<section class="popup-exercise__content popup-exercise--timer-edit__content">
				<button type="button" class="popup-exercise__close-button"><img src="../img/close.svg" alt=""></button>
                <div class="popup-exercise--timer-edit__item">
                    <label class="popup-exercise--timer-edit__item__label" for="time_for_work">Время на подход (мин)</label>
                    <input class="popup-exercise__input-item popup-exercise--timer-edit__item__input" id="time_for_work" type="number">
                </div>
				<div class="popup-exercise--timer-edit__item">
                    <label class="popup-exercise--timer-edit__item__label" for="time_for_rest">Время на отдых (мин)</label>
                    <input class="popup-exercise__input-item popup-exercise--timer-edit__item__input" id="time_for_rest" type="number">
                </div>
				<button class="button-text popup-exercise__submit-button popup-exercise__button--edit-timer">Сохранить</button>
			</section>
		</section>
    </main>

    <?php include "../templates/footer.html"; // include footer template ?>

    <!-- loading the slider -->
    <script src="https://cdn.jsdelivr.net/npm/swiper@10/swiper-element-bundle.min.js"></script>
    <script>
        // Button to see exercise info
        let infoExerciseButton = document.querySelectorAll('.exercise-item__info-btn');
        let closeInfoExerciseButton = document.querySelectorAll('.exercise-item__info-close');
        let infoBlock = document.querySelectorAll('.exercise-item__info-content');

        for(let i = 0; i < infoExerciseButton.length; i++){
            infoExerciseButton[i].addEventListener('click', function(){ // open exercise info
                infoBlock[i].style.cssText = `top: -1%;`;
            });
        }
        for(let i = 0; i < closeInfoExerciseButton.length; i++){
            closeInfoExerciseButton[i].addEventListener('click', function(){ // close exercise info
                infoBlock[i].style.cssText = `top: -101%;`;
            });
        }


        // info items' spans width for info slide(same width)
        let infoItemsSpans = document.querySelectorAll('.workouts-card__item span');
        let maxSpanWidth = 0;

        for(let i = 0; i < infoItemsSpans.length; i++){
            maxSpanWidth = Math.max(maxSpanWidth, infoItemsSpans[i].clientWidth); // finding the largest width
        }

        for(let i = 0; i < infoItemsSpans.length; i++){
            infoItemsSpans[i].style.cssText = `width: ${maxSpanWidth}px;`; // set the largest width
        }

        //Difficult for exercises
		let difficultCountArr = document.querySelectorAll('.exercise-item__difficult-number');
		let difficultBlockArr = document.querySelectorAll('.exercise-item__difficult');

		for(let i = 0; i < difficultCountArr.length; i++){
			difficultBlockArr[i].innerHTML = '';
            for(let j = 0; j < 5; j++){ // creating circles of difficulty for the exercise card
				let newElem = document.createElement('div');
				newElem.classList.add('exercise-item__difficult-item');
				if(j > Number(difficultCountArr[i].innerHTML) - 1){
					newElem.classList.add('exercise-item__difficult-item--disabled');
				}
				difficultBlockArr[i].appendChild(newElem);
			}
        }


        // Timer (set time for work and rest)
        let TimerEditButton = document.querySelector('.day-workouts__card-button--time');
        let TimerEditPopup = document.querySelector('.popup-exercise--timer-edit');

        if(TimerEditButton){
            TimerEditButton.addEventListener('click', function(){ // open popup window for timer edetting
                TimerEditPopup.classList.add("open");
            });
        }

		const closeBtn = document.querySelectorAll('.popup-exercise__close-button');
		for(let i = 0; i < closeBtn.length; i++){
			closeBtn[i].addEventListener('click', function(){ // close popup window for timer edetting
				TimerEditPopup.classList.remove("open");
			});
		}

		window.addEventListener('keydown', (e) => { // close popup window if escape pressed
		if(e.key == "Escape"){
			TimerEditPopup.classList.remove("open");
		}
		});



        // Data for timer
        let submitTimerButton = document.querySelector('.popup-exercise__button--edit-timer');
        let TimerEditInputs = document.querySelectorAll('.popup-exercise--timer-edit__item__input');
        let time = localStorage.getItem(`SpendWorkoutTime`);

        for(let i = 0; i < TimerEditInputs.length; i++){
            TimerEditInputs[i].addEventListener('input', function(){ // checking the values for the timer
                if (this.value < 0) {
					this.value = 0;
				}
                else if(this.value != 0 && this.value < 0.1){
                    this.value = 0.1;
                }
            });
        }

        submitTimerButton.addEventListener('click', function(){ // set the value of the rest and work time in the localstorage when pressing the button
            let TimeForWork = TimerEditInputs[0].value;
            let TimeForRest = TimerEditInputs[1].value;
            if(TimeForWork != ''){
                localStorage.setItem("TimeForWork", TimeForWork);
            }
            if(TimeForRest != ''){
                localStorage.setItem("TimeForRest", TimeForRest);
            }
            TimerEditPopup.classList.remove("open");
        });

        // interval for timer
        let IntervalTimer = 0;

        // if the training is already underway, then we continue the timer operation
        if(localStorage.getItem(`SpendWorkoutTime`) && localStorage.getItem(`SpendWorkoutTime`) != -1){
            IntervalTimer = setInterval(UpdateTime, 1000);
            localStorage.setItem(`SpendWorkoutTime`, time);
        }


        // Update value if timer
        function UpdateTime(){
            time++; // add one second for timer
            localStorage.setItem(`SpendWorkoutTime`, time); // set value of timer to localstorage
        }
    </script>
</body>
</html>