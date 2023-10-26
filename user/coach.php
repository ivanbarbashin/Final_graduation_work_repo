<?php
include "../templates/func.php";
include "../templates/settings.php";
?>
<!DOCTYPE html>
<html lang="en">
<?php inc_head(); ?>
<body>
    <?php include "../templates/header.php" ?>

	<main class="staff-cover">
		<div class="container">
			<section class="staff-block">
				<p class="staff-block__title">Спортсмен</p>
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
				<section class="staff-block__item">
					<h2 class="staff-block__subtitle">Ближайшая тренировка</h2>
					<div class="staff-block__nearest-workout-content">
						<div class="staff-block__nearest-workout-date">12.12.2023</div>
						<a href="" class="staff-block__button-more"><p>Подробнее</p> <img src="../img/more_white.svg" alt=""></a>
					</div>
				</section>
				<div class="staff-block__line"></div>
				<section class="staff-block__item">
					<h2 class="staff-block__subtitle">Цели</h2>
					<ul class="staff-block__goals-list">
						<li class="staff-block__goals-item">
							<div class="staff-block__goals-item-cover">
								<div class="staff-block__goal-name">
									<p class="staff-block__goal-text">Атжуания 20.5 раз</p> <img src="../img/green_check_mark.svg" alt="">
								</div>
								<div class="staff-block__goal-buttons">
									<button class="staff-block__goal-button staff-block__goal-button--text">Не выполненна</button>
									<button class="button-img staff-block__item-button staff-block__item-button--goal-edit"><img src="../img/edit.svg" alt=""></button>
									<button class="button-img staff-block__item-button"><img src="../img/delete.svg" alt=""></button>
								</div>
							</div>
						</li>
						<li class="staff-block__goals-item">
							<div class="staff-block__goals-item-cover">
								<div class="staff-block__goal-name">
									<p class="staff-block__goal-text">Атжуания 20.5 раз</p> <img src="../img/blue_question_mark.svg" alt="">
								</div>
								<div class="staff-block__goal-buttons">
									<button class="staff-block__goal-button staff-block__goal-button--text">Выполненна</button>
									<button class="button-img staff-block__item-button staff-block__item-button--goal-edit"><img src="../img/edit.svg" alt=""></button>
									<button class="button-img staff-block__item-button"><img src="../img/delete.svg" alt=""></button>
								</div>
							</div>
						</li>
					</ul>
					<button class="button-text staff-block__item-button--add staff-block__item-button--goal-add"><p>Добавить</p><img src="../img/add.svg" alt=""></button>
				</section>
				<div class="staff-block__line"></div>
				<section class="staff-block__item">
					<h2 class="staff-block__subtitle">Контрольные тренировки</h2>
					<div class="staff-block__control-workout-nearest">
						<div class="staff-block__control-workout-info">
							<p class="staff-block__control-workout-text">Ближайшая:</p>
							<div class="staff-block__control-workouts-date">12.12.2023</div>
						</div>
						<a href="control_workouts.php" class="staff-block__button-more"><p>Подробнее</p> <img src="../img/more_white.svg" alt=""></a>
					</div>
				</section>
				<div class="staff-block__line"></div>
				<section class="staff-block__item">
					<h2 class="staff-block__subtitle">Турниры и соревнования</h2>
					<div class="staff-block__competitions">
						<div class="staff-block__competition-item">
							<p class="staff-block__competition-text">Игра с балбесами</p>
							<div class="staff-block__item-buttons">
								<a class="staff-block__link-button staff-block__link-button--competitions-file" href="" download><img src="../img/file.svg" alt=""></a>
								<a class="staff-block__link-button staff-block__link-button--competitions-link" href=""><img src="../img/link.svg" alt=""></a>
								<button class="button-img staff-block__item-button staff-block__item-button--competition-edit"><img src="../img/edit.svg" alt=""></button>
								<button class="button-img staff-block__item-button"><img src="../img/delete.svg" alt=""></button>
							</div>
						</div>
						<div class="staff-block__competition-item">
							<p class="staff-block__competition-text">Игра с балбесами</p>
							<div class="staff-block__item-buttons">
								<a class="staff-block__link-button staff-block__link-button--competitions-file" href="" download><img src="../img/file.svg" alt=""></a>
								<a class="staff-block__link-button staff-block__link-button--competitions-link" href=""><img src="../img/link.svg" alt=""></a>
								<button class="button-img staff-block__item-button staff-block__item-button--competition-edit"><img src="../img/edit.svg" alt=""></button>
								<button class="button-img staff-block__item-button"><img src="../img/delete.svg" alt=""></button>
							</div>
						</div>
						<button class="button-text staff-block__item-button--add staff-block__item-button--competition-add"><p>Добавить</p><img src="../img/add.svg" alt=""></button>
					</div>
				</section>
				<div class="staff-block__line"></div>
				<section class="staff-block__item">
					<h2 class="staff-block__subtitle">Полезная информация</h2>
					<div class="staff-block__useful-links">
						<div class="staff-block__useful-links-item">
							<p class="staff-block__useful-links-text">Атжумания</p>
							<div class="staff-block__item-buttons">
								<a class="staff-block__link-button staff-block__link-button--info-file" href="" download><img src="../img/file.svg" alt=""></a>
								<a class="staff-block__link-button staff-block__link-button--info-link" href=""><img src="../img/link.svg" alt=""></a>
								<button class="button-img staff-block__item-button staff-block__item-button--link-edit"><img src="../img/edit.svg" alt=""></button>
								<button class="button-img staff-block__item-button"><img src="../img/delete.svg" alt=""></button>
							</div>
						</div>
						<div class="staff-block__useful-links-item">
							<p class="staff-block__useful-links-text">Атжумания</p>
							<div class="staff-block__item-buttons">
								<a class="staff-block__link-button staff-block__link-button--info-file" href="" download><img src="../img/file.svg" alt=""></a>
								<a class="staff-block__link-button staff-block__link-button--info-link" href=""><img src="../img/link.svg" alt=""></a>
								<button class="button-img staff-block__item-button staff-block__item-button--link-edit"><img src="../img/edit.svg" alt=""></button>
								<button class="button-img staff-block__item-button"><img src="../img/delete.svg" alt=""></button>
							</div>
						</div>
						<button class="button-text staff-block__item-button--add staff-block__item-button--link-add"><p>Добавить</p><img src="../img/add.svg" alt=""></button>
					</div>
				</section>
			</section>
			<section class="coach-other">
				<section class="last-trainings last-trainings--coach">
					<h1 class="last-trainings__title">Последние тренировки</h1>
					<div class="last-trainings__content">
						<!-- Item -->
						<section class="last-trainings__card">
							<!-- Left part of last exercise item -->
							<div class="last-trainings__card-left">
								<!-- Time of training -->
								<div class="last-trainings__item">
								<img class="last-trainings__item-img" src="../img/time.svg" alt="">
								<p class="last-trainings__item-text"><span>12</span> мин</p>
								</div>
								<!-- Exercise count of training -->
								<div class="last-trainings__item">
								<img class="last-trainings__item-img" src="../img/cards.svg" alt="">
								<p class="last-trainings__item-text"><span>12</span> упражнений</p>
								</div>
							</div>
							<!-- Right part of last exercise item -->
							<div class="last-trainings__card-right">
								<!-- Muscle groups count of training -->
								<div class="last-trainings__item">
								<img class="last-trainings__item-img" src="../img/cards.svg" alt="">
								<p class="last-trainings__item-text"><span>12</span> группы мышц</p>
								</div>
								<!-- Button 'Подробнее' for more info about exercise -->
								<div class="last-trainings__item">
								<a class="button-text last-trainings__item-button" href="{{ link }}">Подробнее <img src="../img/other.svg" alt=""></a>
								</div>
							</div>
						</section>
						<!-- Item -->
						<section class="last-trainings__card">
							<!-- Left part of last exercise item -->
							<div class="last-trainings__card-left">
								<!-- Time of training -->
								<div class="last-trainings__item">
								<img class="last-trainings__item-img" src="../img/time.svg" alt="">
								<p class="last-trainings__item-text"><span>12</span> мин</p>
								</div>
								<!-- Exercise count of training -->
								<div class="last-trainings__item">
								<img class="last-trainings__item-img" src="../img/cards.svg" alt="">
								<p class="last-trainings__item-text"><span>12</span> упражнений</p>
								</div>
							</div>
							<!-- Right part of last exercise item -->
							<div class="last-trainings__card-right">
								<!-- Muscle groups count of training -->
								<div class="last-trainings__item">
								<img class="last-trainings__item-img" src="../img/cards.svg" alt="">
								<p class="last-trainings__item-text"><span>12</span> группы мышц</p>
								</div>
								<!-- Button 'Подробнее' for more info about exercise -->
								<div class="last-trainings__item">
								<a class="button-text last-trainings__item-button" href="{{ link }}">Подробнее <img src="../img/other.svg" alt=""></a>
								</div>
							</div>
						</section>
					</div>
				</section>
				<section class="friends-block">
                    <!-- Title and button to search friends -->
                    <div class="friends-block__header">
                        <h1 class="friends-block__header-title">Другие спортсмены</h1>
                        <a class="friends-block__header-button" href=""><img src="../img/search.svg" alt=""></a>
                    </div>
                    <!-- Friends' workout swiper -->
                   <section class="friends-block__cover">
						<a href="../user/profile.php?user={{ id }}" class="friends-block__item">
							<img class="friends-block__avatar" src="../img/man_avatar.svg" alt="">
							<p class="friends-block__name">Иван Иванов</p>
						</a>
						<a href="../user/profile.php?user={{ id }}" class="friends-block__item">
							<img class="friends-block__avatar" src="../img/man_avatar.svg" alt="">
							<p class="friends-block__name">Иван Иванов</p>
						</a>
						<a href="../user/profile.php?user={{ id }}" class="friends-block__item">
							<img class="friends-block__avatar" src="../img/man_avatar.svg" alt="">
							<p class="friends-block__name">Иван Иванов</p>
						</a>
						<a href="../user/profile.php?user={{ id }}" class="friends-block__item">
							<img class="friends-block__avatar" src="../img/man_avatar.svg" alt="">
							<p class="friends-block__name">Иван Иванов</p>
						</a>
					</section>
			</section>
			<section class="staff-other__buttons">
				<a href="" class="button-text staff-other__button"><p>Программа</p> <img src="../img/my_programm.svg" alt=""></a>
				<a href="users_comparison.php" class="button-text staff-other__button"><p>Сравнить спортсменов</p> <img src="../img/my_programm.svg" alt=""></a>
				<a href="" class="button-text staff-other__button"><p>Группы</p> <img src="../img/my_programm.svg" alt=""></a>
			</section>
		</div>


		<!-- Goals edit -->
		<section class="popup-exercise popup-exercise--goals-edit">
			<form method="post" class="popup-exercise__content">
				<button type="button" class="popup-exercise__close-button"><img src="../img/close.svg" alt=""></button>
				<input class="popup-exercise__input-item goals-edit__name" type="text" placeholder="название цели">
				<button class="button-text popup-exercise__submit-button">Сохранить</button>
			</form>
		</section>

		<!-- Goals add -->
		<section class="popup-exercise popup-exercise--goals-add">
			<form method="post" class="popup-exercise__content">
				<button type="button" class="popup-exercise__close-button"><img src="../img/close.svg" alt=""></button>
				<input class="popup-exercise__input-item goals-add__name" type="text" placeholder="название цели">
				<button class="button-text popup-exercise__submit-button">Добавить</button>
			</form>
		</section>

		<!-- Competitions edit -->
		<section class="popup-exercise popup-exercise--competitions-edit">
			<form method="post" class="popup-exercise__content">
				<button type="button" class="popup-exercise__close-button"><img src="../img/close.svg" alt=""></button>
				<input class="popup-exercise__input-item competitions-edit__name" type="text" placeholder="название соревнования">
				<input class="popup-exercise__input-item popup-exercise__input-item--file competitions-edit__file" type="file">
				<input class="popup-exercise__input-item competitions-edit__link" type="text" placeholder="вставьте ссылку">
				<button class="button-text popup-exercise__submit-button">Сохранить</button>
			</form>
		</section>

		<!-- Competitions add-->
		<section class="popup-exercise popup-exercise--competitions-add">
			<form method="post" class="popup-exercise__content">
				<button type="button" class="popup-exercise__close-button"><img src="../img/close.svg" alt=""></button>
				<input class="popup-exercise__input-item competitions-add__name" type="text" placeholder="название соревнования">
				<input class="popup-exercise__input-item popup-exercise__input-item--file competitions-add__file" type="file">
				<input class="popup-exercise__input-item competitions-add__link" type="text" placeholder="вставьте ссылку">
				<button class="button-text popup-exercise__submit-button">Добавить</button>
			</form>
		</section>

		<!-- Useful links edit-->
		<section class="popup-exercise popup-exercise--links-edit">
			<form method="post" class="popup-exercise__content">
				<button type="button" class="popup-exercise__close-button"><img src="../img/close.svg" alt=""></button>
				<input class="popup-exercise__input-item links-edit__name" type="text" placeholder="название">
				<input class="popup-exercise__input-item popup-exercise__input-item--file links-edit__file" type="file">
				<input class="popup-exercise__input-item links-edit__link" type="text" placeholder="вставьте ссылку">
				<button class="button-text popup-exercise__submit-button">Сохранить</button>
			</form>
		</section>

		<!-- Useful links add-->
		<section class="popup-exercise popup-exercise--links-add">
			<form method="post" class="popup-exercise__content">
				<button type="button" class="popup-exercise__close-button"><img src="../img/close.svg" alt=""></button>
				<input class="popup-exercise__input-item links-add__name" type="text" placeholder="название">
				<input class="popup-exercise__input-item popup-exercise__input-item--file links-add__file" type="file">
				<input class="popup-exercise__input-item links-add__link" type="text" placeholder="вставьте ссылку">
				<button class="button-text popup-exercise__submit-button">Добавить</button>
			</form>
		</section>
	</main>

    <?php include "../templates/footer.html" ?>

	<script>
		// Popup workouts
		let GoalsEditPopup = document.querySelector('.popup-exercise--goals-edit');
		let GoalsAddPopup = document.querySelector('.popup-exercise--goals-add');
		let CompetitionsEditPopup = document.querySelector('.popup-exercise--competitions-edit');
		let CompetitionsAddPopup = document.querySelector('.popup-exercise--competitions-add');
		let LinksEditPopup = document.querySelector('.popup-exercise--links-edit');
		let LinksAddPopup = document.querySelector('.popup-exercise--links-add');

		let GoalsEditButtons = document.querySelectorAll('.staff-block__item-button--goal-edit');
		let GoalsAddButton = document.querySelector('.staff-block__item-button--goal-add');
		let CompetitionsEditButtons = document.querySelectorAll('.staff-block__item-button--competition-edit');
		let CompetitionsAddButton = document.querySelector('.staff-block__item-button--competition-add');
		let LinksEditButtons = document.querySelectorAll('.staff-block__item-button--link-edit');
		let LinksAddButton = document.querySelector('.staff-block__item-button--link-add');

		let GoalNameText = document.querySelectorAll('.staff-block__goal-text');
		let CompetitionNameText = document.querySelectorAll('.staff-block__competition-text');
		let CompetitionFileText = document.querySelectorAll('.staff-block__link-button--competitions-file');
		let CompetitionLinkText = document.querySelectorAll('.staff-block__link-button--competitions-link');
		let InfoNameText = document.querySelectorAll('.staff-block__useful-links-text');
		let InfoFileText = document.querySelectorAll('.staff-block__link-button--info-file');
		let InfoLinkText = document.querySelectorAll('.staff-block__link-button--info-link');

		for(let i = 0; i < GoalsEditButtons.length; i++){
			GoalsEditButtons[i].addEventListener('click', function(){
				document.querySelector('.goals-edit__name').value = GoalNameText[i].innerHTML;
				GoalsEditPopup.classList.add("open");
			});
		}

		GoalsAddButton.addEventListener('click', function(){
			GoalsAddPopup.classList.add("open");
		});

		for(let i = 0; i < CompetitionsEditButtons.length; i++){
			CompetitionsEditButtons[i].addEventListener('click', function(){
				document.querySelector('.competitions-edit__name').value = CompetitionNameText[i].innerHTML;
				document.querySelector('.competitions-edit__link').value = CompetitionLinkText[i].href;
				CompetitionsEditPopup.classList.add("open");
			});
		}

		CompetitionsAddButton.addEventListener('click', function(){
			CompetitionsAddPopup.classList.add("open");
		});

		for(let i = 0; i < LinksEditButtons.length; i++){
			LinksEditButtons[i].addEventListener('click', function(){
				document.querySelector('.links-edit__name').value = InfoNameText[i].innerHTML;
				document.querySelector('.links-edit__link').value = InfoLinkText[i].href;
				LinksEditPopup.classList.add("open");
			});
		}

		LinksAddButton.addEventListener('click', function(){
			LinksAddPopup.classList.add("open");
		});

		const closeBtn = document.querySelectorAll('.popup-exercise__close-button');
		for(let i = 0; i < closeBtn.length; i++){
			closeBtn[i].addEventListener('click', function(){
				GoalsEditPopup.classList.remove("open");
				GoalsAddPopup.classList.remove("open");
				CompetitionsEditPopup.classList.remove("open");
				CompetitionsAddPopup.classList.remove("open");
				LinksEditPopup.classList.remove("open");
				LinksAddPopup.classList.remove("open");
			});
		}

		window.addEventListener('keydown', (e) => {
		if(e.key == "Escape"){
			GoalsEditPopup.classList.remove("open");
			GoalsAddPopup.classList.remove("open");
			CompetitionsEditPopup.classList.remove("open");
			CompetitionsAddPopup.classList.remove("open");
			LinksEditPopup.classList.remove("open");
			LinksAddPopup.classList.remove("open");
		}
		});

		document.querySelector('.popup-exercise__content').addEventListener('click', event => {
			event.isClickWithInModal = true;
		});
	</script>
</body>
</html>