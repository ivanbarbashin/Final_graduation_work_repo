<?php
include "../templates/func.php";
include "../templates/settings.php";
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
				<section class="staff-block__header">
					<button class="button-text comparison-block__add-button"><p>Добавить спортсмена</p> <img src="../img/add.svg" alt=""></button>
				</section>
			</section>
			<section class="comparison-block">
				<p class="staff-block__title">Второй спортсмен</p>
				<section class="staff-block__header">
					<img class="staff-block__avatar" src="../img/man_avatar.svg" alt="">
					<section class="staff-block__info">
						<div class="staff-block__name">
							<h1 class="staff-block__name-text">Иван Иванов</h1>
							<a class="staff-block__profile-link" href=""><img src="../img/profile_black.svg" alt=""></a>
						</div>
						<div class="staff-block__buttons">
							<a href="" class="staff-block__button staff-block__button--img"><img src="../img/vk.svg" alt=""></a>
							<a href="../img/tg.svg" class="staff-block__button staff-block__button--img"><img src="../img/tg.svg" alt=""></a>
							<button class="button-text staff-block__button staff-block__button--delite"><p>Удалить</p> <img src="../img/delete.svg" alt=""></button>
						</div>
					</section>
				</section>
				<div class="staff-block__line"></div>
				<section class="comparison-block__physics">
					<h2 class="staff-block__subtitle">Физические данные</h2>
					<div class="comparison-block__physics-content">
						<div class="comparison-block__physics-item">
							<p class="comparison-block__physics-name">Вес</p>
							<div class="comparison-block__physics-number">70 кг</div>
						</div>
						<div class="comparison-block__physics-item">
							<p class="comparison-block__physics-name">Рост</p>
							<div class="comparison-block__physics-number">180 см</div>
						</div>
					</div>
				</section>
				<div class="staff-block__line"></div>
				<section class="comparison-block__exercises">
					<h2 class="staff-block__subtitle">Контрольная тренировка</h2>
					<section class="exercise-item exercise-item--workout">
						<!-- Exercise info button -->
						<button type="button"  class="exercise-item__info-btn"><img src="../img/info.svg" alt=""></button>
						<!-- Info text -->
						<div class="exercise-item__info-content">
							<button type="button" class="exercise-item__info-close"><img src="../img/close.svg" alt=""></button>
							<p class="exercise-item__info-text">{{ description }}</p>
						</div>
						<!-- Exercise muscle groups -->
						<div class="exercise-item__muscle-groups">{{ muscle }}</div>
						<!-- Exercise image -->
						<img class="exercise-item__img" src="{{ image }}" alt="">
						<!-- Decoration line -->
						<div class="exercise-item__line"></div>
						<!-- Exercise title -->
						<h1 class="exercise-item__title">{{ name }}</h1>
						<!-- Rating and difficult -->
						<div class="exercise-item__statistic">
							<div class="exercise-item__rating">
								<p class="exercise-item__score">{{ rating }}</p>
								<img class="exercise-item__star" src="../img/Star.svg" alt="">
							</div>
							<div class="exercise-item__difficult">
								<p class="exercise-item__difficult-number">{{ difficulty }}</p>
								<div class="exercise-item__difficult-item"></div>
							</div>
						</div>
						<!-- Count of repetitions -->
						<div class="exercise-item__progress">
							<div class="exercise-item__repetitions">
								<p class="exercise-item__repetitions-title">22 x 33</p>
							</div>
							<p class="exercise-item__progress-number exercise-item__progress-number--green">+ 30%</p>
						</div>
						
					</section>
				</section>
			</section>
		</div>
	</main>

    <?php include "../templates/footer.html" ?>
</body>
</html>