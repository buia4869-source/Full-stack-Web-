<?php

session_start();

include('./modules/shared-images.php');

define("DATABASE_FILE", "database/accounts.db");
define("IMAGE_STORAGE", "database/images/");

$public_images = array();

function sort_by_timestamp($a, $b)
{
	return $b["timestamp"] <=> $a["timestamp"];
}

function load_images()
{

	global $public_images;

	$fileContent = file_get_contents(DATABASE_FILE);
	$users = json_decode($fileContent, true);

	foreach ($users as &$user) {
		foreach ($user["images"] as $img) {
			$saving_img = array(
				"email" => $user["email"],
				"url" => $img["imageUrl"],
				"timestamp" => $img["timestamp"],
				"desc" => $img["description"]
			);


			if ($img['visibility'] == 'public') {
				$public_images[] = $saving_img;
			}
		}
	}

	usort($public_images, "sort_by_timestamp");
}

load_images();

// Check if the user is already logged in, if yes then redirect him to welcome page
if (isset($_SESSION["email"])) {


	header(
		($_SESSION["email"] == "admin")
			? "location: admin-instakilogram.php"
			: "location: userinfo.php"
	);
	exit;
}

// Define variables and initialize with empty values
$email = $password = "";
$email_err = $password_err = $login_err = "";

// Processing form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

	// Check if email is empty
	if (empty(trim($_POST["email"]))) {
		$email_err = "Please enter email.";
	} else {
		$email = trim($_POST["email"]);
	}

	// Check if password is empty
	if (empty(trim($_POST["password"]))) {
		$password_err = "Please enter your password.";
	} else {
		$password = trim($_POST["password"]);
	}

	if ($email == "admin" && $password == "admin") {
		$_SESSION["email"] = $email;
		header("location: admin-instakilogram.php");
	}

	// Validate credentials
	if (empty($email_err) && empty($password_err)) {
		// Prepare a select statement
		$file = file_get_contents(DATABASE_FILE);
		$users = json_decode($file, true);

		foreach ($users as $user) {

			if (strtolower($user["email"]) == strtolower($email)
			&& $user["password"] == "") {
				session_destroy();
				session_start();
				$_SESSION["email"] = $user["email"];
				header("location: reset-password.php");
			} elseif (
				strtolower($user["email"]) == strtolower($email)
				&& password_verify($password, $user["password"])
			) {

				$loggedin = true;

				session_destroy();

				session_start();

				$_SESSION["email"] = $user["email"];
				$_SESSION["first_name"] = $user["first_name"];
				$_SESSION["last_name"] = $user["last_name"];
				$_SESSION["picture"] = $user["picture"];

				header("location: userinfo.php");
			}
		}
		if (!isset($_SESSION["email"])) {
			$login_err = "Invalid email or password.";
		}
	}
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<title>Login</title>
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
	<link rel="stylesheet" href="style.css">
	<script src="https://kit.fontawesome.com/f6af0088ad.js" crossorigin="anonymous"></script>
	<script src="cookie-consent.js"></script>
</head>

<body>
	<?php include('./modules/navbar.php') ?>
	<div class="wrapper centered">

		<div class="layout-horizontal-2" style="width: 80%">
			<div>
				<?php echo render_gallery($public_images); ?>
			</div>
			<div style="min-width: 425px; max-width: 425px">
				<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
					<h2>Login</h2>

					<?php
					if (!empty($login_err)) {
						echo '<div class="alert alert-danger">' . $login_err . '</div>';
					}
					?>
					<div class="form-group">
						<label>Email</label>
						<input type="text" name="email" class="form-control <?php echo (!empty($email_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $email; ?>">
						<span class="invalid-feedback"><?php echo $email_err; ?></span>
					</div>
					<div class="form-group">
						<label>Password</label>
						<input type="password" name="password" class="form-control <?php echo (!empty($password_err)) ? 'is-invalid' : ''; ?>">
						<span class="invalid-feedback"><?php echo $password_err; ?></span>
					</div>
					<div class="form-group">
						<input type="submit" class="btn btn-primary" value="Login">
					</div>
					<div>
						<p>Don't have an account? <a href="signup.php">Register a new one</a>.</p>
					</div>
				</form>
			</div>
		</div>


	</div>
	<?php include('./modules/footer.php') ?>

</body>

</html>
