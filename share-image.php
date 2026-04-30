<?php

session_start();
define("DATABASE_FILE", "database/accounts.db");
define("IMAGE_STORAGE", "database/images/");

if ($_SERVER["REQUEST_METHOD"] == "POST") {

	$target_file = "";

	// validate upload file
	if (!empty($_FILES["image"]["name"])) {

		if ($_FILES["image"]['error'] != 0) {
			$file_err = "File upload failed";
		} else {
			$target_file   = IMAGE_STORAGE . $_FILES["image"]["name"];
			$imageFileType = pathinfo($target_file, PATHINFO_EXTENSION);
			$maxfilesize   = 800000;
			$allowtypes    = array('jpg', 'png', 'jpeg', 'gif');


			if ($_FILES["image"]["size"] > $maxfilesize) {
				$file_err = "File is too large.";
			} elseif (
				!in_array($imageFileType, $allowtypes)
				|| !(getimagesize($_FILES["image"]["tmp_name"]) !== false)
			) {
				$file_err = "Not an image file";
			} elseif (!move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
				$file_err = "File upload failed";
			}
		}
	}

	if (empty($file_err)) {
		$fileContent = file_get_contents(DATABASE_FILE);
		$users = json_decode($fileContent, true);

		foreach ($users as &$user) {
			if ($user["email"] == $_SESSION["email"]) {

				$new_image = array(
					"imageUrl" => $target_file,
					"description" => isset($_POST["desc"]) ? $_POST["desc"] : "",
					"visibility" => $_POST["visibility"],
					"timestamp" => time()
				);

				$user["images"][] = $new_image;
				break;
			}
		}

		$file = fopen(DATABASE_FILE, "w") or die("Unable to open file!");

		fwrite(
			$file,
			json_encode($users, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
		);
		fclose($file);

		$success = true;
		header("Location: userinfo.php");
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

	<div class="centered">

		<h2>Share your image</h2>

		<?php
		if (!empty($login_err)) {
			echo '<div class="alert alert-danger">' . $login_err . '</div>';
		}
		?>


		<div>
			<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data">

				<div class="form-group">
					<label>Choose a Picture to share:</label>
					<input type="file" name="image" class="form-control <?php echo (!empty($file_err)) ? 'is-invalid' : ''; ?>">
					<span class="invalid-feedback"><?php echo $file_err; ?></span>
				</div>

				<div class="form-group">
					<label>Description:</label>
					<input type="text" name="desc" class="form-control" value="">
				</div>

				<div class="form-group">
					<label>Set your visibility:</label>
					<br>
					<div class="form-check form-check-inline">
						<input class="form-check-input" type="radio" name="visibility" id="publicRadio" value="public" checked>
						<label class="form-check-label" for="publicRadio">Public</label>
					</div>
					<div class="form-check form-check-inline">
						<input class="form-check-input" type="radio" name="visibility" id="internalRadio" value="internal">
						<label class="form-check-label" for="internalRadio">Internal</label>
					</div>
					<div class="form-check form-check-inline">
						<input class="form-check-input" type="radio" name="visibility" id="privateRadio" value="private">
						<label class="form-check-label" for="privateRadio">Private</label>
					</div>
				</div>

				<div class="form-group" style="text-align: right">
					<input type="submit" class="btn btn-primary" value="Share">
					<a href="userinfo.php" class="btn btn-light">Cancel</a>
				</div>
			</form>
		</div>





	</div>
	<?php include('./modules/footer.php') ?>


</body>

</html>