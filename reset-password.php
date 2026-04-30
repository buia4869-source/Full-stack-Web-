<?php

define("DATABASE_FILE", "database/accounts.db");

session_start();

$inputPass = "";
$confirm_password = "";
$success = null;

if ($_SERVER["REQUEST_METHOD"] == "POST") {

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
	// Check input errors before inserting in database
	if (
		empty($password_err)
		&& empty($confirm_password_err)
	) {

		// append given user into json file
		$fileContent = file_get_contents(DATABASE_FILE);
		$users = json_decode($fileContent, true);

		foreach ($users as &$user) {
			$user['password'] = password_hash($inputPass, PASSWORD_DEFAULT);
		}

		$file = fopen(DATABASE_FILE, "w") or die("Unable to open file!");

		fwrite(
			$file,
			json_encode($users, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
		);
		fclose($file);

		$success = true;

		unset($_SESSION['email']);
		session_destroy();
		session_start();
		header("location: login.php");
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
			<h2>Reset Password</h2>
			<p>Please fill this form to adjust your password.</p>

			<div class="form-group">
				<label>New Password:</label>
				<input id="pass" type="password" name="password" class="form-control <?php echo (!empty($password_err)) ? 'is-invalid' : ''; ?>" value="">
				<span id="pass-err" class="invalid-feedback"><?php echo $password_err; ?></span>
			</div>
			<div class="form-group">
				<label>Confirm New Password:</label>
				<input id="repass" type="password" name="confirm_password" class="form-control" value="">
				<span id="repass-err" class="invalid-feedback"></span>
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
