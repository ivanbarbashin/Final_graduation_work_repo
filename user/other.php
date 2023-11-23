<?php
include "../templates/func.php"; // Include functions file
include "../templates/settings.php"; // Include settings file
?>
<!DOCTYPE html>
<html lang="en">
<?php inc_head(); // print head.php ?>
<body class="other-cover">
    <?php include "../templates/header.php"; // print header template ?>

    <main class="other-block">
        <div class="container">
			<section class="other-links">
				<section class="other-links__email">
					<p>Вопросы и пожелания:</p>
					<a class="footer__email-btn" href="mailto:ivanbarbash06@gmail.com?subject=Вопрос по сайту">ivanbarbash06@gmail.com</a>
				</section>
				<section class="other-links__about">
					<h1>Автор проекта: Барбашин Иван</h1>
					<h2><p>Подробнее о проекте:</p> <a href="https://github.com/ivanbarbashin/Final_graduation_work_repo">github</a></h2>
				</section>
			</section>
		</div>
    </main>

    <?php include "../templates/footer.html"; // print footer template ?>
</body>
</html>