<?php
include "../templates/func.php";
include "../templates/settings.php";
if ($user_data->get_status() != "user" && $user_data->get_status() != "admin")
    header("Location: profile.php");

$user_data->set_staff($conn);
$has_coach = $user_data->coach != NULL;
$has_doctor = $user_data->doctor != NULL;
if ($has_coach){
    $coach_data = $user_data->get_coach_data($conn);
    $coach_data["competitions"] = json_decode($coach_data["competitions"]);
    $coach_data["goals"] = json_decode($coach_data["goals"]);
    $coach_data["info"] = json_decode($coach_data["info"]);
}

if ($has_doctor){
    $doctor_data = $user_data->get_doctor_data($conn);
    $doctor_data["medicines"] = json_decode($doctor_data["medicines"]);
}

?>
<!DOCTYPE html>
<html lang="en">
<?php inc_head(); ?>
<body>
    <?php include "../templates/header.php" ?>
	<main class="staff-cover">
		<div class="container">
            <?php if ($has_coach){ ?>
			<section class="staff-block">
				<p class="staff-block__title">Тренер</p>
				<section class="staff-block__header">
					<img class="staff-block__avatar" src="<?php echo $user_data->coach->get_avatar($conn); ?>" alt="">
					<section class="staff-block__info">
						<div class="staff-block__name">
							<h1 class="staff-block__name-text"><?php echo $user_data->coach->name." ".$user_data->coach->surname; ?></h1>
							<a class="staff-block__profile-link" href="profile.php?user=<?php echo $user_data->coach->get_id(); ?>"><img src="../img/profile_black.svg" alt=""></a>
						</div>
						<div class="staff-block__buttons">
                            <?php if ($user_data->coach->vk != NULL){ ?>
                            <a href="<?php echo $user_data->coach->vk; ?>" class="staff-block__button staff-block__button--img"><img src="../img/vk.svg" alt=""></a>
                            <?php }if ($user_data->coach->tg != NULL){ ?>
							<a href="<?php echo $user_data->coach->tg; ?>" class="staff-block__button staff-block__button--img"><img src="../img/tg.svg" alt=""></a>
                            <?php } ?>
							<a href="delete_coach.php?id=<?php echo $user_data->coach->get_id(); ?>" class="button-text staff-block__button staff-block__button--delite"><p>Удалить</p> <img src="../img/delete.svg" alt=""></a>
						</div>
					</section>
				</section>
				<div class="staff-block__line"></div>
				<section class="staff-block__item">
					<h2 class="staff-block__subtitle">Цели</h2>
					<ul class="staff-block__goals-list">
                        <?php if (count($coach_data["goals"]) > 0)
                            foreach ($coach_data["goals"] as $goal)
                                print_goal($conn, (int)$goal, $user_data->coach->get_id(), 0);
                        else{ ?>
                            <li class="staff-block__goals-item">Нет назначенных целей</li>
                        <?php } ?>
				</section>
				<div class="staff-block__line"></div>
				<section class="staff-block__item">
					<h2 class="staff-block__subtitle">Контрольная тренировка</h2>
					<div class="staff-block__control-workout-nearest">
						<div class="staff-block__control-workout-info">
							<p class="staff-block__control-workout-text">Близжайшая:</p>
							<div class="staff-block__control-workouts-date">12.12.2023</div>
						</div>
						<a href="" class="staff-block__button-more"><p>Подробнее</p> <img src="../img/more_white.svg" alt=""></a>
					</div>
				</section>
				<div class="staff-block__line"></div>
				<section class="staff-block__item">
					<h2 class="staff-block__subtitle">Турниры и соревнования</h2>
					<div class="staff-block__competitions">
                        <?php if (count($coach_data["competitions"]) > 0)
                            foreach ($coach_data["competitions"] as $competition)
                                print_competition($conn, (int)$competition, $user_data->get_id(), 0);
                        else{ ?>
                            <p class="staff-block__control-none">Нет назначенных соревнований</p>
                        <?php } ?>
					</div>
				</section>
				<div class="staff-block__line"></div>
				<section class="staff-block__item">
					<h2 class="staff-block__subtitle">Полезные ссылки</h2>
					<div class="staff-block__useful-links">
                        <?php if (count($coach_data["info"]) > 0)
                            foreach ($coach_data["info"] as $advice)
                                print_advice($conn, (int)$advice, $user_data->get_id(), 0);
                        else{ ?>
                            <p class="staff-block__control-none">Нет назначенных советов</p>
                        <?php } ?>
					</div>
					
				</section>
			</section>
            <?php } else { ?>
				<section class="staff-block">
					<p class="staff-block__title-none">У вас нет тренера</p>
					<a href="search_users.php" class="button-text staff-block__button-add"><p>Добавить</p><img src="../img/add.svg" alt=""></a>
				</section>
            <?php }
            if ($has_doctor){ ?>
			<section class="staff-block">
				<p class="staff-block__title">Врач</p>
				<section class="staff-block__header">
					<img class="staff-block__avatar" src="<?php echo $user_data->doctor->get_avatar($conn); ?>" alt="">
					<section class="staff-block__info">
						<div class="staff-block__name">
							<h1 class="staff-block__name-text"><?php echo $user_data->doctor->name." ".$user_data->doctor->surname; ?></h1>
							<a class="staff-block__profile-link" href="profile.php?user=<?php echo $user_data->doctor->get_id(); ?>"><img src="../img/profile_black.svg" alt=""></a>
						</div>
						<div class="staff-block__buttons">	
                            <?php if ($user_data->doctor->vk != NULL){ ?>
                                <a href="<?php echo $user_data->doctor->vk; ?>" class="staff-block__button staff-block__button--img"><img src="../img/vk.svg" alt=""></a>
                            <?php }if ($user_data->doctor->tg != NULL){ ?>
                                <a href="<?php echo $user_data->doctor->tg; ?>" class="staff-block__button staff-block__button--img"><img src="../img/tg.svg" alt=""></a>
                            <?php } ?>
							<a href="delete_doctor.php?id=<?php echo $user_data->doctor->get_id(); ?>" class="button-text staff-block__button staff-block__button--delite"><p>Удалить</p> <img src="../img/delete.svg" alt=""></a>
						</div>
					</section>
				</section>
				<div class="staff-block__line"></div>
				<section class="staff-block__item">
					<h2 class="staff-block__subtitle">Приём лекарств</h2>
                    <?php if (count($doctor_data["medicines"]) > 0)
                        foreach ($doctor_data["medicines"] as $medicine)
                            print_medicine($conn, (int)$medicine, $user_data->get_id(), 0);
                    else{ ?>
                        <p class="staff-block__medicines-none">Нет назначенных лекарств</p>
                    <?php } ?>
				</section>
				<div class="staff-block__line"></div>
				<section class="staff-block__item">
					<h2 class="staff-block__subtitle">Переиод лечения</h2>
					<div class="staff-block__treatment-date">
                        <div class="staff-block__treatment-date-item"><?php if ($doctor_data["intake_start"] == NULL) echo "Не выбрано"; else echo date("d.m.Y", $doctor_data["intake_start"]) ?></div>
                        <div class="staff-block__treatment-date-line"></div>
                        <div class="staff-block__treatment-date-item"><?php if ($doctor_data["intake_end"] == NULL) echo "Не выбрано"; else echo date("d.m.Y", $doctor_data["intake_end"]) ?></div>
                    </div>
				</section>
				<div class="staff-block__line"></div>
				<section class="staff-block__item">
					<h2 class="staff-block__subtitle">Рекомендации по лечению</h2>
                    <div class="staff-block__treatment-recommendation"><?php if (isset($doctor_data["recommendations"]) && $doctor_data["recommendations"] != "") echo $doctor_data["recommendations"]; else echo "Нет рекоммендаций"; ?></div>
                </section>
			</section>
            <?php } else { ?>
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