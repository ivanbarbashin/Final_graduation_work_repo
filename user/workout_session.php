<?php
include "../templates/func.php"; // Including functions file
include "../templates/settings.php"; // Including settings file

$weekday = date("N") - 1; // Getting the current day of the week as a number (1 for Monday, 7 for Sunday)
$user_data->set_program($conn); // Setting the program for the user based on the connection to the database
$workout_id = $user_data->program->program[$weekday]; // Getting the workout ID for the current weekday from the user's program
$workout = new Workout($conn, $workout_id, $weekday); // Creating a new Workout object based on the fetched workout ID and the weekday
$cnt_apps = 0; // count of approaches
?>
<!DOCTYPE html>
<html lang="en">
<?php inc_head(); ?>
<body class="workout-session">
    <header class="workout-session__header">
        <a href="../index.php" class="header__item header__item--logo header__item--logo--workout">
            <img src="../img/logo.svg" alt="">
            <p>Training</p>
        </a>
        <!-- Progress of test(in percents) -->
        <section class="workout-session__progress">
            <!-- Progress line and count of percent -->
            <h2 class="workout-session__progress-title">Прогресс</h2>
            <div class="workout-session__progress-percents">
                <p class="workout-session__percents-number">0%</p>
                <div class="workout-session__finish-line"></div>
            </div>
        </section>
    </header>
    
    <main class="session-exercises">
        <div class="container">
            <div class="session-exercises__info-cover">
                <section class="session-exercises__info">
                    <div class="session-exercises__help">
                        <p class="session-exercises__help-item session-exercises__help-item--green">время на подход</p>
                        <p class="session-exercises__help-item"> | </p>
                        <p class="session-exercises__help-item session-exercises__help-item--white">время на отдых</p>
                    </div>
                    <!-- Timer -->
                    <div class="workout-session__time">
                        00:00
                    </div>
                </section>
            </div>
            <swiper-container class="session-exercises__swiper" pagination="true" pagination-clickable="true" navigation="true" space-between="30" loop="true">
                <?php for ($i = 0; $i < $workout->loops; $i++) { foreach ($workout->exercises as $exercise){ // for each exercise, we output a slide ?>
                <swiper-slide class="session-exercises__slide">
                    <?php
                    $cnt_apps += $exercise->approaches; // increasing the number of approaches
                    $exercise->print_it($conn, false, false, false, true); // Printing the exercise details
                    ?>
                </swiper-slide>
                <?php } } ?>
            </swiper-container>
        </div>
    </main> 

    <footer class="workout-session-footer">
        <h1 class="workout-session-footer__title">Осталось:</h1>
        <h2 class="workout-session-footer__item"><span><?php echo count($workout->exercises); //number of exercises ?></span> упражнений(ия)</h2>
        <h2 class="workout-session-footer__item"><span><?php echo $cnt_apps; //number of approaches ?></span> подходов(a)</h2>
        <form method="post" action="end_workout.php">
            <input class="workout-session-footer__input" name="time" type="hidden" value="0">
            <button class="button-text workout-session-footer__button">Завершить</button>
        </form>
        
    </footer>
    
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-element-bundle.min.js"></script>
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


    
        // Timer variables
        let TimeForWork = localStorage.getItem('TimeForWork') * 60;
        let TimeForRest = localStorage.getItem('TimeForRest') * 60;
        let currentWorkTime = 0;
        let currentRestTime = 0;
        const timer = document.querySelector('.workout-session__time');
        let FinsishButton = document.querySelector('.workout-session-footer__button');
        let time = 0;

        let workoutSessionInputTime = document.querySelector('.workout-session-footer__input');

        // get data for timer from localStorage
        if(localStorage.getItem(`SpendWorkoutTime`) && localStorage.getItem(`SpendWorkoutTime`) != -1){
            time = localStorage.getItem(`SpendWorkoutTime`); //get time
            workoutSessionInputTime.value = time;

            let minutes = Math.floor(time / 60);
            let seconds = time % 60;
            if (seconds < 10){
                seconds = '0' + seconds;
            }
            if (minutes < 10){
                minutes = '0' + minutes;
            }
            timer.innerHTML = `${minutes}:${seconds}`; // set current time

            if (localStorage.getItem(`Period`) == 'Rest'){
                timer.style.cssText = 'background-color: rgb(1, 221, 34); box-shadow: 0px 3px 0px rgba(0, 94, 13, 1);'; // change styles if rest
            }
            else{
                timer.style.cssText = 'color: #ffffff; background-color: rgba(0, 94, 13, 1); box-shadow: 0px 3px 0px rgb(1, 221, 34);'; // change styles if work
            }
        }
        else{ // if localStorage does not contain variables
            localStorage.setItem(`SpendWorkoutTime`, 0);
            localStorage.setItem(`Period`, 'Work');
        }


        let IntervalTimer = setInterval(UpdateTime, 1000); // setting the interval for the timer

        workoutSessionInputTime.value = time; // set value for data base
        localStorage.setItem(`SpendWorkoutTime`, time);

        // finish button event listener
        FinsishButton.addEventListener('click', function(){ // if the finish button is pressed, then we stop the timer and change the locastorage variables
            localStorage.setItem("TimeForWork", -1);
            localStorage.setItem("TimeForRest", -1);
            localStorage.removeItem(`currentRepetsLeft`);
            localStorage.removeItem(`allRepetsNumber`);
            time = 0;
            localStorage.setItem(`SpendWorkoutTime`, -1);
            clearInterval(IntervalTimer);
        });

        
        // Update value of timer
        function UpdateTime(){
            time++; // add one second for timer
            workoutSessionInputTime.value = time; // set value for data base
            localStorage.setItem(`SpendWorkoutTime`, time); // set value of timer to localstorage

            if(TimeForWork >= 0 && TimeForRest >= 0){ // If time has come to rest
                if (time - (Math.floor(time / (TimeForWork + TimeForRest)) * (TimeForWork + TimeForRest)) == TimeForWork){
                    timer.style.cssText = 'background-color: rgb(1, 221, 34); box-shadow: 0px 3px 0px rgba(0, 94, 13, 1);';
                    localStorage.setItem(`Period`, 'Rest');
                }
                else if (time % (TimeForWork + TimeForRest) == 0){ // If time has come to work
                    timer.style.cssText = 'color: #ffffff; background-color: rgba(0, 94, 13, 1); box-shadow: 0px 3px 0px rgb(1, 221, 34);';
                    localStorage.setItem(`Period`, 'Work');
                }
            }
 
            let minutes = Math.floor(time / 60);
            let seconds = time % 60;
            if (seconds < 10){
                seconds = '0' + seconds;
            }
            if (minutes < 10){
                minutes = '0' + minutes;
            }

            timer.innerHTML = `${minutes}:${seconds}`; // set value(minutes and seconds) to visual block
            
        }



        // Progress of workout (variables)
        let repetDoneButtons = document.querySelectorAll('.exercise-item__done');
        let exercisesLeft = document.querySelectorAll('.workout-session-footer__item span')[0];
        let allrepetsLeft = document.querySelectorAll('.workout-session-footer__item span')[1];
        let currentRepetsLeft = document.querySelectorAll('.exercise-item__repetitions-title span');
        let exerciseTitle = document.querySelectorAll('.exercise-item__repetitions-title');

        let progressLine = document.querySelector('.workout-session__finish-line');
        let progressPercents = document.querySelector('.workout-session__percents-number');

        let allRepetsNumber = parseInt(allrepetsLeft.innerHTML);


        // set data of workout progress to blocks
        if(localStorage.getItem('currentRepetsLeft') && localStorage.getItem('allRepetsNumber')){
            let array = localStorage.getItem('currentRepetsLeft').split(',');
            let exercisesLeftNumber = 0;
            let doneRepetsNumber = parseInt(allrepetsLeft.innerHTML);

            // set repets of exercises
            for(let i = 0; i < array.length; i++){
                if(array[i] == '0'){
                    exerciseTitle[i].innerHTML = `сделанно`;
                    exercisesLeftNumber += 1;
                    repetDoneButtons[i].style.cssText = `display: none;`;
                }
                currentRepetsLeft[i].innerHTML = `${parseInt(array[i])}`;
                doneRepetsNumber -= parseInt(array[i]);
            }

            // set progress
            allRepetsNumber = parseInt(localStorage.getItem('allRepetsNumber'));
            
            allrepetsLeft.innerHTML = `${parseInt(allrepetsLeft.innerHTML) - doneRepetsNumber}`;
            exercisesLeft.innerHTML = `${parseInt(exercisesLeft.innerHTML) - exercisesLeftNumber}`;
            progressPercents.innerHTML = `${Math.round((allRepetsNumber - parseInt(allrepetsLeft.innerHTML)) / allRepetsNumber * 100)}%`;
            progressLine.style.cssText = `width:${Math.round((allRepetsNumber - parseInt(allrepetsLeft.innerHTML)) / allRepetsNumber * 100)}%`;
            if(progressPercents.innerHTML == '100%'){
                progressPercents.style.cssText = `color: #ffffff;`;
            }

        }
        else{
            // set data of repets to localstorage
            let array = [];
            for(let i = 0; i < currentRepetsLeft.length; i++){
                array.push(currentRepetsLeft[i].innerHTML);
            }
            localStorage.setItem('currentRepetsLeft', array);
            localStorage.setItem('allRepetsNumber', parseInt(allrepetsLeft.innerHTML));
        }

        // workout progress logic
        for(let i = 0; i < repetDoneButtons.length; i++){
            repetDoneButtons[i].addEventListener('click', function(){
                let Count = parseInt(currentRepetsLeft[i].innerHTML) - 1; // count of exercise repets

                if(Count == 0){ // if count == 0 then exercise if done
                    exerciseTitle[i].innerHTML = `сделанно`;
                    exercisesLeft.innerHTML = `${parseInt(exercisesLeft.innerHTML) - 1}`;
                    repetDoneButtons[i].style.cssText = `display: none;`;
                }

                // set progress to visual blocks
                currentRepetsLeft[i].innerHTML = `${parseInt(currentRepetsLeft[i].innerHTML) - 1}`;
                allrepetsLeft.innerHTML = `${parseInt(allrepetsLeft.innerHTML) - 1}`;
                progressPercents.innerHTML = `${Math.round((allRepetsNumber - parseInt(allrepetsLeft.innerHTML)) / allRepetsNumber * 100)}%`;
                progressLine.style.cssText = `width:${Math.round((allRepetsNumber - parseInt(allrepetsLeft.innerHTML)) / allRepetsNumber * 100)}%`;
                if(progressPercents.innerHTML == '100%'){
                    progressPercents.style.cssText = `color: #ffffff;`;
                }


                // set localstorage data
                let array = [];
                for(let i = 0; i < currentRepetsLeft.length; i++){
                    array.push(currentRepetsLeft[i].innerHTML);
                }
                localStorage.setItem('currentRepetsLeft', array); 

                if(parseInt(exercisesLeft.innerHTML) == 0){
                    clearInterval(IntervalTimer);
                    localStorage.setItem("TimeForWork", -1);
                    localStorage.setItem("TimeForRest", -1);
                    localStorage.setItem(`SpendWorkoutTime`, -1);
                    localStorage.removeItem(`currentRepetsLeft`);
                    localStorage.removeItem(`allRepetsNumber`);
                    FinsishButton.click();
                }
            });
            }


            // Exercise cards height
            let maximunExerciseCardHeight = 0;
            let exerciseCards = document.querySelectorAll('.exercise-item');
            for(let i = 0; i < exerciseCards.length; i++){
                maximunExerciseCardHeight = Math.max(maximunExerciseCardHeight, exerciseCards[i].clientHeight); // find maximum height of exercise card
            }

            for(let i = 0; i < exerciseCards.length; i++){
                exerciseCards[i].style.cssText = `height: ${maximunExerciseCardHeight}px;`; // set maximum height of exercise card
            }
    </script>
</body>
</html>