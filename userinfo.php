<?php

session_start();
define("DATABASE_FILE", "database/accounts.db");
define("IMAGE_STORAGE", "database/images/");

if (!isset($_SESSION["email"])) {
	header("location: login.php");
	exit;
}

$picture = "";

function load_images()
{
	$fileContent = file_get_contents(DATABASE_FILE);
	$users = json_decode($fileContent, true);

	foreach ($users as &$user) {
		if ($user["email"] == $_SESSION["email"]) {
			return $user["images"];
		}
	}
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<title>Welcome</title>
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
	<style>
		body {
			font: 14px sans-serif;
			padding: 20px;
		}

		table {
			border: black 1px solid;
			border-radius: 5px;
			margin-bottom: 50px;
		}

		td {
			border: black 1px solid;
			padding: 0.5em;
		}

		th {
			background: lightgray;
			border: black 1px solid;
			padding: 0.5em;
		}
	</style>
	<link rel="stylesheet" href="style.css">
	<script src="https://kit.fontawesome.com/f6af0088ad.js" crossorigin="anonymous"></script>
	<script src="cookie-consent.js"></script>
</head>

<body>
	<?php include('./modules/navbar.php') ?>

	<section class="bio">
		<div class="profile-photo">
			<?php
			if (!empty($_SESSION["picture"])) {
				echo "<img src='{$_SESSION["picture"]}' alt='{$_SESSION["picture"]}' width='5cm' height='5cm'>";
			}
			?>
		</div>
		<div class="profile-info">
			<p class="username"><?php echo $_SESSION["first_name"] . " " . $_SESSION["last_name"]; ?></p>
			<p>
				<em style="color: grey;">
					<?php echo $_SESSION["email"]; ?>
				</em>
			</p>
			<a class="btn btn-secondary" href="edit-info.php">Edit profile</a>
		</div>
	</section>

	<hr>
	<div style="width: 100%; display: flex; align-items: center; flex-direction: column;">

		<?php
		if (isset($success)) {
			echo
			'<div class="alert alert-success" role="alert" style="width: 40%">
								<i class="bi bi-check-circle"></i>
								File uploaded successfully.
						</div>';
		}
		?>

		<form class="upload-form" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" enctype="multipart/form-data">
			<span class="text">
				<h2>POST</h2>
			</span>
			<div style="float: right;">
				<a href="share-image.php"><i class="add-photo fa-solid fa-plus"></i></a>
			</div>
		</form>
	</div>
	<div class="gallery-nav"></div>
	<section class="gallery">
		<?php

		global $images;
		$images = load_images();

		if (isset($images)) {
			foreach ($images as $image) {
				echo
				"<div class='post-container'>
					<img src='{$image["imageUrl"]}' width='100%' height='100%'>;
					<div class='post-info'>
						<p><strong>{$image["visibility"]}</strong>: {$image["description"]}</p>
					</div>
				</div>";
			}
		}
		?>
	</section>
	<?php include('./modules/footer.php') ?>
</body>

</html>
