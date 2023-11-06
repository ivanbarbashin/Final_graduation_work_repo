<?php
include "../templates/func.php";
include "../templates/settings.php";
?>
<!DOCTYPE html>
<html lang="en">
<?php inc_head(); ?>
<body>
    <?php include "../templates/header.php" ?>

    <main class="user-news__cover">
        <div class="container">
			<section class="user-news">
				<!-- News item -->
				<?php
				if ($news = $user_data->get_news($conn)) {
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
		</div>
    </main>

    <?php include "../templates/footer.html" ?>

</body>
</html>