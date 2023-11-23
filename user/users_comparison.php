<?php
include "../templates/func.php"; // Include functions file
include "../templates/settings.php"; // Include settings file

$user1 = NULL; // Initialize user1 as NULL
$user2 = NULL; // Initialize user2 as NULL

$sportsmen = $user_data->get_sportsmen(); // get a list of sportsmen

// Validate if user1 and user2 parameters are provided in the URL and are valid numeric IDs belonging to sportsmen
$is_valid1 = isset($_GET["user1"]) && is_numeric($_GET["user1"]) && in_array($_GET["user1"], $sportsmen);
$is_valid2 = isset($_GET["user2"]) && is_numeric($_GET["user2"]) && in_array($_GET["user2"], $sportsmen);
if ($is_valid1) // If user1 ID is valid, create a User object for user1
    $user1 = new User($conn, $_GET["user1"]);
if ($is_valid2) // If user2 ID is valid, create a User object for user2
    $user2 = new User($conn, $_GET["user2"]);

$sportsmen_advanced = $user_data->get_sportsmen_advanced($conn); // get advanced sportsmen data
$flag_main = false;

// If both user1 and user2 IDs are valid, proceed with comparison
if ($is_valid1 && $is_valid2){
    // get control workouts for user1 and user2
    $user1_workouts = $user1->get_control_workouts($conn, NULL, 1);
    $user2_workouts = $user2->get_control_workouts($conn, NULL, 1);
    if (count($user1_workouts) > 0 && count($user2_workouts) > 0){ // If both users have control workouts
        // get last exercises for user1 and user2
        $last_1 = $user1_workouts[0]->exercises;
        $last_2 = $user2_workouts[0]->exercises;
        $flag_main = true;
        foreach ($last_1 as $item1){ // Compare exercises of user1 and user2
            $flag = false;
            foreach ($last_2 as $item2){
                if ($item1->get_id() == $item2->get_id()){ // if exercises are the same
                    $flag = true;
                    break;
                }
            }
            if (!$flag){
                $flag_main = false;
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

	<main class="user-comparison">
		<div class="container">
			<section class="comparison-block">
				<p class="staff-block__title">Первый спортсмен</p>
                    <?php if ($user1 == NULL){ // if the user is not selected ?>
                        <section class="staff-block__header">
                            <button class="button-text comparison-block__add-button comparison-block__add-button--first"><p>Добавить спортсмена</p> <img src="../img/add.svg" alt=""></button>
                        </section>
                    <?php } else {
                        if ($is_valid2) // If user1 is valid
                            $reps = get_reps_for_comparison($user1, $conn, 1, $_GET["user2"]); // Get workout data for comparison between user1 and user2 if user2 is valid
                        else
                            $reps = get_reps_for_comparison($user1, $conn, 1, NULL); // Get workout data for comparison for user1 alone if user2 is not valid
                        $reps["{{ exercises }}"] = ''; // Initialize exercise data
                        if ($flag_main){ // If flag_main is true (indicating similarity in last exercises between users)
                            foreach ($last_1 as $item){
                                $count_exercise_reps = 0;
                                if ($item->description == '') // if exercise description is none
                                    $description = "Нет описания";
                                else
                                    $description = $item->description;
                                if ($item->reps == '')
                                    $count_exercise_reps  = "Нет данных";
                                else
                                    $count_exercise_reps = $item->reps;
                                $muscle_list = "";
                                foreach ($item->muscles as $muscle){ // Generate muscle list
                                    $muscle_list .= translate_group($muscle) . " ";
                                }
                                $muscle_list = str_replace(' ', '-', trim($muscle_list));

                                 // Replace placeholders with exercise information
                                $replaces = array(
                                    "{{ image }}" => $item->get_image($conn),
                                    "{{ name }}" => $item->name,
                                    "{{ rating }}" => $item->get_rating(),
                                    "{{ difficulty }}" => $item->difficulty,
                                    "{{ muscle }}" => $muscle_list,
                                    "{{ description }}" => $description,
                                    "{{ button_featured }}" => '',
                                    "{{ input }}" => '<div class="exercise-item__repetitions">'. $count_exercise_reps . '</div>'
                                );
                                $reps["{{ exercises }}"] .= render($replaces, "../templates/control_exercise.html"); // Render exercise details and concatenate to existing exercises data
                            }
                        }
                        echo render($reps, "../templates/comparison_block.html");  // Render the comparison block with exercise details
                    } ?>
			</section>
			<section class="comparison-block">
				<p class="staff-block__title">Второй спортсмен</p>
                    <?php if ($user2 == NULL){ // if the user is not selected ?>
                        <section class="staff-block__header">
                            <button class="button-text comparison-block__add-button comparison-block__add-button--second"><p>Добавить спортсмена</p> <img src="../img/add.svg" alt=""></button>
                        </section>
                    <?php } else {
                        if ($is_valid1)
                            $reps = get_reps_for_comparison($user2, $conn, 2, $_GET["user1"]);
                        else
                            $reps = get_reps_for_comparison($user2, $conn, 2, NULL);
                        $reps["{{ exercises }}"] = '';
                        if ($flag_main){
                            foreach ($last_2 as $item){
                                $count_exercise_reps = 0;
                                if ($item->description == '')
                                    $description = "Нет описания";
                                else
                                    $description = $item->description;
                                if ($item->reps == '')
                                    $count_exercise_reps  = "Нет данных";
                                else
                                    $count_exercise_reps = $item->reps;
                                $muscle_list = "";
                                foreach ($item->muscles as $muscle){
                                    $muscle_list .= translate_group($muscle) . " ";
                                }
                                $muscle_list = str_replace(' ', '-', trim($muscle_list));
                                $replaces = array(
                                    "{{ image }}" => $item->get_image($conn),
                                    "{{ name }}" => $item->name,
                                    "{{ rating }}" => $item->get_rating(),
                                    "{{ difficulty }}" => $item->difficulty,
                                    "{{ muscle }}" => $muscle_list,
                                    "{{ description }}" => $description,
                                    "{{ button_featured }}" => '',
                                    "{{ input }}" => '<div class="exercise-item__repetitions">'. $count_exercise_reps . '</div>'
                                );
                                $reps["{{ exercises }}"] .= render($replaces, "../templates/control_exercise.html");
                            }
                        }
                        echo render($reps, "../templates/comparison_block.html");
                    } ?>
			</section>
		</div>

        <section class="popup-exercise popup-exercise--user-first">
			<form class="popup-exercise__content popup-exercise--add-users__form">
				<button type="button" type="button" class="popup-exercise__close-button"><img src="../img/close.svg" alt=""></button>
                    <?php foreach ($sportsmen_advanced as $sportsman){ // Iterates through each sportsman in $sportsmen_advanced?>
                    <div class="popup-exercise--add-users__item">
                        <input class="popup-exercise--add-users__input" type="radio" id="users-list1-<?php echo $sportsman->get_id(); ?>" name="user1" value="<?php echo $sportsman->get_id(); ?>"/>
                        <label class="popup-exercise--add-users__label" for="users-list1-<?php echo $sportsman->get_id(); ?>"><?php echo $sportsman->name. " " . $sportsman->surname; ?></label>
                    </div>
                    <?php }
                    if ($is_valid2){ //If $user2 is valid, add a hidden input with user2's ID ?>
                        <input type="hidden" name="user2" value="<?php echo $user2->get_id(); ?>">
                    <?php } ?>
				<button type="submit" class="button-text popup-exercise--add-users__button-add" type="submit"><p>Добавить</p><img src="../img/add.svg" alt=""></button>
			</form>
		</section>

        <section class="popup-exercise popup-exercise--user-second">
            <form class="popup-exercise__content popup-exercise--add-users__form">
				<button type="button" type="button" class="popup-exercise__close-button"><img src="../img/close.svg" alt=""></button>
                <?php if ($is_valid1){ ?>
                    <input type="hidden" name="user1" value="<?php echo $user1->get_id(); ?>">
                <?php }
                foreach ($sportsmen_advanced as $sportsman){ ?>
                <div class="popup-exercise--add-users__item">
					<input class="popup-exercise--add-users__input" type="radio" id="users-list2-<?php echo $sportsman->get_id(); ?>" name="user2" value="<?php echo $sportsman->get_id(); ?>"/>
					<label class="popup-exercise--add-users__label" for="users-list2-<?php echo $sportsman->get_id(); ?>"><?php echo $sportsman->name. " " . $sportsman->surname; ?></label>
				</div>
                <?php } ?>
				<button type="submit" class="button-text popup-exercise--add-users__button-add" type="submit"><p>Добавить</p><img src="../img/add.svg" alt=""></button>
			</form> 
		</section>
	</main>

    <?php include "../templates/footer.html" ?>

    <script>
        // blocks of comarison
        let FirstUserAddPopup = document.querySelector('.popup-exercise--user-first');
        let SecondUserAddPopup = document.querySelector('.popup-exercise--user-second');

        let userAddButtonFirst = document.querySelector('.comparison-block__add-button--first');
        let userAddButtonSecond = document.querySelector('.comparison-block__add-button--second');

        // event listeners for add buttons(if click open popup windows)
        if(userAddButtonFirst){
            userAddButtonFirst.addEventListener('click', function(){ // open first popup window (list of users)
                FirstUserAddPopup.classList.add("open");
            });
        }
        if(userAddButtonSecond){
            userAddButtonSecond.addEventListener('click', function(){ // open second popup window (list of users)
                SecondUserAddPopup.classList.add("open");
            });
        }
        

        // close popup windows
        const closeBtn = document.querySelectorAll('.popup-exercise__close-button');
		for(let i = 0; i < closeBtn.length; i++){
			closeBtn[i].addEventListener('click', function(){ // close popup windows
				FirstUserAddPopup.classList.remove("open");
                SecondUserAddPopup.classList.remove("open");
			});
		}

		window.addEventListener('keydown', (e) => { // close popup windows
            if(e.key == "Escape"){
                FirstUserAddPopup.classList.remove("open");
                SecondUserAddPopup.classList.remove("open");
            }
		});


        // Button to see exercise info
        let infoExerciseButton = document.querySelectorAll('.exercise-item__info-btn');
        let closeInfoExerciseButton = document.querySelectorAll('.exercise-item__info-close');
        let infoBlock = document.querySelectorAll('.exercise-item__info-content');

        for(let i = 0; i < infoExerciseButton.length; i++){
            infoExerciseButton[i].addEventListener('click', function(){ // show exercise info
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
    </script>
</body>
</html>