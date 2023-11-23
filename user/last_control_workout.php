<?php
include "../templates/func.php"; // Include functions file
include "../templates/settings.php"; // Include settings file
if (isset($_POST['featured'])) // Check if 'featured' field is sent to change the featured status of a workout
    $user_data->change_featured($conn, $_POST['featured']);

if ((empty($_GET["id"]) || !is_numeric($_GET["id"])) && empty($_POST['featured'])) // Validate the ID in the URL and check for the 'featured' field in POST; if not valid or no 'featured' field, redirect to the index page
    header("Location: ../index.php");

$workout = new Control_Workout($conn, $_GET["id"]); // Initialize a new Control_Workout object using the provided ID from the GET parameter
?>
<!DOCTYPE html>
<html lang="en">
<?php inc_head(); // print head.php ?>
<body>
    <?php include "../templates/header.php"; // print header template ?>
	<main class="user-comparison">
		<div class="container">
			<section class="last-control-cover">
				<p class="last-control__title">Контрольная тренировка <?php echo date("d.m.Y", $workout->date); // print date of control workout ?></p>
                <form method="post" class="staff-block__header">
                    <?php $workout->print_control_exercises($conn, $user_data); // print exercises list ?>
                </form>
			</section>
		</div>
	</main>

    <?php include "../templates/footer.html"; // print footer template ?>

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


		//Difficult of exercises
		let difficultCountArr = document.querySelectorAll('.exercise-item__difficult-number');
		let difficultBlockArr = document.querySelectorAll('.exercise-item__difficult');

		for(let i = 0; i < difficultCountArr.length; i++){
			difficultBlockArr[i].innerHTML = '';
            for(let j = 0; j < 5; j++){ // create difficult circles
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