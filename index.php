<?php

ini_set('include_path', __DIR__);

session_start();
ob_start();



require_once ('e/config/config.php');
require_once ('e/modules/mysql/mysql.php');
require_once ('e/modules/user/user.php');



?>
<!doctype html>
<html class="no-js" lang="hu">

	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<meta name="robots" content="noindex, nofollow, noarchive">
		<link rel="stylesheet" href="<?php echo URL_PREFIX; ?>/styles/main.css?<?php echo date('U'); ?>">
		<title><?php echo $title; ?></title>
		<meta name="description" content="Hozzáteszi a termékekhez a gyáriszámokat">
	</head>


	<body>

		<header>		
			<div>
				Ki vagyok? <span><a href="<?php echo $_SERVER['REQUEST_URI']; ?>?logout"><?php echo $name; ?></a></span>
			</div>
			<div>
				Holvagyok? <span><?php echo $location; ?></span>
			</div>

		</header>

		<main>
			<?php if(!$user): ?>
				<!-- Egy formon megkérdezzük a nevet, és választhat 3 hely közül. -->
				<form action="<?php echo $_SERVER['REQUEST_URI'];  ?>" method="post">
					<input type="hidden" name="action" value="login">
					<div>
						<label for="nev">Ki vagy?</label>
						<input type="text" name="user" id="nev" required>
						<?php

						/**
						 * a gyariszam.gyariszamok tábla user mezőit lekérjük csoportosítva,
						 * és ha van benne már valai, akkor megjelenítünk egy slectet,
						 * hogy ne kelljen beírni mindig a nevet.
						 * 
						 * 
						 */
						$query = $conn->query("SELECT name FROM gyariszam.gyariszamok GROUP BY name");
						if ($query->num_rows > 0) {
						?>
							<select name="nev2" id="nev2">
								<option value="">Válassz...</option>
								<?php
								while ($row = $query->fetch_assoc()) {
								?>
									<option value="<?php echo $row['name']; ?>"><?php echo $row['name']; ?></option>
								<?php
								}
								?>
							</select>

							<script>
								document.getElementById('nev').focus();
								// HA választunk egy értéket "nev" selectből, akkor a "name" inputba beírjuk a választott értéket.
								document.getElementById('nev2').onchange = function() {
									document.getElementById('nev').value = this.value;
								}
								
							</script>

						<?php
						}
						?>
					</div>
					<div>
						<label for="hely">Hol vagy?</label>
						<select name="location" id="hely" required>
							<option selected disabled>Válassz...</option>
							<option value="Központ">Központ</option>
							<option value="Centrum">Centrum</option>
							<option value="Budapest">Budapest</option>
						</select>
					</div>
					<div>
						<button type="submit">Én vagyok</button>
					</div>
				</form>
			<?php else: ?>
				<?php require_once ('e/modules/pages/pages.php'); ?>
			<?php endif; ?>
		</main>


		<footer>
			<!--a href="https://github.com/borbasmatyas" target="_blank">
				<svg aria-hidden="true" height="16" viewBox="0 0 16 16" version="1.1" width="16" data-view-component="true">
					<path d="M8 0c4.42 0 8 3.58 8 8a8.013 8.013 0 0 1-5.45 7.59c-.4.08-.55-.17-.55-.38 0-.27.01-1.13.01-2.2 0-.75-.25-1.23-.54-1.48 1.78-.2 3.65-.88 3.65-3.95 0-.88-.31-1.59-.82-2.15.08-.2.36-1.02-.08-2.12 0 0-.67-.22-2.2.82-.64-.18-1.32-.27-2-.27-.68 0-1.36.09-2 .27-1.53-1.03-2.2-.82-2.2-.82-.44 1.1-.16 1.92-.08 2.12-.51.56-.82 1.28-.82 2.15 0 3.06 1.86 3.75 3.64 3.95-.23.2-.44.55-.51 1.07-.46.21-1.61.55-2.33-.66-.15-.24-.6-.83-1.23-.82-.67.01-.27.38.01.53.34.19.73.9.82 1.13.16.45.68 1.31 2.69.94 0 .67.01 1.3.01 1.49 0 .21-.15.45-.55.38A7.995 7.995 0 0 1 0 8c0-4.42 3.58-8 8-8Z"></path>
				</svg>
				@borbasmatyas
			</a-->

			<a href="https://github.com/fotoplus/gyariszam" target="_blank">
				<svg aria-hidden="true" height="16" viewBox="0 0 16 16" version="1.1" width="16" data-view-component="true">
					<path d="M2 2.5A2.5 2.5 0 0 1 4.5 0h8.75a.75.75 0 0 1 .75.75v12.5a.75.75 0 0 1-.75.75h-2.5a.75.75 0 0 1 0-1.5h1.75v-2h-8a1 1 0 0 0-.714 1.7.75.75 0 1 1-1.072 1.05A2.495 2.495 0 0 1 2 11.5Zm10.5-1h-8a1 1 0 0 0-1 1v6.708A2.486 2.486 0 0 1 4.5 9h8ZM5 12.25a.25.25 0 0 1 .25-.25h3.5a.25.25 0 0 1 .25.25v3.25a.25.25 0 0 1-.4.2l-1.45-1.087a.249.249 0 0 0-.3 0L5.4 15.7a.25.25 0 0 1-.4-.2Z"></path>
				</svg>
				fotoplus/gyariszam
			</a>

			<a href="https://github.com/fotoplus/gyariszam/tree/main" target="_blank">
				<svg text="gray" aria-hidden="true" height="16" viewBox="0 0 16 16" version="1.1" width="16" data-view-component="true">
					<path d="M9.5 3.25a2.25 2.25 0 1 1 3 2.122V6A2.5 2.5 0 0 1 10 8.5H6a1 1 0 0 0-1 1v1.128a2.251 2.251 0 1 1-1.5 0V5.372a2.25 2.25 0 1 1 1.5 0v1.836A2.493 2.493 0 0 1 6 7h4a1 1 0 0 0 1-1v-.628A2.25 2.25 0 0 1 9.5 3.25Zm-6 0a.75.75 0 1 0 1.5 0 .75.75 0 0 0-1.5 0Zm8.25-.75a.75.75 0 1 0 0 1.5.75.75 0 0 0 0-1.5ZM4.25 12a.75.75 0 1 0 0 1.5.75.75 0 0 0 0-1.5Z"></path>
				</svg>
				main
			</a>

			<a href="https://githib.com/fotoplus/gyariszam/issues" target="_blank">
				<svg aria-hidden="true" height="16" viewBox="0 0 16 16" version="1.1" width="16" data-view-component="true" class="octicon octicon-issue-opened UnderlineNav-octicon d-none d-sm-inline">
					<path d="M8 9.5a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3Z"></path><path d="M8 0a8 8 0 1 1 0 16A8 8 0 0 1 8 0ZM1.5 8a6.5 6.5 0 1 0 13 0 6.5 6.5 0 0 0-13 0Z"></path>
				</svg>
				Issues
			</a>

			<a href="/fotoplus/gyariszam/pulls" target="_blank">
                	<svg aria-hidden="true" height="16" viewBox="0 0 16 16" version="1.1" width="16" data-view-component="true" class="octicon octicon-git-pull-request UnderlineNav-octicon d-none d-sm-inline">
    				<path d="M1.5 3.25a2.25 2.25 0 1 1 3 2.122v5.256a2.251 2.251 0 1 1-1.5 0V5.372A2.25 2.25 0 0 1 1.5 3.25Zm5.677-.177L9.573.677A.25.25 0 0 1 10 .854V2.5h1A2.5 2.5 0 0 1 13.5 5v5.628a2.251 2.251 0 1 1-1.5 0V5a1 1 0 0 0-1-1h-1v1.646a.25.25 0 0 1-.427.177L7.177 3.427a.25.25 0 0 1 0-.354ZM3.75 2.5a.75.75 0 1 0 0 1.5.75.75 0 0 0 0-1.5Zm0 9.5a.75.75 0 1 0 0 1.5.75.75 0 0 0 0-1.5Zm8.25.75a.75.75 0 1 0 1.5 0 .75.75 0 0 0-1.5 0Z"></path>
				</svg>
        		Pull requests
   			</a>

		</footer>

		<!-- Scriptek -->

		<!-- Scriptek (vége) -->

	</body>
</html>