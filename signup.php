<?php

define("DATABASE_FILE", "database/accounts.db");
define("IMAGE_STORAGE", "database/images/");

$inputEmail = "";
$inputPass = "";
$confirm_password = "";
$success = null;

if ($_SERVER["REQUEST_METHOD"] == "POST") {

	// validate names

	$first_name = $_POST["first_name"];

	if (empty($_POST["first_name"])) {
		$first_name_err = "First name is required";
	} elseif (!preg_match("/^[a-zA-Z]{2,20}$/", $first_name)) {
		$first_name_err = "First name must be between 2 and 20 characters long";
	} else {
		$first_name = trim($first_name);
	}

	$last_name = $_POST["last_name"];

	if (empty($last_name)) {
		$last_name_err = "Last name is required";
	} elseif (!preg_match("/^[a-zA-Z]{2,20}$/", $last_name)) {
		$last_name_err = "Last name must be between 2 and 20 characters long";
	} else {
		$last_name = trim($last_name);
	}

	$inputEmail = trim($_POST["email"]);

	if (empty($inputEmail)) {
		$email_err = "Please enter a email.";
	} elseif (!filter_var($inputEmail, FILTER_VALIDATE_EMAIL)) {
		$email_err = "Email is not in proper format";
	} else {
		// check if email is already taken in json file
		$file = file_get_contents(DATABASE_FILE);
		$users = json_decode($file, true);

		foreach ($users as $user) {
			if (strtolower($user["email"]) == strtolower($inputEmail)) {
				$email_err = "This email is already taken.";
			}
		}
	}

	// Validate password

	if (empty($_POST["password"])) {
		$password_err = "Please enter a password.";
	} else {
		$inputPass = trim($_POST["password"]);

		$len = strlen($inputPass);
		if (!preg_match("#.*^(?=.{8,20})(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9]).*$#", $inputPass)) {
			$password_err = "Password must have at least 8 characters and at most 20 characters and meet requirements";
		}
	}

	// Validate confirm password

	if (empty($_POST["confirm_password"])) {
		$confirm_password_err = "Please confirm password.";
	} else {
		$confirm_password = trim($_POST["confirm_password"]);
		if (empty($password_err) && ($inputPass != $confirm_password)) {
			$confirm_password_err = "Password did not match.";
		}
	}

	$target_file = "";

	// validate upload file
	if (!empty($_FILES["picture"]["name"])) {

		if ($_FILES["picture"]['error'] != 0) {
			$file_err = "File upload failed";
		} else {
			$target_file   = IMAGE_STORAGE . $_FILES["picture"]["name"];
			$imageFileType = pathinfo($target_file, PATHINFO_EXTENSION);
			$maxfilesize   = 800000;
			$allowtypes    = array('jpg', 'png', 'jpeg', 'gif');


			if ($_FILES["picture"]["size"] > $maxfilesize) {
				$file_err = "File is too large.";
			} elseif (
				!in_array($imageFileType, $allowtypes)
				|| !(getimagesize($_FILES["picture"]["tmp_name"]) !== false)
			) {
				$file_err = "Not an image file";
			} elseif (!move_uploaded_file($_FILES["picture"]["tmp_name"], $target_file)) {
				$file_err = "File upload failed";
			}
		}
	}


	// Check input errors before inserting in database
	if (
		empty($first_name_err)
		&& empty($last_name_err)
		&& empty($email_err)
		&& empty($password_err)
		&& empty($confirm_password_err)
		&& empty($file_err)
	) {

		// append given user into json file
		$fileContent = file_get_contents(DATABASE_FILE);
		$users = json_decode($fileContent, true);

		$new_user = array(
			"email" => $inputEmail,
			"password" => password_hash($inputPass, PASSWORD_DEFAULT),
			"picture" => $target_file,
			"first_name" => $first_name,
			"last_name" => $last_name,
			"images" => array()
		);

		array_push($users, $new_user);
		$file = fopen(DATABASE_FILE, "w") or die("Unable to open file!");

		fwrite(
			$file,
			json_encode($users, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
		);
		fclose($file);

		$success = true;
	}
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<title>Sign Up</title>
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
	<style>
		body {
			font: 14px sans-serif;
		}

		.wrapper {
			width: 360px;
			padding: 20px;
		}
	</style>
	<script src="cookie-consent.js"></script>


	<script>
		function isValidatedPassword() {
			const password = document.getElementById("pass");
			const retypePassword = document.getElementById("repass");

			const repassErr = document.getElementById("repass-err");

			if (password.value != retypePassword.value) {
				repassErr.innerText = "Password did not match";
				retypePassword.classList.add("is-invalid");
			} else {
				repassErr.innerText = "";
				retypePassword.classList.remove("is-invalid");
			}
		}

		window.onload = () => {
			document.getElementById("repass").addEventListener("keyup", isValidatedPassword);
		}
	</script>
	<link rel="stylesheet" href="style.css">
    <script src="https://kit.fontawesome.com/f6af0088ad.js" crossorigin="anonymous"></script>
	<script src="cookie-consent.js"></script>
</head>

<body>
	<?php include('./modules/navbar.php') ?>

	<div class="wrapper centered">
		<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data">
			<h2>Sign Up</h2>
			<p>Please fill this form to create an account.</p>

			<?php
			if (isset($success)) {
				echo
				'<div class="alert alert-success" role="alert">
								<i class="bi bi-check-circle"></i>
								New account created successfully.
						</div>';
			}
			?>
			<div class="form-group">
				<label>First Name:</label>
				<input type="text" name="first_name" class="form-control <?php echo (!empty($first_name_err)) ? 'is-invalid' : ''; ?>" value="<?php echo isset($first_name) ? $first_name : '' ?>">
				<span class="invalid-feedback"><?php echo $first_name_err; ?></span>
			</div>
			<div class="form-group">
				<label>Last Name:</label>
				<input type="text" name="last_name" class="form-control <?php echo (!empty($last_name_err)) ? 'is-invalid' : ''; ?>" value="<?php echo isset($last_name) ? $last_name : '' ?>">
				<span class="invalid-feedback"><?php echo $last_name_err; ?></span>
			</div>

			<div class="form-group">
				<label>Email:</label>
				<input type="text" name="email" class="form-control <?php echo (!empty($email_err)) ? 'is-invalid' : ''; ?>" value="<?php echo isset($inputEmail) ? $inputEmail : '' ?>">
				<span class="invalid-feedback"><?php echo $email_err; ?></span>
			</div>
			<div class="form-group">
				<label>Password:</label>
				<input id="pass" type="password" name="password" class="form-control <?php echo (!empty($password_err)) ? 'is-invalid' : ''; ?>" value="">
				<span id="pass-err" class="invalid-feedback"><?php echo $password_err; ?></span>
			</div>
			<div class="form-group">
				<label>Confirm Password:</label>
				<input id="repass" type="password" name="confirm_password" class="form-control" value="">
				<span id="repass-err" class="invalid-feedback"></span>
			</div>

			<div class="form-group">
				<label>Profile Picture:</label>
				<input type="file" name="picture" class="form-control <?php echo (!empty($file_err)) ? 'is-invalid' : ''; ?>">
				<span class="invalid-feedback"><?php echo $file_err; ?></span>
			</div>

			<div class="form-group">
				<input type="submit" class="btn btn-primary" value="Submit">
				<input type="reset" class="btn btn-secondary ml-2" value="Reset">
			</div>
			<p>Already have an account? <a href="login.php">Login here</a>.</p>
		</form>
	</div>
	<?php include('./modules/footer.php') ?>

</body>

</html>
