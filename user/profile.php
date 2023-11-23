<?php
include "../templates/func.php"; // Include functions file
include "../templates/settings.php"; // Include settings file
$request_flag = false;
if (isset($_GET["user"]) && $_GET["user"] != $user_data->get_id()){ // Check if accessing a specific user profile
    // If accessing a different user profile, set the user variable accordingly
    $user = new User($conn, $_GET["user"]); // creates a new User object based on the user ID retrieved from the $_GET parameters
    $user_data->set_staff($conn); // set data related to staff within the $user_data object
    $request_flag = $user_data->check_request($conn, $user->get_id()); // checking whether a request exists for the given user I
}else{ // If no specific user is accessed, use the logged-in user's data
    $user = $user_data;
}

// Set user subscriptions, subscribers, and staff details
$user->set_subscriptions($conn);
$user->set_subscribers($conn);
$user->set_staff($conn);

# ---------------- avatar upload ------------------------

if(isset($_POST['image_to_php']) && $user->get_auth()) { // check user avatar upload
    $user->update_avatar($conn, $_POST['image_to_php']); // update user avatar
}

// Update user description based on form submission
if (isset($_POST["change_disc"]) && $user->get_auth()){
    $new_disc = trim($_POST["change_disc"]); // Fetch new description and update user data
    if ($new_disc == ''){
        $new_disc = "Нет описания";
    }
    $user->description = $new_disc; // Update description in the database
    $sql = "UPDATE users SET description='$new_disc' WHERE id=".$user->get_id();
    if (!$conn->query($sql)){
        echo $conn->error;
    }
}

// check updates for user type
if (isset($_POST["type"])){
    // Fetch new user type and update user data
    $type = (int)$_POST["type"];
    $user->type = $type;
    // Update user type in the database
    $sql = "UPDATE users SET type=$type WHERE id=".$user->get_id();
    if (!$conn->query($sql)){
        echo $conn->error;
    }
}

// check user preparation updates(level of training)
if (isset($_POST["prep"])){
    // Fetch new preparation status and update user data
    $preparation = $_POST["prep"];
    $user->preparation = $preparation;
    // Update user preparation status in the database
    $sql = "UPDATE users SET preparation=$preparation WHERE id=".$user->get_id();
    if (!$conn->query($sql)){
        echo $conn->error;
    }
}

// Update user's Telegram link if submitted
if (isset($_POST["tg"]) && $_POST["tg"] != $user->tg){
    $tg=$_POST["tg"];
    $sql = "UPDATE users SET tg='$tg' WHERE id=".$user->get_id();
    if ($conn->query($sql))
        $user->tg = $tg;
    else
        echo $conn->error;

}

// Update user's VKontakte link if submitted
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
<?php inc_head(); // print head.php ?>
<body>
	<?php include "../templates/header.php"; // print header template ?>

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
                        <!-- Form for uploading avatar -->
                        <form id="avatar_form" class="avatar" method="post">
                            <img id="profileImage" src="<?php echo $user->get_avatar($conn); // User's avatar image ?>">
                            <input type="file" id="avatar_file" accept="image/*" />
                            <?php if ($user->get_auth()){ //if the user is logged in current acc ?>
                            <label for="avatar_file" class="uppload_button">Выбрать фото</label>
                            <?php } ?>
                            <input type="hidden" id="image_to_php" name="image_to_php" value="">
                        </form>
                        <div class="user-block__avatar-name">
                            <h1><?php echo $user->name . " " . $user->surname; // print user' mame and surname ?></h1>
                        </div>
                        <button class="user-block__avatar-more"><img src="../img/info.svg" alt=""><p>Подробнее</p></button>
                        <?php if (!$user->get_auth() && in_array($user_data->get_id(), $user->subscribers)){ // Subscription button based on authentication status ?>
                            <a class="button-text user-block__sub-button" href="unsub.php?id=<?php echo $user->get_id(); // unsub link ?>">Отписаться</a>
                        <?php }else if (!$user->get_auth()){ ?>
                            <a class="button-text user-block__sub-button" href="sub.php?id=<?php echo $user->get_id(); // sub link ?>"><p>Подписаться</p><img src="../img/add.svg" alt=""></a>
                        <?php } ?>
                    </section>
                    
                    <!-- User info text -->
					<div class="user-about__description">
                        <?php if ($user->description){ // Check if the user has a description ?>
						    <p class="user-about__description-text"><?php echo $user->description; // Display the user's description ?></p>
                        <?php } else{ //  If no description exists, display this message ?>
                            <p class="user-about__description-text">Нет описания</p>
                        <?php } ?>
                        <?php if ($user->get_auth()){ //Button to edit the description (only visible if user is authenticated) ?>
						    <button class="user-about__description-button"><img src="../img/edit_gray.svg" alt="">Изменить</button>
                        <?php } ?>
					</div>
				</section>
                <!-- User's news -->
				<section class="user-news">
					<!-- News item -->
                    <?php
                    if ($news = $user->get_my_news($conn)) { // Fetch the user's news items from the database
                        foreach ($news as $new){ // Iterate through the user's news items
                            $date = date("d.m.Y", $new['date']); // Format the date for display
                            // Define data to replace placeholders in the news item template
                            $replacements = array(
                                "{{ user_name }}" => $new['name'].' '.$new['surname'],
                                "{{ link }}" => '',
                                "{{ date }}" => $date,
                                "{{ message }}" => $new['message'],
                                "{{ avatar }}" => $new['file']
                            );
                            echo render($replacements, "../templates/news_item.html"); // Render each news item using the provided template and replaced data
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
                        <?php print_user_list($conn, $user->subscriptions); // print user's subscriptions ?>
					</section>
                </section>
                <?php if ($user->get_status() == "user"){ // if staus is user ?>
                <!-- User's staff (coach and doctor) -->
				<section class="user-block__staff">
                    <!-- Coach info and buttons to chat, ptofile and delete -->
					<div class="user-block__coach">
                        <?php $has_coach = $user->coach != NULL; // Check if the user has a coach ?>
						<p class="user-block__staff-title">Тренер: <span><?php if ($has_coach){ echo $user->coach->surname; }else{ echo "нет"; } // if user has coach print it ?></span></p>
                        <?php if ($has_coach){ ?>
                            <!-- <button class="user-block__staff-button"><img src="../img/message.svg" alt=""></button> -->
                            <a href="profile.php?user=<?php echo $user->coach->get_id(); ?>" class="user-block__staff-button"><img src="../img/profile_black.svg" alt=""></a>
                            <?php if ($user->get_auth()){ // if user is authenticated ?>
                                <a href="delete_coach.php" class="user-block__staff-button"><img src="../img/delete_black.svg" alt=""></a>
                            <?php } ?>
                        <?php }else if($user->get_auth()){ // else print button to search users ?>
                            <a href="search_users.php" class="user-block__staff-button user-block__staff-button--add"><img src="../img/add_black.svg" alt=""></a>
                        <?php } ?>
					</div>
                    <!-- Doctor info -->
					<div class="user-block__doctor">
                        <?php $has_doctor = $user->doctor != NULL; // Check if the user has a doctor ?>
						<p class="user-block__staff-title">Врач: <span><?php if ($has_doctor){ echo $user->doctor->surname; }else{ echo "нет"; } // if user has doctor print it ?></span></p>
                        <?php if ($has_doctor){ ?>
                            <!-- <button class="user-block__staff-button"><img src="../img/message.svg" alt=""></button> -->
                            <a href="profile.php?user=<?php echo $user->doctor->get_id(); ?>" class="user-block__staff-button"><img src="../img/profile_black.svg" alt=""></a>
                            <?php if ($user->get_auth()){ // if user is authenticated ?>
                                <a href="delete_doctor.php" class="user-block__staff-button"><img src="../img/delete_black.svg" alt=""></a>
                            <?php } ?>
                        <?php }else if($user->get_auth()){ // else print button to search users ?>
                            <a href="search_users.php" class="user-block__staff-button user-block__staff-button--add"><img src="../img/add_black.svg" alt=""></a>
                        <?php } ?>
					</div>
                    <!-- Count of subscribers and subscriptions -->
					<div class="user-block__sub-count">
						<p class="user-block__sub-count-item" href=""><span>Подписчики: <?php echo count($user->subscribers); // print count of subscribers ?></span></p>
						<p class="user-block__sub-count-item" href=""><span>Подписки: <?php echo count($user->subscriptions); // print count of subscriptions ?></span></p>
					</div>
				</section>

				<?php $user->print_workout_history($conn);  // print user's workout history ?>

                <?php } ?>
                <!-- Buttons to edit profile, search sportsmen and logout -->
				<section class="user-block__buttons">
                    <!-- Display 'Send Request' button under specific conditions -->
                    <?php if (!$user->get_auth() && $user_data->get_status() == "user" && (($user->get_status() == "coach" && $user_data->coach == NULL) || ($user->get_status() == "doctor" && $user_data->doctor == NULL)) && !$request_flag){ ?>
                        <a href="send_request.php?id=<?php echo $user->get_id();?>" class="button-text user-block__button"><p>Отправить заявку</p> <img src="../img/send.svg" alt=""></a>
                    <?php }
                    // Display options for authenticated users
                    if ($user->get_auth()){ ?>
                        <?php if ($user->set_program($conn)){ // Display link to 'My Program' if available ?>
                            <a href="my_program.php" class="button-text user-block__button">Моя программа<img src="../img/my_programm.svg" alt=""></a>
                        <?php } ?>
					    <a href="../clear.php" class="button-text user-block__button-logout">Выйти <img src="../img/logout.svg" alt=""></a>
                    <?php } else if ($user->set_program($conn)) { // Display link to user's program ?>
                        <a href="my_program.php?user=<?php echo $user->get_id(); ?>" class="button-text user-block__button">Программа пользователя<img src="../img/my_programm.svg" alt=""></a>
                    <?php } ?>
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
                <?php if ($user->get_status() == "user"){ ?>
                    <!-- Люитель / профессионал / не указан -->
                    <div class="popup-user__info-item">
                        <p class="popup-user__info-item-name">Тип спортсмена</p>
                        <p class="popup-user__info-item-info"><?php $user->print_type(); // print type of user ?></p>
                    </div>
                <?php } ?>
                <?php if ($user->get_status() == "coach"){ ?>
                    <!-- Люитель / профессионал / не указан -->
                    <div class="popup-user__info-item">
                        <p class="popup-user__info-item-name">Тип тренера</p>
                        <p class="popup-user__info-item-info"><?php $user->print_type(); // print type of coach ?></p>
                    </div>
                <?php } ?>
                <?php if ($user->get_status() == "doctor"){ ?>
                    <!-- Люитель / профессионал / не указан -->
                    <div class="popup-user__info-item">
                        <p class="popup-user__info-item-name">Тип доктора</p>
                        <p class="popup-user__info-item-info"><?php $user->print_type(); // print type of doctor ?></p>
                    </div>
                <?php } ?>
                <?php if ($user->get_status() == "user"){ ?>
                    <!-- низкий / средний / высокий / не указан-->
                    <div class="popup-user__info-item">
                        <p class="popup-user__info-item-name">Уровень физической подготовки</p>
                        <p class="popup-user__info-item-info"><?php $user->print_prep(); // print level of physical training ?></p>
                    </div>
                <?php } ?>
                <div class="popup-user__info-item">
                    <?php if ($user->tg != NULL){ ?>
                    <div class="popup-user__info-social">
                        <p class="popup-user__info-item-name popup-user__info-social-name">Телеграм:</p>
                        <a href="<?php echo $user->tg; // print tg link ?>"><img src="../img/tg.svg" alt=""></a>
                    </div>
                    <?php }
                    if ($user->vk != NULL){ ?>
                    <div class="popup-user__info-social popup-user__info-social-name">
                        <p class="popup-user__info-item-name popup-user__info-social-name">Вконтакте:</p>
                        <a href="<?php echo $user->vk; // print vk link ?>"><img src="../img/vk.svg" alt=""></a>
                    </div>
                    <?php } ?>
                </div>
                <?php if ($user->get_auth()){ // if user is authenticated print edit bitton ?>
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
                    <p class="popup-user__info-item-info popup-user__info-item-info--status"><?php $user->print_status(); // print status of user ?></p>
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
        let userDescriptionEditBlock = document.querySelector('.popup-user__description-edit-text'); // description editting ckecks
        if(userDescriptionEditBlock){
            userDescriptionEditBlock.addEventListener('input', function(){
                if(this.value.length > 500){
                    this.value = this.value.slice(0, 500);
                }
            });
        }


        let socialLinksInputs = document.querySelectorAll('.popup-user__input'); // social links inputs
        let editSubmitButton = document.querySelector('.popup-user__save-button'); // save changes button

        // checks for links(tg and vk)
        if(socialLinksInputs){
            const telegramRegex = /^(https?:\/\/)?(www\.)?(t\.me|telegram\.me)\/[a-zA-Z0-9_]{5,}$/;
            socialLinksInputs[0].addEventListener('input', function(){
                if (!telegramRegex.test(socialLinksInputs[0].value.trim()) && !(socialLinksInputs[0].value.trim().length == 0)) { // if link is not a telegram link add warning style
                    editSubmitButton.type = 'button';
                    socialLinksInputs[0].style.cssText = 'border: 1px solid #ff2323';
                }
                else{
                    editSubmitButton.type = 'submit';
                    socialLinksInputs[0].style.cssText = 'border: 1px solid #7D7D7D;';
                }
            });

            const vkRegex = /^(https?:\/\/)?(www\.)?(vk\.com)\/(id[0-9]+|[a-zA-Z0-9_\.]+)$/;
            socialLinksInputs[1].addEventListener('input', function(){
                if (!vkRegex.test(socialLinksInputs[1].value.trim()) && !(socialLinksInputs[1].value.trim().length == 0)) { // if link is not a vk link add warning style
                    editSubmitButton.type = 'button';
                    socialLinksInputs[1].style.cssText = 'border: 1px solid #ff2323';
                }
                else{
                    editSubmitButton.type = 'submit';
                    socialLinksInputs[1].style.cssText = 'border: 1px solid #7D7D7D;';
                }
            });
        }

        // set profile type in localstorage
        let profileType = document.querySelector('.popup-user__info-item-info--status');
        localStorage.setItem('profileType', profileType.innerHTML);

        // choose photo event listener
        avatar_file.addEventListener('click', function(){
            document.querySelector('.preview_cover').style.cssText = `display: flex;`;
        });

        // choose avatar photo logic
        $(document).ready(function () {
          var croppedImageDataURL; // variable Data URL of cropped image
      
          // When selecting a file to download
          $("#avatar_file").on("change", function (e) {
            var file = e.target.files[0];
            var reader = new FileReader();
      
            // Reading a file and displaying it in the #preview element
            reader.onload = function (event) {
              $("#preview").attr("src", event.target.result);
              initCropper();
            };
      
            reader.readAsDataURL(file);
          });
      
          // Initialization Cropper.js
          function initCropper() {
            var image = document.getElementById("preview");
            var cropper = new Cropper(image, {
                aspectRatio: 1, // The aspect ratio is 1:1 for a circular cropping area
                viewMode: 2,    // Allows you to crop the image inside the cropping area


                // Positioning the cropping area in the center
                autoCropArea: 0.6,


                // Image Cropping Processing
                crop: function (event) {
                    // Getting the coordinates and dimensions of the cropped area
                    var x = event.detail.x;
                    var y = event.detail.y;
                    var width = event.detail.width;
                    var height = event.detail.height;

                    // Creating a canvas with a cropped image
                    var canvas = cropper.getCroppedCanvas({
                        width: width,
                        height: height,
                        left: x,
                        top: y,
                    });

                    // Converting canvas to Image Data URL
                    croppedImageDataURL = canvas.toDataURL("png");
                },
            });
        }
      
          // Processing of saving an updated avatar
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

        // Event listener for save avatar button
        saveAvatar.addEventListener('click', function(){
            document.querySelector('.preview_cover').style.cssText = `display: none;`;
        });





        // More information(about user) blocks
        let MoreInfoButton = document.querySelector('.user-block__avatar-more');

        let UserInfoPopup = document.querySelector('.popup-exercise--user-info');

        // Open popup for editting 
        MoreInfoButton.addEventListener('click', function(){
			UserInfoPopup.classList.add("open");
		});



        // More information(about user) blocks (edit)
        let MoreInfoEditButton = document.querySelector('.popup-user__edit-button');

        let UserInfoEditPopup = document.querySelector('.popup-exercise--user-info-edit');

        let socialLinks = document.querySelectorAll('.popup-user__info-social a');
        let socialLinksEdit = document.querySelectorAll('.popup-user__input');

        if(MoreInfoEditButton){
            MoreInfoEditButton.addEventListener('click', function(){
                UserInfoPopup.classList.remove("open");
                UserInfoEditPopup.classList.add("open");

                for(let i = 0; i < socialLinksEdit.length; i++){
                    if(socialLinks[i] && socialLinks[i].href != ''){
                        socialLinksEdit[i].value = socialLinks[i].href;
                    }
                }
            });
        }


        // change user's description
        let DescriptionEditButton = document.querySelector('.user-about__description-button');

        let DescriptionPopup = document.querySelector('.popup-exercise--description-edit');

        // open popup window to edit user's description
        if(DescriptionEditButton){
            DescriptionEditButton.addEventListener('click', function(){
                document.querySelector('.popup-user__description-edit-text').value = document.querySelector('.user-about__description-text').innerHTML;
                DescriptionPopup.classList.add("open");
            });
        }


        // buttons to close popup windows
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
    </script>


    <!-- testing adding a description -->
    <!-- <script src="../tests/test_profile_edit_description.js"></script> -->
</body>