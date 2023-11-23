<?php
include "../templates/func.php"; // Include functions file
include "../templates/settings.php"; // Include settings file

if (empty($_GET["id"]) || !is_numeric($_GET["id"]))
    header("Location: coach.php"); // Redirect if the ID is empty or not numeric

$workout = new Control_Workout($conn, $_GET["id"]); // Creating a new Control_Workout object
$cnt_apps = 0; // Initializing a variable for counting approaches
?>
<!DOCTYPE html>
<html lang="en">
<?php inc_head(); // print head.php ?>
<form method="post" class="workout-session" action="end_control_workout.php">
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
        <swiper-container class="session-exercises__swiper" pagination="true" pagination-clickable="true" navigation="true" space-between="30" loop="true">
            <!-- for loop -->
            <?php foreach ($workout->exercises as $exercise){ //Loop through each exercise in the Control_Workout object ?>
                <swiper-slide class="session-exercises__slide">
                    <?php
                    $cnt_apps += $exercise->approaches; // Increment the count of approaches by the number of approaches for the current exercise
                    $exercise->print_control_exercise($conn, 0, 1); // Print details of the current exercise
                    ?>
                </swiper-slide>
            <?php } ?>
        </swiper-container>
    </div>
</main>

<footer class="workout-session-footer workout-session-footer--с">
    <div class="workout-session-footer-cover">
        <h1 class="workout-session-footer__title">Осталось:</h1>
        <h2 class="workout-session-footer__item"><span><?php echo count($workout->exercises); // exercises number left ?></span> упражнений(я / е)</h2>
    </div>
    <input type="hidden" name="id" value="<?php echo $_GET["id"];?>">
    <button type="submit" class="button-text workout-session-footer__button">Завершить</button>

</footer>

<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-element-bundle.min.js"></script>
<script>
    // Button to see exercise info
    let infoExerciseButton = document.querySelectorAll('.exercise-item__info-btn');
    let closeInfoExerciseButton = document.querySelectorAll('.exercise-item__info-close');
    let infoBlock = document.querySelectorAll('.exercise-item__info-content');
    let FinsishButton = document.querySelector('.workout-session-footer__button');

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


    //Difficult of exercises
    let difficultCountArr = document.querySelectorAll('.exercise-item__difficult-number');
    let difficultBlockArr = document.querySelectorAll('.exercise-item__difficult');

    for(let i = 0; i < difficultCountArr.length; i++){
        difficultBlockArr[i].innerHTML = '';
        for(let j = 0; j < 5; j++){ // create difficul circles
            let newElem = document.createElement('div');
            newElem.classList.add('exercise-item__difficult-item');
            if(j > Number(difficultCountArr[i].innerHTML) - 1){
                newElem.classList.add('exercise-item__difficult-item--disabled');
            }
            difficultBlockArr[i].appendChild(newElem);
        }
    }



    // Progress of workout
    let repetDoneInputs = document.querySelectorAll('.exercise-item__input');
    let exercisesLeft = document.querySelector('.workout-session-footer__item span');
    let progressLine = document.querySelector('.workout-session__finish-line');
    let progressPercents = document.querySelector('.workout-session__percents-number');

    currentArray = [];
    let currentInputValues = [];
    // get workout data from localstorage
    if(localStorage.getItem('repetDoneArray') && localStorage.getItem('repetDoneInputs')){
        let currentArray = localStorage.getItem('repetDoneArray').split(',');
        let currentInputValues = localStorage.getItem('repetDoneInputs').split(',');
        let completedCount = 0;
        let allCount = currentArray.length;

        for(let i = 0; i < currentArray.length; i++){
            if(currentArray[i] == '1'){
                completedCount += 1;
            }
        }

        for(let i = 0; i < repetDoneInputs.length; i++){
            repetDoneInputs[i].value = currentInputValues[i];
        }

        progressPercents.innerHTML = `${Math.round(completedCount / allCount * 100)}%`;
        progressLine.style.cssText = `width:${Math.round(completedCount / allCount * 100)}%`;
        exercisesLeft.innerHTML = allCount - completedCount;

        if(progressPercents.innerHTML == '100%'){
            progressPercents.style.cssText = `color: #ffffff;`;
        }
        else{
            progressPercents.style.cssText = `color: #000000;`;
        }
    }
    else{
        for(let i = 0; i < repetDoneInputs.length; i++){
            currentArray.push(0);
            currentInputValues.push('');
        }
        localStorage.setItem('repetDoneArray', currentArray);
        localStorage.setItem('repetDoneInputs', currentInputValues);
    }


    // workout logic when reperts' input edit
    for(let i = 0; i < repetDoneInputs.length; i++){
        repetDoneInputs[i].addEventListener('input', function(){ // if edit repets
            let completedCount = 0;
            let allCount = repetDoneInputs.length;
            for(let i = 0; i < repetDoneInputs.length; i++){
                if(repetDoneInputs[i].value != ''){
                    completedCount += 1;
                    currentArray[i] = 1;
                }
                else{
                    currentArray[i] = 0;
                }
                currentInputValues[i] = repetDoneInputs[i].value;
            }

            // set data to localstorage
            localStorage.setItem('repetDoneArray', currentArray);
            localStorage.setItem('repetDoneInputs', currentInputValues);

            // Edit progress of workout
            progressPercents.innerHTML = `${Math.round(completedCount / allCount * 100)}%`;
            progressLine.style.cssText = `width:${Math.round(completedCount / allCount * 100)}%`;
            exercisesLeft.innerHTML = allCount - completedCount;

            if(progressPercents.innerHTML == '100%'){
                progressPercents.style.cssText = `color: #ffffff;`;
            }
            else{
                progressPercents.style.cssText = `color: #000000;`;
            }
        });
    }


    // Button to finish workout
    FinsishButton.addEventListener('click', function(){
        localStorage.removeItem('repetDoneArray');
        localStorage.removeItem('repetDoneInputs');
    });


    // Exercises card height
    let maximunExerciseCardHeight = 0;
    let exerciseCards = document.querySelectorAll('.exercise-item');
    for(let i = 0; i < exerciseCards.length; i++){
        maximunExerciseCardHeight = Math.max(maximunExerciseCardHeight, exerciseCards[i].clientHeight);
    }

    for(let i = 0; i < exerciseCards.length; i++){
        exerciseCards[i].style.cssText = `height: ${maximunExerciseCardHeight}px;`;
    }
</script>
</form>
</html>