<?php

define("DATABASE_FILE", "database/accounts.db");
define("IMAGE_STORAGE", "database/images/");

session_start();

function load_users()
{
	$file = file_get_contents(DATABASE_FILE);
	$users = json_decode($file, true);
	return $users;
}

$users = load_users();
$email_err = "";

if (isset($_GET["email"])) {
	global $users;

	$email = strtolower($_GET['email']);
	$users = array_filter($users, static function ($user) use ($email) {
		return str_contains(strtolower($user["email"]), $email);
	});

	if (empty($users)) {
		$email_err = "Given email is not existed";
	}

}

if (isset($_GET['remove-email'])) {
	$email = $_GET['remove-email'];
	foreach ($users as $key => $user) {
		if ($user['email'] == $email) {
			unset($users[$key]);
		}
	}
	file_put_contents(DATABASE_FILE, json_encode($users));
	header('Location: admin-instakilogram.php');
	exit;
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Document</title>
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
	<h2 style="text-align: center; width: 100%; margin-top: 1em;">Admin Page: Accounts management</h2>

	<div class="wrapper centered" style="justify-content: flex-start">

		<div>
			<form
				action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>"
				method="get"
				style="display: flex; align-items: flex-end; margin-bottom: 2em; width: 100%"
			>
				<div class="form-group">
					<label>Email</label>
					<input type="text" name="email" class="form-control <?php echo (!empty($email_err)) ? 'is-invalid' : ''; ?>" value="<?php echo (isset($email)) ? $email : ''; ?>">
					<span class="invalid-feedback"><?php echo $email_err; ?></span>
				</div>

				<div class="form-group" style="margin-left: 1em">
					<input type="submit" class="btn btn-primary" value="Search">
					<a href="/admin-instakilogram.php" class="btn btn-light ml-2">Reset</a>
				</div>

			</form>
		</div>

		<div>
			<table>
				<tr>
					<th>Email</th>
					<th>Action</th>
				</tr>
				<?php
				if (isset($users)) {
					foreach ($users as $user) {
						print("
								<tr>
									<td><b>{$user["email"]}</b></td>
									<td>
										<a class='btn btn-primary' href='admin-userinfo.php?email={$user["email"]}'>Details</a>
										<a class='btn btn-danger' href='?remove-email={$user["email"]}'>Delete</a>
									</td>
								</tr>
							");
					}
				}
				?>
			</table>
		</div>
	</div>
	<?php include('./modules/footer.php') ?>
</body>

</html>
