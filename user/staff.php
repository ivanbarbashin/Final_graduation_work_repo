<?php
include "../templates/func.php"; // Include functions file
include "../templates/settings.php"; // Include settings file
if ($user_data->get_status() != "user" && $user_data->get_status() != "admin") // Check user status and restrict access if not "user" or "admin"
    header("Location: profile.php"); // Redirect to profile page

$user_data->set_staff($conn); // Set staff-related information for the user
$has_coach = $user_data->coach != NULL; // Check if the user has a coach
$has_doctor = $user_data->doctor != NULL; // Check if the user has a doctor
if ($has_coach){ // If user has a coach, fetch and decode coach-related data
    $coach_data = $user_data->get_coach_data($conn);
    $coach_data["competitions"] = json_decode($coach_data["competitions"]);
    $coach_data["goals"] = json_decode($coach_data["goals"]);
    $coach_data["info"] = json_decode($coach_data["info"]);
}

if ($has_doctor){ // If user has a doctor, fetch and decode doctor-related data
    $doctor_data = $user_data->get_doctor_data($conn);
    $doctor_data["medicines"] = json_decode($doctor_data["medicines"]);
}

$control_workouts = $user_data->get_control_workouts($conn, $user_data->get_id(), 0); // get control workouts to the user

?>
<!DOCTYPE html>
<html lang="en">
<?php inc_head(); // print head.php ?>
<body class="staff-page">
    <?php include "../templates/header.php" // include header template ?>
	<main class="staff-cover">
		<div class="container">
            <?php if ($has_coach){ // if user has a coach ?>
			<section class="staff-block">
				<p class="staff-block__title">Тренер</p>
				<section class="staff-block__header">
					<img class="staff-block__avatar" src="<?php echo $user_data->coach->get_avatar($conn); // get coach avatar ?>" alt="">
					<section class="staff-block__info">
						<div class="staff-block__name">
							<h1 class="staff-block__name-text"><?php echo $user_data->coach->name." ".$user_data->coach->surname; // get coach name and coach surname ?></h1>
							<a class="staff-block__profile-link" href="profile.php?user=<?php echo $user_data->coach->get_id(); // link to coach's profile ?>"><img src="../img/profile_black.svg" alt=""></a>
						</div>
						<div class="staff-block__buttons">
                            <?php if ($user_data->coach->vk != NULL){ // Display VK Contact Button if available ?>
                            <a href="<?php echo $user_data->coach->vk; ?>" class="staff-block__button staff-block__button--img"><img src="../img/vk.svg" alt=""></a>
                            <?php }if ($user_data->coach->tg != NULL){ // Display Telegram Contact Button if available ?>
							<a href="<?php echo $user_data->coach->tg; ?>" class="staff-block__button staff-block__button--img"><img src="../img/tg.svg" alt=""></a>
                            <?php } ?>
							<a href="delete_coach.php?id=<?php echo $user_data->coach->get_id(); // link to delite coach ?>" class="button-text staff-block__button staff-block__button--delite"><p>Удалить</p> <img src="../img/delete.svg" alt=""></a>
						</div>
					</section>
				</section>
				<div class="staff-block__line"></div>
				<section class="staff-block__item">
					<h2 class="staff-block__subtitle">Цели</h2>
					<ul class="staff-block__goals-list">
                        <?php if (count($coach_data["goals"]) > 0) // Check if there are goals for the coach
                            foreach ($coach_data["goals"] as $goal) // If goals exist, loop through each goal and print it
                                print_goal($conn, (int)$goal, $user_data->coach->get_id(), 0); // Call a function to print the goal using provided parameters
                        else{ // If there are no goals, print a default message ?>
                            <li class="staff-block__goals-item">Нет назначенных целей</li>
                        <?php } ?>
					</ul>
				</section>
				<div class="staff-block__line"></div>
                <section class="staff-block__item">
                    <h2 class="staff-block__subtitle">Контрольные тренировки</h2>
                    <?php if (count($control_workouts) > 0){ // If there are control workouts available ?>
                        <div class="staff-block__control-workout-nearest">
                            <div class="staff-block__control-workout-info">
                                <p class="staff-block__control-workout-text">Ближайшая:</p>
                                <div class="staff-block__control-workouts-date"><?php echo date("d.m.Y", ($control_workouts[0])->date); // Display the nearest workout date ?></div>
                            </div>
							<a href="control_workouts.php?user=<?php echo $user_data->get_id(); // Link to view more about control workouts ?>" class="staff-block__button-more"><p>Подробнее</p> <img src="../img/more_white.svg" alt=""></a>
                        </div>
                    <?php } else { // If there are no control workouts, print a default message ?>
                        <p class="staff-block__control-none">Нет назначенных контрольных тренировок</p>
                    <?php } ?>
                </section>
				<div class="staff-block__line"></div>
				<section class="staff-block__item">
					<h2 class="staff-block__subtitle">Турниры и соревнования</h2>
					<div class="staff-block__competitions">
                        <?php if (count($coach_data["competitions"]) > 0) // Check if there are competitions
                            foreach ($coach_data["competitions"] as $competition) // If competitions exist, loop through each competition and print it
                                print_competition($conn, (int)$competition, $user_data->get_id(), 0); // Call a function to print the competition using provided parameters
                        else{ // If there are no competitions, print a default message ?>
                            <p class="staff-block__control-none">Нет назначенных соревнований</p>
                        <?php } ?>
					</div>
				</section>
				<div class="staff-block__line"></div>
				<section class="staff-block__item">
					<h2 class="staff-block__subtitle">Полезные ссылки</h2>
					<div class="staff-block__useful-links">
                        <?php if (count($coach_data["info"]) > 0) // Check if there are useful links/advice
                            foreach ($coach_data["info"] as $advice) // If there are links/advice, loop through each one and print it
                                print_advice($conn, (int)$advice, $user_data->get_id(), 0); // Call a function to print the advice using provided parameters
                        else{ // If there are no links/advice, print a default message ?>
                            <p class="staff-block__control-none">Нет назначенных советов</p>
                        <?php } ?>
					</div>
				</section>
			</section>
            <?php } else { // if the user does not have a coach ?>
				<section class="staff-block">
					<p class="staff-block__title-none">У вас нет тренера</p>
					<a href="search_users.php" class="button-text staff-block__button-add"><p>Добавить</p><img src="../img/add.svg" alt=""></a>
				</section>
            <?php }
            if ($has_doctor){ // if user has a doctor ?>
			<section class="staff-block">
				<p class="staff-block__title">Врач</p>
				<section class="staff-block__header">
					<img class="staff-block__avatar" src="<?php echo $user_data->doctor->get_avatar($conn); // get doctor avatar ?>" alt="">
					<section class="staff-block__info">
						<div class="staff-block__name">
							<h1 class="staff-block__name-text"><?php echo $user_data->doctor->name." ".$user_data->doctor->surname; // get doctor name and coach surname ?></h1>
							<a class="staff-block__profile-link" href="profile.php?user=<?php echo $user_data->doctor->get_id(); ?>"><img src="../img/profile_black.svg" alt=""></a>
						</div>
						<div class="staff-block__buttons">	
                            <?php if ($user_data->doctor->vk != NULL){ // Display VK Contact Button if available ?>
                                <a href="<?php echo $user_data->doctor->vk; ?>" class="staff-block__button staff-block__button--img"><img src="../img/vk.svg" alt=""></a>
                            <?php }if ($user_data->doctor->tg != NULL){ // Display Telegram Contact Button if available ?>
                                <a href="<?php echo $user_data->doctor->tg; ?>" class="staff-block__button staff-block__button--img"><img src="../img/tg.svg" alt=""></a>
                            <?php } ?>
							<a href="delete_doctor.php?id=<?php echo $user_data->doctor->get_id(); // // link to delite doctor ?>" class="button-text staff-block__button staff-block__button--delite"><p>Удалить</p> <img src="../img/delete.svg" alt=""></a>
						</div>
					</section>
				</section>
				<div class="staff-block__line"></div>
				<section class="staff-block__item">
					<h2 class="staff-block__subtitle">Приём лекарств</h2>
                    <?php if (count($doctor_data["medicines"]) > 0) // Check if there are prescribed medicines by the doctor for the user
                        foreach ($doctor_data["medicines"] as $medicine) // If medicines exist, loop through each medicine and print it
                            print_medicine($conn, (int)$medicine, $user_data->get_id(), 0); // Call a function to print the medicine using provided parameters
                    else{ // If there are no prescribed medicines, print a default message ?>
                        <p class="staff-block__medicines-none">Нет назначенных лекарств</p>
                    <?php } ?>
				</section>
				<div class="staff-block__line"></div>
				<section class="staff-block__item">
					<h2 class="staff-block__subtitle">Переиод лечения</h2>
					<div class="staff-block__treatment-date">
                        <div class="staff-block__treatment-date-item"><?php if ($doctor_data["intake_start"] == NULL) echo "Не выбрано"; else echo date("d.m.Y", $doctor_data["intake_start"]) // Display start date of treatment or "Не выбрано" if not selected ?></div>
                        <div class="staff-block__treatment-date-line"></div>
                        <div class="staff-block__treatment-date-item"><?php if ($doctor_data["intake_end"] == NULL) echo "Не выбрано"; else echo date("d.m.Y", $doctor_data["intake_end"]) // Display end date of treatment or "Не выбрано" if not selected ?></div>
                    </div>
				</section>
				<div class="staff-block__line"></div>
				<section class="staff-block__item">
					<h2 class="staff-block__subtitle">Рекомендации по лечению</h2>
                    <div class="staff-block__treatment-recommendation"><?php if (isset($doctor_data["recommendations"]) && $doctor_data["recommendations"] != "") echo $doctor_data["recommendations"]; else echo "Нет рекоммендаций"; // If recommendations exist, display them, else display default message ?></div>
                </section>
			</section>
            <?php } else { // if the user does not have a doctor ?>
				<section class="staff-block">
					<p class="staff-block__title-none">У вас нет врача</p>
					<a href="search_users.php" class="button-text staff-block__button-add"><p>Добавить</p><img src="../img/add.svg" alt=""></a>
				</section>
            <?php } ?>
		</div>
	</main>

    <?php include "../templates/footer.html" ?>
</body>
</html>