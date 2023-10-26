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
					<h2 class="staff-block__subtitle">Прием лекарств</h2>
					<div class="staff-block__medicines">
						<div class="staff-block__medicine-item">
							<p class="staff-block__medicine-name">Мазь</p>
							<div class="staff-block__medicine-dose">2 раза в день</div>
							<button class="button-img staff-block__item-button"><img src="../img/edit.svg" alt=""></button>
							<button class="button-img staff-block__item-button"><img src="../img/delete.svg" alt=""></button>
						</div>
						<div class="staff-block__medicine-item">
							<p class="staff-block__medicine-name">Мазь</p>
							<div class="staff-block__medicine-dose">2 раза в день</div>
							<button class="button-img staff-block__item-button"><img src="../img/edit.svg" alt=""></button>
							<button class="button-img staff-block__item-button"><img src="../img/delete.svg" alt=""></button>
						</div>
					</div>
					<button class="button-text staff-block__item-button--add"><p>Добавить</p><img src="../img/add.svg" alt=""></button>
				</section>
				<div class="staff-block__line"></div>
				<section class="staff-block__item">
					<h2 class="staff-block__subtitle">Период лечения</h2>
					<div class="staff-block__treatment-date">
						<div class="staff-block__treatment-date-item">12.02.2023</div>
						<div class="staff-block__treatment-date-line"></div>
						<div class="staff-block__treatment-date-item">12.03.2023</div>
					</div>
					<div class="staff-block__treatment-buttons">
						<button class="button-img staff-block__item-button staff-block__item-button--date"><img src="../img/edit.svg" alt=""></button>
					</div>
				</section>
				<div class="staff-block__line"></div>
				<section class="staff-block__item">
					<h2 class="staff-block__subtitle">Рекомендации по лечению</h2>
					<div class="staff-block__treatment-recommendation">Избегать физических нагрузок, побольше кайфа и чайку оформить. АААААААААААААААААААААА</div>
					<div class="staff-block__treatment-buttons">
						<button class="button-img staff-block__item-button staff-block__item-button--recommendation"><img src="../img/edit.svg" alt=""></button>
					</div>
				</section>
			</section>
			<section class="staff-other">
				<section class="friends-block">
                    <!-- Title and button to search friends -->
                    <div class="friends-block__header">
                        <h1 class="friends-block__header-title">Другие спортсмены</h1>
                        <a class="friends-block__header-button" href=""><img src="../img/search.svg" alt=""></a>
                    </div>
                    <!-- Friends' workout swiper -->
                   <section class="friends-block__cover" navigation="true">
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
				<button class="button-text staff-other__button"><p>Группы</p> <img src="../img/my_programm.svg" alt=""></button>
			</section>
		</div>

		<!-- Edit medicine -->
		<section class="popup-exercise popup-exercise--edit-medicine">
			<form method="post" class="popup-exercise__content">
				<button type="button" class="popup-exercise__close-button"><img src="../img/close.svg" alt=""></button>
				<input class="popup-exercise__input-item edit-medicine__name" type="text" placeholder="название">
				<input class="popup-exercise__input-item edit-medicine__dose" type="text" placeholder="доза">
				<button class="button-text popup-exercise__submit-button">Сохранить</button>
			</form>
		</section>

		<!-- Add medicine -->
		<section class="popup-exercise popup-exercise--add-medicine">
			<form method="post" class="popup-exercise__content">
				<button type="button" class="popup-exercise__close-button"><img src="../img/close.svg" alt=""></button>
				<input class="popup-exercise__input-item add-medicine__name" type="text" placeholder="название">
				<input class="popup-exercise__input-item add-medicine__dose" type="text" placeholder="доза">
				<button class="button-text popup-exercise__submit-button">Добавить</button>
			</form>
		</section>

		<!-- Treatment date -->
		<section class="popup-exercise popup-exercise--treatment-date">
			<form method="post" class="popup-exercise__content">
				<button type="button" class="popup-exercise__close-button"><img src="../img/close.svg" alt=""></button>
				<input class="popup-exercise__input-item treatment-date__start" type="text" placeholder="начало">
				<input class="popup-exercise__input-item treatment-date__end" type="text" placeholder="начало">
				<button class="button-text popup-exercise__submit-button">Сохранить</button>
			</form>
		</section>

		<!-- Treatment recommendation -->
		<section class="popup-exercise popup-exercise--treatment-recommendation">
			<form method="post" class="popup-exercise__content">
				<button type="button" class="popup-exercise__close-button"><img src="../img/close.svg" alt=""></button>
				<textarea class="doctor-texterea-item treatment-recommendation__edit" name="" id="" placeholder="рекомендации"></textarea>
				<button class="button-text popup-exercise__submit-button">Сохранить</button>
			</form>
		</section>
	</main>

    <?php include "../templates/footer.html" ?>

	<script>
		// Popup workouts
		let MedicineEditPopup = document.querySelector('.popup-exercise--edit-medicine');
		let MedicineAddPopup = document.querySelector('.popup-exercise--add-medicine');
		let TreatmentDatePopup = document.querySelector('.popup-exercise--treatment-date');
		let RecommendationPopup = document.querySelector('.popup-exercise--treatment-recommendation');
		
		let MedicineEditButtons = document.querySelectorAll('.staff-block__medicines .staff-block__item-button');
		let MedicineAddButton = document.querySelector('.staff-block__item-button--add');
		let TreatmentDateEditButton = document.querySelector('.staff-block__item-button--date');
		let RecommendationEditButton = document.querySelector('.staff-block__item-button--recommendation');

		let MedicineNameText = document.querySelectorAll('.staff-block__medicine-name');
		let MedicineDoseText = document.querySelectorAll('.staff-block__medicine-dose');

		for(let i = 0; i < MedicineEditButtons.length; i++){
			MedicineEditButtons[i].addEventListener('click', function(){
				document.querySelector('.edit-medicine__name').value = MedicineNameText[i].innerHTML;
				document.querySelector('.edit-medicine__dose').value = MedicineDoseText[i].innerHTML;
				MedicineEditPopup.classList.add("open");
			});
		}

		MedicineAddButton.addEventListener('click', function(){
			MedicineAddPopup.classList.add("open");
		});

		let TreatmentDateText = document.querySelectorAll('.staff-block__treatment-date-item');

		TreatmentDateEditButton.addEventListener('click', function(){
			document.querySelector('.treatment-date__start').value = TreatmentDateText[0].innerHTML;
			document.querySelector('.treatment-date__end').value = TreatmentDateText[1].innerHTML;
			TreatmentDatePopup.classList.add("open");
		});

		RecommendationEditButton.addEventListener('click', function(){
			document.querySelector('.treatment-recommendation__edit').value = document.querySelector('.staff-block__treatment-recommendation').innerHTML;
			RecommendationPopup.classList.add("open");
		});

		const closeBtn = document.querySelectorAll('.popup-exercise__close-button');
		for(let i = 0; i < closeBtn.length; i++){
			closeBtn[i].addEventListener('click', function(){
				MedicineEditPopup.classList.remove("open");
				MedicineAddPopup.classList.remove("open");
				TreatmentDatePopup.classList.remove("open");
				RecommendationPopup.classList.remove("open");
			});
		}

		window.addEventListener('keydown', (e) => {
		if(e.key == "Escape"){
			MedicineEditPopup.classList.remove("open");
			MedicineAddPopup.classList.remove("open");
			TreatmentDatePopup.classList.remove("open");
			RecommendationPopup.classList.remove("open");
		}
		});

		document.querySelector('.popup-exercise__content').addEventListener('click', event => {
			event.isClickWithInModal = true;
		});


	</script>
</body>
</html>