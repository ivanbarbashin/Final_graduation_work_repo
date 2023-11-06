<?php
include "../templates/func.php";
include "../templates/settings.php";

if (empty($_GET["id"]) || !is_numeric($_GET["id"]))
    header("Location: ../index.php");

$workout = new Control_Workout($conn, $_GET["id"]);
?>
<!DOCTYPE html>
<html lang="en">
<?php inc_head(); ?>
<body>
    <?php include "../templates/header.php" ?>
	<main class="user-comparison">
		<div class="container">
			<section class="last-control-cover">
				<p class="last-control__title">Контрольная тренировка <?php echo date("d.m.Y", $workout->date); ?></p>
                <section class="staff-block__header">
                    <?php $workout->print_control_exercises($conn); ?>
                </section>
			</section>
		</div>
	</main>

    <?php include "../templates/footer.html" ?>

    <script>
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