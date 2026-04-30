<?php

define("DATABASE_FILE", "database/accounts.db");
define("IMAGE_STORAGE", "database/images/");
define("DEFAULT_PASSWORD", "");

session_start();

if ($_SERVER["REQUEST_METHOD"] == "GET") {

	if (!isset($_GET["action"])) {
		$file = file_get_contents(DATABASE_FILE);
		$users = json_decode($file, true);

		foreach ($users as $user) {
			if (isset($_GET["email"]) && strtolower($user["email"]) == strtolower($_GET["email"])) {
				$email = $user["email"];
				$first_name = $user["first_name"];
				$last_name = $user["last_name"];
				$picture = $user["picture"];
				$password = $user["password"];
				$images = $user["images"];
			}
		}
	} else {
		if ($_GET["action"] == "reset-password") {
			reset_password($_GET["email"]);
		}
		if ($_GET["action"] == "delete-image") {
			delete_image($_GET["email"], $_GET["img"]);
		}
	}
}

function reset_password($email)
{
	$file = file_get_contents(DATABASE_FILE);
	$users = json_decode($file, true);

	foreach ($users as &$user) {
		if ($user["email"] == $email) {
			$user["password"] = DEFAULT_PASSWORD;
			$success = true;
			export($users);
		}
	}
	header("Location: admin-userinfo.php?email=$email");
}

function delete_image($email, $image)
{
	$file = file_get_contents(DATABASE_FILE);
	$users = json_decode($file, true);

	foreach ($users as &$user) {
		if ($user["email"] == $email) {

			$user["images"] = array_filter($user["images"], static function ($e) use ($image) {
				return $e["imageUrl"] !== $image;
			});
		}
	}

	export($users);
	header("Location: admin-userinfo.php?email=$email");
}

function export($users)
{
	$file = fopen(DATABASE_FILE, "w") or die("Unable to open file!");

	fwrite(
		$file,
		json_encode($users, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
	);
	fclose($file);
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
			width: 100%;
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

		<a href="admin-instakilogram.php" style="float: left;">< Back</a>

		<h2 class="my-5">Account Detail</h2>

		<?php

		if (isset($_SESSION['picture'])) {
			echo '<img src="' . $_SESSION['picture'] . '" class="img-thumbnail" width="200" height="200">';
		}

		if (isset($success)) {
			echo
			'<div class="alert alert-success" role="alert">
						<i class="bi bi-check-circle"></i>
						New information updated successfully
			</div>';
		}
		?>

		<div>
			<table>
				<tr>
					<th>Profile Picture</th>
					<td>

						<?php
						if (isset($picture)) {
							echo "<img src='{$picture}' class='img-thumbnail' width='200' height='200'>";
						}
						?>
					</td>
				</tr>
				<tr>
					<th>Email</th>
					<td><b><?php echo $email; ?></b></td>
				</tr>
				<tr>
					<th>First Name</th>
					<td><?php echo $first_name; ?></td>
				</tr>
				<tr>
					<th>Last Name</th>
					<td><?php echo $last_name; ?></td>
				</tr>
				<tr>
					<th>Password</th>
					<td>
						<?php
						if (!empty($password)) {
							echo "<span><code><?php echo $password; ?></code></span>
						  <a href='?action=reset-password&email={$email}' class='btn btn-primary'>Reset</a>";
						} else {
							echo "<span><em>Password is empty now</em></span>";
						}
						?>
					</td>
				</tr>
			</table>
		</div>
		<div>
			<h3>Images list</h3>
			<table>

				<?php
				if (isset($images) && !empty($images)) {
					echo '<tr>
							<th>Image</th>
							<th>Action</th>
						 </tr>';
					foreach ($images as $image) {
						echo "
							<tr>
								<td><img src='{$image["imageUrl"]}' height='300px'></td>
								<td>
									<a href='?action=delete-image&email={$email}&img={$image["imageUrl"]}' class='btn btn-danger'>Delete</a>
								</td>
							</tr>
						";
					}
				} else {
					echo '<tr> <td> There is no images to show</td> </tr>';
				}
				?>
			</table>

		</div>
	</div>

	<?php include('./modules/footer.php') ?>


</body>

</html>
