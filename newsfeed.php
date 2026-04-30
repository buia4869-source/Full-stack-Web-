<?php


session_start();

include('./modules/shared-images.php');

define("DATABASE_FILE", "database/accounts.db");
define("IMAGE_STORAGE", "database/images/");

if (!isset($_SESSION["email"])) {
	header("location: login.php");
	exit;
}

$public_images = array();
$internal_images = array();

function sort_by_timestamp($a, $b)
{
	return $b["timestamp"] <=> $a["timestamp"];
}

function load_images()
{

	global $public_images, $internal_images;

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
			} elseif ($img['visibility'] == 'internal') {
				$internal_images[] = $saving_img;
			}
		}
	}

	usort($public_images, "sort_by_timestamp");
	usort($internal_images, "sort_by_timestamp");
}

load_images();

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

	<div class="wrapper centered">
		<div class="layout-horizontal-2" style="width: 75%">
			<div style="min-width: 40%">
				<h2>Public images</h2>
				<hr>
				<?php echo render_gallery($public_images) ?>
			</div>
			<div style="min-width: 40%">
				<h2>Internal images</h2>
				<hr>
				<?php echo render_gallery($internal_images) ?>
			</div>
		</div>
	</div>
	<?php include('./modules/footer.php') ?>

</body>

</html>