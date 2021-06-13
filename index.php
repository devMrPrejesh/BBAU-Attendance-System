<?php
	session_set_cookie_params(604800, "/");
	session_start();
	include ('server/Environment.php');

	$redirect = '';

	if (array_key_exists('redirect', $_GET)) {
		$redirect = "?redirect=".$_GET['redirect'];
	}

	if (array_key_exists('role', $_SESSION) && UserRole::isValid(trim($_SESSION['role']))) {
		header('location: '.constant("RedirectUrl::".trim($_SESSION['role'])).$redirect);
	}
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<title>Sign In</title>
</head>
<body></body>
</html>
