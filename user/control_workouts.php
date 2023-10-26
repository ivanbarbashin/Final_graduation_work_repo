<?php
include "../templates/func.php";
include "../templates/settings.php";
?>
<!DOCTYPE html>
<html lang="en">
<?php inc_head(); ?>
<body>
    <?php include "../templates/header.php" ?>

	<main class="workouts-block">
        <div class="container">
            <?php if ($user_data->program->get_id() > 0){ ?>
            <!-- Day's workout swiper -->
            <swiper-container class="workouts-swiper" navigation="true">
                <swiper-slide class="workouts-slide">
                <!-- Slide -->
                <?php
                    $workout = new Workout($conn, $user_data->program->program[$weekday], $weekday);
                    if ($workout->holiday){
                        include "../templates/holiday.html";
                    }else{ $workout->set_muscles(); ?>
                        <!-- slide(no arrows) -->
                        <section class="workouts-card">
                            <!-- Title and button to add to favorite collection -->
                            <div class="workouts-card__header">
                                <h2 class="workouts-card__date"><?php echo date("d.m.Y"); ?></h2>
                                <button class="workouts-card__favorite-btn"><img src="../img/favorite.svg" alt=""></button>
                            </div>
                            <!-- Content of workout -->
                            <section class="workouts-card__content">
                                <!-- Exercises array -->
                                <section class="workouts-card__exercises-cover">
                                    <!-- Exercise items -->
                                    <?php $workout->print_exercises($conn); ?>
                                </section>
                                <!-- Info about day workout -->
                                <?php
                                $date1 = mktime(0, 0, 0, date("m"), date("d"), date("Y"));
                                $date2 = mktime(23, 59, 59, date("m"), date("d"), date("Y"));
                                $sql = "SELECT id FROM workout_history WHERE date_completed > $date1 AND date_completed < $date2 AND user=".$user_data->get_id();
                                if ($result = $conn->query($sql)){
                                    if ($result->num_rows == 0){
                                        $workout->print_workout_info(2, $user_data->get_id(), 1);
                                    }else{
                                        $workout->print_workout_info(2, $user_data->get_id());
                                    }
                                }else{
                                    $workout->print_workout_info(2, $user_data->get_id());
                                }
                                ?>
                            </section>
                        </section>
                    <?php } ?>
                </swiper-slide>
                <swiper-slide class="workouts-slide">
                    <?php
                    if ($weekday == 6){
                        $weekday = 0;
                    }else{
                        $weekday++;
                    }
                    $workout = new Workout($conn, $user_data->program->program[$weekday], $weekday);
                    if ($workout->holiday){
                        include "../templates/holiday.html";
                    }else{ $workout->set_muscles(); ?>
                    <section class="workouts-card">
                        <div class="workouts-card__header">
                            <h2 class="workouts-card__date"><?php echo date("d.m.Y", time() + 86400); ?></h2>
                            <button class="workouts-card__favorite-btn"><img src="../img/favorite.svg" alt=""></button>
                        </div>
                        <section class="workouts-card__content">
                            <section class="workouts-card__exercises-cover">
                                <?php $workout->print_exercises($conn); ?>
                            </section>
                            <?php $workout->print_workout_info(0, $user_data->get_id()); ?>
                        </section>
                    </section>
                    <?php } ?>
                </swiper-slide>
            </swiper-container>
            <?php } else { ?>
                <div class="workouts-card__no-program">
                    <p class="workouts-card__no-program-title">Нет тренировки</p>
                </div>
            <?php } ?>
            <section class="workout-other">
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
								<img class="last-trainings__item-img" src="../img/progress.svg" alt="">
								<p class="last-trainings__item-text last-trainings__item-text--green"><span>+ 12</span>%</p>
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
                <?php $user_data->print_workout_history($conn); ?>
                <!-- Buttons favorite workouts and my program -->
                <section class="workout-other__buttons">
                    <a class="button-text workout-other__button" href=""><p>Вся история</p> <img src="../img/my_programm.svg" alt=""></a>
                    <a class="button-text workout-other__button" href="my_program.php"><p>Новая</p> <img src="../img/my_programm.svg" alt=""></a>
                </section>
            </section>
        </div>
    </main>
	
    <?php include "../templates/footer.html" ?>

	<script src="https://cdn.jsdelivr.net/npm/swiper@10/swiper-element-bundle.min.js"></script>
</body>
</html>