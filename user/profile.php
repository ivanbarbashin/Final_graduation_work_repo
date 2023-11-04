<?php
include "../templates/func.php";
include "../templates/settings.php";
$request_flag = false;
if (isset($_GET["user"])){
    $user = new User($conn, $_GET["user"]);
    $user_data->set_staff($conn);
    $request_flag = $user_data->check_request($conn, $user->get_id());
}else{
    $user = $user_data;
}
$user->set_subscriptions($conn);
$user->set_subscribers($conn);
$user->set_staff($conn);

# ---------------- avatar upload ------------------------

if(isset($_POST['image_to_php']) && $user->get_auth()) {
    $user->update_avatar($conn, $_POST['image_to_php']);
}


if (isset($_POST["change_disc"]) && $user->get_auth()){
    $new_disc = trim($_POST["change_disc"]);
    if ($new_disc == ''){
        $new_disc = "Нет описания";
    }
    $user->description = $new_disc;
    $sql = "UPDATE users SET description='$new_disc' WHERE id=".$user->get_id();
    if (!$conn->query($sql)){
        echo $conn->error;
    }
}

if (isset($_POST["type"])){
    $type = (int)$_POST["type"];
    $user->type = $type;
    $sql = "UPDATE users SET type=$type WHERE id=".$user->get_id();
    if (!$conn->query($sql)){
        echo $conn->error;
    }
}

if (isset($_POST["prep"])){
    $preparation = $_POST["prep"];
    $user->preparation = $preparation;
    $sql = "UPDATE users SET preparation=$preparation WHERE id=".$user->get_id();
    if (!$conn->query($sql)){
        echo $conn->error;
    }
}

if (isset($_POST["tg"]) && $_POST["tg"] != $user->tg){
    $tg=$_POST["tg"];
    $sql = "UPDATE users SET tg='$tg' WHERE id=".$user->get_id();
    if ($conn->query($sql))
        $user->tg = $tg;
    else
        echo $conn->error;

}

if (isset($_POST["vk"]) && $_POST["vk"] != $user->vk){
    $vk=$_POST["vk"];
    $sql = "UPDATE users SET vk='$vk' WHERE id=".$user->get_id();
    if ($conn->query($sql))
        $user->vk = $vk;
    else
        echo $conn->error;
}
?>
<!DOCTYPE html>
<html lang="en">
<?php inc_head(); ?>
<body>
	<?php include "../templates/header.php"; ?>

	<main class="user-block">
        <section class="preview_cover">
            <div class="preview_block">
                <img id="preview" src="#" alt="Preview"/>
            </div>
            <button id="saveAvatar">Сохранить</button>
        </section>

		<div class="container">
            <!-- Info about user -->
			<section class="user-block__info">
				<section class="user-about">
					<!-- User avatar, name and surname -->
                    <section class="user-block__avatar">
                        <form id="avatar_form" class="avatar" method="post">
                            <img id="profileImage" src="<?php echo $user->get_avatar($conn); ?>">
                            <input type="file" id="avatar_file" accept="image/*" />
                            <?php if ($user->get_auth()){?>
                            <label for="avatar_file" class="uppload_button">Choose photo</label>
                            <?php } ?>
                            <input type="hidden" id="image_to_php" name="image_to_php" value="">
                        </form>
                        <div class="user-block__avatar-name">
                            <h1><?php echo $user->name . " " . $user->surname; ?></h1>
                        </div>
                        <button class="user-block__avatar-more"><img src="../img/info.svg" alt=""><p>Подробнее</p></button>
                        <?php
                         if (!$user->get_auth() && in_array($user_data->get_id(), $user->subscribers)){ ?>
                            <a class="button-text user-block__sub-button" href="unsub.php?id=<?php echo $user->get_id(); ?>">Отписаться</a>
                        <?php }else if (!$user->get_auth()){ ?>
                            <a class="button-text user-block__sub-button" href="sub.php?id=<?php echo $user->get_id(); ?>"><p>Подписаться</p><img src="../img/add.svg" alt=""></a>
                        <?php }
                         ?>
                    </section>
                    
                    <!-- User info text -->
					<div class="user-about__description">
                        <?php if ($user->description){ ?>
						    <p class="user-about__description-text"><?php echo $user->description; ?></p>
                        <?php } else{ ?>
                            <p class="user-about__description-text">Нет описания</p>
                        <?php } ?>
                        <?php if ($user->get_auth()){ ?>
						    <button class="user-about__description-button"><img src="../img/edit_gray.svg" alt="">Изменить</button>
                        <?php } ?>
					</div>
				</section>
                <!-- User's news -->
				<section class="user-news">
					<!-- News item -->
                    <?php
                    if ($news = $user->get_my_news($conn)) {
                        foreach ($news as $new){
                            $date = date("d.m.Y", $new['date']);
                            $replacements = array(
                                "{{ user_name }}" => $new['name'].' '.$new['surname'],
                                "{{ link }}" => '',
                                "{{ date }}" => $date,
                                "{{ message }}" => $new['message'],
                                "{{ avatar }}" => $new['file']
                            );
                            echo render($replacements, "../templates/news_item.html");
                        }
                    }?>
				</section>
			</section>
			<section class="user-block__other">
				<section class="friends-block">
                    <!-- Title and button to search friends -->
                    <div class="friends-block__header">
                        <h1 class="friends-block__header-title">Подписки</h1>
                        <a href="search_users.php" class="friends-block__header-button" href=""><img src="../img/search.svg" alt=""></a>
                    </div>
                    <!-- Friends swiper -->
                    <section class="friends-block__cover" navigation="true">
                        <?php print_user_list($conn, $user->subscriptions); ?>
					</section>
                </section>
                <?php if ($user->get_status() == "user"){ ?>
                <!-- User's staff (coach and doctor) -->
				<section class="user-block__staff">
                    <!-- Coach info and buttons to chat, ptofile and delete -->
					<div class="user-block__coach">
                        <?php $has_coach = $user->coach != NULL; ?>
						<p class="user-block__staff-title">Тренер: <span><?php if ($has_coach){ echo $user->coach->surname; }else{ echo "нет"; } ?></span></p>
                        <?php if ($has_coach){ ?>
                            <!-- <button class="user-block__staff-button"><img src="../img/message.svg" alt=""></button> -->
                            <a href="profile.php?user=<?php echo $user->coach->get_id(); ?>" class="user-block__staff-button"><img src="../img/profile_black.svg" alt=""></a>
                            <?php if ($user->get_auth()){ ?>
                                <a href="delete_coach.php" class="user-block__staff-button"><img src="../img/delete_black.svg" alt=""></a>
                            <?php } ?>
                        <?php }else{ ?>
                            <a href="search_users.php" class="user-block__staff-button"><img src="../img/add_black.svg" alt=""></a>
                        <?php } ?>
					</div>
                    <!-- Doctor info -->
					<div class="user-block__doctor">
                        <?php $has_doctor = $user->doctor != NULL; ?>
						<p class="user-block__staff-title">Врач: <span><?php if ($has_doctor){ echo $user->doctor->surname; }else{ echo "нет"; } ?></span></p>
                        <?php if ($has_doctor){ ?>
                            <!-- <button class="user-block__staff-button"><img src="../img/message.svg" alt=""></button> -->
                            <a href="profile.php?user=<?php echo $user->doctor->get_id(); ?>" class="user-block__staff-button"><img src="../img/profile_black.svg" alt=""></a>
                            <?php if ($user->get_auth()){ ?>
                                <a href="delete_doctor.php" class="user-block__staff-button"><img src="../img/delete_black.svg" alt=""></a>
                            <?php } ?>
                        <?php }else{ ?>
                            <a href="search_users.php" class="user-block__staff-button"><img src="../img/add_black.svg" alt=""></a>
                        <?php } ?>
					</div>
                    <!-- Count of subscribers and subscriptions -->
					<div class="user-block__sub-count">
						<p class="user-block__sub-count-item" href=""><span><?php echo count($user->subscribers); ?> подписчика(ов)</span></p>
						<p class="user-block__sub-count-item" href=""><span><?php echo count($user->subscriptions); ?> подписок(ски)</span></p>
					</div>
				</section>

				<?php $user->print_workout_history($conn); ?>

                <?php } ?>
                <!-- Buttons to edit profile, search sportsmen and logout -->
				<section class="user-block__buttons">
                    <?php if (!$user->get_auth() && $user_data->get_status() == "user" && (($user->get_status() == "coach" && $user_data->coach == NULL) || ($user->get_status() == "doctor" && $user_data->doctor == NULL)) && !$request_flag){ ?>
                        <a href="send_request.php?id=<?php echo $user->get_id();?>" class="button-text user-block__button"><p>Отправить заявку</p> <img src="../img/send.svg" alt=""></a>
                    <?php } ?>
                    <?php if ($user->get_auth()){ ?>
					<a href="../clear.php" class="button-text user-block__button-logout">Выйти <img src="../img/logout.svg" alt=""></a>
                    <?php }?>
				</section>
			</section>
		</div>


        <!-- User more information -->
        <section class="popup-exercise popup-exercise--user-info">
			<section class="popup-exercise__content popup-exercise__content--user-info">
                <button type="button" class="popup-exercise__close-button"><img src="../img/close.svg" alt=""></button>
                <!-- Спортсмен / тренер / врач -->
                <div class="popup-user__info-item">
                    <p class="popup-user__info-item-name">Тип пользователя</p>
                    <p class="popup-user__info-item-info"><?php $user->print_status(); ?></p>
                </div>

                <!-- Люитель / профессионал / не указан -->
				<div class="popup-user__info-item">
                    <p class="popup-user__info-item-name">Тип спортсмена</p>
                    <p class="popup-user__info-item-info"><?php $user->print_type(); ?></p>
                </div>
                <?php if ($user->get_status() == "user"){ ?>
                    <!-- низкий / средний / высокий / не указан-->
                    <div class="popup-user__info-item">
                        <p class="popup-user__info-item-name">Уровень физической подготовки</p>
                        <p class="popup-user__info-item-info"><?php $user->print_prep(); ?></p>
                    </div>
                <?php } ?>
                <div class="popup-user__info-item">
                    <?php if ($user->tg != NULL){ ?>
                    <div class="popup-user__info-social">
                        <p class="popup-user__info-item-name popup-user__info-social-name">Телеграм:</p>
                        <a href="<?php echo $user->tg; ?>"><img src="../img/tg.svg" alt=""></a>
                    </div>
                    <?php }
                    if ($user->vk != NULL){ ?>
                    <div class="popup-user__info-social popup-user__info-social-name">
                        <p class="popup-user__info-item-name popup-user__info-social-name">Вконтакте:</p>
                        <a href="<?php echo $user->vk; ?>"><img src="../img/vk.svg" alt=""></a>
                    </div>
                    <?php } ?>
                </div>
                <?php if ($user->get_auth()){  ?>
                    <button type="button" class="popup-user__edit-button"><img src="../img/edit_gray.svg" alt="">Изменить</button>
                <?php } ?>
			</section>
		</section>

        <!-- User more information (version for editting) -->
        <section class="popup-exercise popup-exercise--user-info-edit">
			<form method="post" class="popup-exercise__content popup-exercise__content--user-info">
                <button type="button" class="popup-exercise__close-button"><img src="../img/close.svg" alt=""></button>
                <!-- Спортсмен / тренер / врач -->
                <div class="popup-user__info-item">
                <p class="popup-user__info-item-name">Тип пользователя</p>
                    <p class="popup-user__info-item-info"><?php $user->print_status(); ?></p>
                </div>
               <?php switch ($user->get_status()){
                   case "coach":
               ?>
                <div class="popup-user__info-item">
                    <p class="popup-user__info-item-name">Тип тренера</p>
                    <select class="popup-user__select" name="type" id="">
                        <option class="popup-user__option" selected value="0">не указан</option>
                        <option class="popup-user__option" value="1">личный тренер</option>
                        <option class="popup-user__option" value="2">тренер команды</option>
                    </select>
                </div>
                   <?php
                    break;
                   case "doctor":?>

                <div class="popup-user__info-item">
                    <p class="popup-user__info-item-name">Тип доктора</p>
                    <select class="popup-user__select" name="type" id="">
                        <option class="popup-user__option" selected value="0">не указан</option>
                        <option class="popup-user__option" value="1">личный врач</option>
                        <option class="popup-user__option" value="2">врач команды</option>
                    </select>
                </div>

                <?php break;
                   case "user":
                ?>
				<div class="popup-user__info-item">
                    <p class="popup-user__info-item-name">Тип спортсмена</p>
                    <select class="popup-user__select" name="type" id="">
                        <option class="popup-user__option" selected value="0">не указан</option>
                        <option class="popup-user__option" value="1">любитель</option>
                        <option class="popup-user__option" value="2">профессионал</option>
                    </select>
                </div>
                <?php break;
               } ?>

                <?php if ($user->get_status() == "user"){ ?>
                    <!-- низкий / средний / высокий / не указан-->
                    <div class="popup-user__info-item">
                        <p class="popup-user__info-item-name">Уровень физической подготовки</p>
                        <select class="popup-user__select" name="prep" id="">
                            <option class="popup-user__option" selected value="0">не указан</option>
                            <option class="popup-user__option" value="1">низкий</option>
                            <option class="popup-user__option" value="2">средний</option>
                            <option class="popup-user__option" value="3">высокий</option>
                        </select>
                    </div>
                <?php } ?>

                <div class="popup-user__info-item">
                    <p class="popup-user__info-item-name">Телеграм:</p>
                    <input name="tg" class="popup-user__input" type="text" placeholder="вставьте ссылку">
                </div>

                <div class="popup-user__info-item">
                    <p class="popup-user__info-item-name">Вконтакте:</p>
                    <input name="vk" class="popup-user__input" type="text" placeholder="вставьте ссылку">
                </div>

                <button type="submit" class="button-text popup-user__save-button">Сохранить</button>
            </form>
		</section>

        <!-- description edit -->
		<section class="popup-exercise popup-exercise--description-edit">
			<form method="post" class="popup-exercise__content">
                <button type="button" class="popup-exercise__close-button"><img src="../img/close.svg" alt=""></button>
				<textarea class="popup-user__description-edit-text" name="change_disc" id=""></textarea>
				<button class="button-text popup-exercise__submit-button">Сохранить</button>
			</form>
		</section>
	</main>

	<?php include "../templates/footer.html";?>

	<script src="https://cdn.jsdelivr.net/npm/swiper@10/swiper-element-bundle.min.js"></script>
    <!-- Подключение скриптов для Cropper.js и jQuery (если требуется) -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.12/cropper.min.js"></script>
    <script>
        avatar_file.addEventListener('click', function(){
            document.querySelector('.preview_cover').style.cssText = `display: flex;`;
        });

        $(document).ready(function () {
          var croppedImageDataURL; // Переменная для хранения Data URL обрезанного изображения
      
          // При выборе файла для загрузки
          $("#avatar_file").on("change", function (e) {
            var file = e.target.files[0];
            var reader = new FileReader();
      
            // Чтение файла и отображение его в элементе img#preview
            reader.onload = function (event) {
              $("#preview").attr("src", event.target.result);
              initCropper();
            };
      
            reader.readAsDataURL(file);
          });
      
          // Инициализация Cropper.js
          function initCropper() {
            var image = document.getElementById("preview");
            var cropper = new Cropper(image, {
                aspectRatio: 1, // Соотношение сторон 1:1 для круглой области обрезки
                viewMode: 2,    // Позволяет обрезать изображение внутри области обрезки


                // Позиционируем область обрезки в центре
                autoCropArea: 0.6,


                // Обработка обрезки изображения
                crop: function (event) {
                    // Получение координат и размеров обрезанной области
                    var x = event.detail.x;
                    var y = event.detail.y;
                    var width = event.detail.width;
                    var height = event.detail.height;

                    // Создание canvas с обрезанным изображением
                    var canvas = cropper.getCroppedCanvas({
                        width: width,
                        height: height,
                        left: x,
                        top: y,
                    });

                    // Преобразование canvas в Data URL изображения
                    croppedImageDataURL = canvas.toDataURL("png");
                },
            });
        }
      
          // Обработка сохранения обновленной аватарки
          $("#saveAvatar").on("click", function () {
            if (croppedImageDataURL) {
                location.reload()
                image_to_php.value = croppedImageDataURL
                document.getElementById("avatar_form").submit();
            } 
            else {
              alert("Сначала выберите и обрежьте изображение");
            }
          });
        });

        saveAvatar.addEventListener('click', function(){
            document.querySelector('.preview_cover').style.cssText = `display: none;`;
        });





        // =========Подробная информация=========
        let MoreInfoButton = document.querySelector('.user-block__avatar-more');

        let UserInfoPopup = document.querySelector('.popup-exercise--user-info');

        MoreInfoButton.addEventListener('click', function(){
			UserInfoPopup.classList.add("open");
		});



        // Подробная информация (изменение)
        let MoreInfoEditButton = document.querySelector('.popup-user__edit-button');

        let UserInfoEditPopup = document.querySelector('.popup-exercise--user-info-edit');

        let socialLinks = document.querySelectorAll('.popup-user__info-social a');
        let socialLinksEdit = document.querySelectorAll('.popup-user__input');

        MoreInfoEditButton.addEventListener('click', function(){
            UserInfoPopup.classList.remove("open");
			UserInfoEditPopup.classList.add("open");

            for(let i = 0; i < socialLinksEdit.length; i++){
                if(socialLinks[i].href != ''){
                    socialLinksEdit[i].value = socialLinks[i].href;
                }
            }
		});



        // Изменение описания
        let DescriptionEditButton = document.querySelector('.user-about__description-button');

        let DescriptionPopup = document.querySelector('.popup-exercise--description-edit');

        DescriptionEditButton.addEventListener('click', function(){
            document.querySelector('.popup-user__description-edit-text').value = document.querySelector('.user-about__description-text').innerHTML;
			DescriptionPopup.classList.add("open");
		});



        const closeBtn = document.querySelectorAll('.popup-exercise__close-button');
		for(let i = 0; i < closeBtn.length; i++){
			closeBtn[i].addEventListener('click', function(){
				UserInfoPopup.classList.remove("open");
			    UserInfoEditPopup.classList.remove("open");
                DescriptionPopup.classList.remove("open");
			});
		}

		window.addEventListener('keydown', (e) => {
            if(e.key == "Escape"){
                UserInfoPopup.classList.remove("open");
                UserInfoEditPopup.classList.remove("open");
                DescriptionPopup.classList.remove("open");
            }
		});

		document.querySelector('.popup-exercise__content').addEventListener('click', event => {
			event.isClickWithInModal = true;
		});

    </script>
</body>