<?php
include "../templates/func.php";  // Include functions file
include "../templates/settings.php"; // Include settings file
?>
<!DOCTYPE html>
<html lang="en">
<?php inc_head(); // print head.php ?>
<body>
    <?php include "../templates/header.php"; //print header template ?>

	<main class="c-program-info">
		<div class="container">
			<h1 class="c-program-info__title">ДОБРО ПОЖАЛОВАТЬ В КОНСТРУКТОР ПРОГРАММЫ</h1>
			<h2 class="c-program-info__subtitle">здесь вы можете создавать свои собственные программы тренировок</h2>
			<div class="c-program-info__content">
				<ol>
					<li class="c-program-info__content-item">Создайте тренировку, добавляя упражнения из собственной или общей коллекции.</li>
					<li class="c-program-info__content-item">Вы можете добавить несколько тренировок в вашу программу. Для этого нажмите кпопку "Создать тренировку" на странице создания программы.</li>
					<li class="c-program-info__content-item">Выберите дни недели для проведения созданных тренировок, укажите длительность программы и дату начала. Вы можете не указывать точную длительность и завершить программу в любое удобное время.</li>
					<li class="c-program-info__content-item">Вы можете добавить программу своего друга и начать её прохождение.</li>
					<li class="c-program-info__content-item">Нажмите кнопку "Начать программу" для старта созданной программы. Продуктивных Вам тренировок!</li>
				</ol>
			</div>
		</div>
		<a class="button-text c-program-info__link" href="c_program.php"><p>СОЗДАТЬ ПРОГРАММУ</p> <img src="../img/arrow_white.svg" alt=""></a>
	</main>

    <?php include "../templates/footer.html"; //print footer template ?>
</body>
</html>