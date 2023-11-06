<?php
include "../templates/func.php";
include "../templates/settings.php";
$user1 = NULL;
$user2 = NULL;
$sportsmen = $user_data->get_sportsmen();
$is_valid1 = isset($_GET["user1"]) && is_numeric($_GET["user1"]) && in_array($_GET["user1"], $sportsmen);
$is_valid2 = isset($_GET["user2"]) && is_numeric($_GET["user2"]) && in_array($_GET["user2"], $sportsmen);
if ($is_valid1)
    $user1 = new User($conn, $_GET["user1"]);

if ($is_valid2)
    $user2 = new User($conn, $_GET["user2"]);

$sportsmen_advanced = $user_data->get_sportsmen_advanced($conn);
$flag_main = false;
if ($is_valid1 && $is_valid2){
    $user1_workouts = $user1->get_control_workouts($conn, NULL, 1);
    $user2_workouts = $user2->get_control_workouts($conn, NULL, 1);
    if (count($user1_workouts) > 0 && count($user2_workouts) > 0){
        $last_1 = $user1_workouts[0]->exercises;
        $last_2 = $user2_workouts[0]->exercises;
        $flag_main = true;
        foreach ($last_1 as $item1){
            $flag = false;
            foreach ($last_2 as $item2){
                if ($item1->get_id() == $item2->get_id()){
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
                    <?php if ($user1 == NULL){ ?>
                        <section class="staff-block__header">
                            <button class="button-text comparison-block__add-button comparison-block__add-button--first"><p>Добавить спортсмена</p> <img src="../img/add.svg" alt=""></button>
                        </section>
                    <?php } else {
                        if ($is_valid2)
                            $reps = get_reps_for_comparison($user1, $conn, 1, $_GET["user2"]);
                        else
                            $reps = get_reps_for_comparison($user1, $conn, 1, NULL);
                        $reps["{{ exercises }}"] = '';
                        if ($flag_main){
                            foreach ($last_1 as $item){
                                $replaces = array(
                                    "{{ image }}" => $item->get_image($conn),
                                    "{{ name }}" => $item->name,
                                    "{{ rating }}" => $item->get_rating(),
                                    "{{ difficulty }}" => $item->difficulty,
                                    "{{ muscle }}" => '',
                                    "{{ description }}" => '',
                                    "{{ input }}" => $item->reps
                                );
                                $reps["{{ exercises }}"] .= render($replaces, "../templates/control_exercise.html");
                            }
                        }
                        echo render($reps, "../templates/comparison_block.html");
                    } ?>
			</section>
			<section class="comparison-block">
				<p class="staff-block__title">Второй спортсмен</p>
                    <?php if ($user2 == NULL){ ?>
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
                                $replaces = array(
                                    "{{ image }}" => $item->get_image($conn),
                                    "{{ name }}" => $item->name,
                                    "{{ rating }}" => $item->get_rating(),
                                    "{{ difficulty }}" => $item->difficulty,
                                    "{{ muscle }}" => '',
                                    "{{ description }}" => '',
                                    "{{ input }}" => $item->reps
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
                    <?php foreach ($sportsmen_advanced as $sportsman){ ?>
                    <div class="popup-exercise--add-users__item">
                        <input class="popup-exercise--add-users__input" type="radio" id="users-list1-<?php echo $sportsman->get_id(); ?>" name="user1" value="<?php echo $sportsman->get_id(); ?>"/>
                        <label class="popup-exercise--add-users__label" for="users-list1-<?php echo $sportsman->get_id(); ?>"><?php echo $sportsman->name. " " . $sportsman->surname; ?></label>
                    </div>
                    <?php }
                    if ($is_valid2){ ?>
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
        let FirstUserAddPopup = document.querySelector('.popup-exercise--user-first');
        let SecondUserAddPopup = document.querySelector('.popup-exercise--user-second');

        let userAddButtonFirst = document.querySelector('.comparison-block__add-button--first');
        let userAddButtonSecond = document.querySelector('.comparison-block__add-button--second');

        if(userAddButtonFirst){
            userAddButtonFirst.addEventListener('click', function(){
                FirstUserAddPopup.classList.add("open");
            });
        }
        if(userAddButtonSecond){
            userAddButtonSecond.addEventListener('click', function(){
                SecondUserAddPopup.classList.add("open");
            });
        }
        

        const closeBtn = document.querySelectorAll('.popup-exercise__close-button');
		for(let i = 0; i < closeBtn.length; i++){
			closeBtn[i].addEventListener('click', function(){
				FirstUserAddPopup.classList.remove("open");
                SecondUserAddPopup.classList.remove("open");
			});
		}

		window.addEventListener('keydown', (e) => {
            if(e.key == "Escape"){
                FirstUserAddPopup.classList.remove("open");
                SecondUserAddPopup.classList.remove("open");
            }
		});

		document.querySelector('.popup-exercise__content').addEventListener('click', event => {
			event.isClickWithInModal = true;
		});


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