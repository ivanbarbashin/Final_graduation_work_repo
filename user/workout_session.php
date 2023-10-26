<?php
include "../templates/func.php";
include "../templates/settings.php";

$weekday = date("N") - 1;
$user_data->set_program($conn);
$workout_id = $user_data->program->program[$weekday];
$workout = new Workout($conn, $workout_id, $weekday);
$cnt_apps = 0;
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
            <h2 class="workout-session__progress-title">Progress</h2>
            <div class="workout-session__progress-percents">
                <p class="workout-session__percents-number">0%</p>
                <div class="workout-session__finish-line"></div>
            </div>
        </section>
    </header>
    
    <main class="session-exercises">
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
                <!-- Navigation of test -->
                <nav class="workout-session__navigation">

                </nav>
            </section>
        </div>
        <swiper-container class="session-exercises__swiper" navigation="true">
            <!-- for loop -->
            <?php for ($i = 0; $i < $workout->loops; $i++) { foreach ($workout->exercises as $exercise){?>
            <swiper-slide class="session-exercises__slide">
                <?php
                $cnt_apps += $exercise->approaches;
                $exercise->print_it($conn);
                # echo render($replaces, "../templates/user_exercise.html");
                ?>
            </swiper-slide>
            <?php } } ?>
        </swiper-container>
    </main>

    <footer class="workout-session-footer">
        <h1 class="workout-session-footer__title">Осталось:</h1>
        <h2 class="workout-session-footer__item"><span><?php echo count($workout->exercises); ?></span> упражнений</h2>
        <h2 class="workout-session-footer__item"><span><?php echo $cnt_apps; ?></span> подходов</h2>
        <a href="end_workout.php" class="button-text workout-session-footer__button">Завершить</a>
    </footer>
    
    <script src="https://cdn.jsdelivr.net/npm/swiper@10/swiper-element-bundle.min.js"></script>
    <script>
        // Navigation
        let questionsArr = document.querySelectorAll('.exercise-item');

        
        const testNavigation = document.querySelector('.workout-session__navigation');
        const progressBar = document.querySelector('.workout-session__finish-line');
        let percents = document.querySelector('.workout-session__percents-number');

        for(let i = 0; i < questionsArr.length; i++){
            let newElem = document.createElement('button');
            newElem.classList.add('button-text');
            newElem.classList.add('workout-session__navigation-button');
            newElem.innerHTML = `${i+1}`;
            testNavigation.appendChild(newElem);
        }

        const navigationButtons = document.querySelectorAll('.workout-session__navigation-button');

        

        // Button to see exercise info
        let infoExerciseButton = document.querySelectorAll('.exercise-item__info-btn');
        let closeInfoExerciseButton = document.querySelectorAll('.exercise-item__info-close');
        let infoBlock = document.querySelectorAll('.exercise-item__info-content');

        for(let i = 0; i < infoExerciseButton.length; i++){
            infoExerciseButton[i].addEventListener('click', function(){
                infoBlock[i].style.cssText = `top: -1%;`;
            });
        }
        for(let i = 0; i < closeInfoExerciseButton.length; i++){
            closeInfoExerciseButton[i].addEventListener('click', function(){
                infoBlock[i].style.cssText = `top: -101%;`;
            });
        }



        //Difficult
		let difficultCountArr = document.querySelectorAll('.exercise-item__difficult-number');
		let difficultBlockArr = document.querySelectorAll('.exercise-item__difficult');

		for(let i = 0; i < difficultCountArr.length; i++){
			difficultBlockArr[i].innerHTML = '';
			console.log(Number(difficultCountArr[i].innerHTML) - 1)
            for(let j = 0; j < 5; j++){
				let newElem = document.createElement('div');
				newElem.classList.add('exercise-item__difficult-item');
				if(j > Number(difficultCountArr[i].innerHTML) - 1){
					newElem.classList.add('exercise-item__difficult-item--disabled');
				}
				difficultBlockArr[i].appendChild(newElem);
			}
        }

    </script>
</body>
</html>