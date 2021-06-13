<?php
    session_set_cookie_params(604800, "/");
	session_start();
	include ('server/Environment.php');

    $redirect = "";
	$section = "home";

	if (array_key_exists('redirect', $_GET)) {
		$redirect = "?redirect=".$_GET['redirect'];
		$section = $_GET['redirect'];
	}

	if (!(array_key_exists('role', $_SESSION) && UserRole::isValid(trim($_SESSION['role'])))) {
		header('location: '.RedirectUrl::LOGIN.$redirect);
	}
	elseif (UserRole::STUDENT !== trim($_SESSION['role'])) {
		header('location: '.constant("RedirectUrl::".trim($_SESSION['role'])).$redirect);
	}
    
    $first_name = $_SESSION['first_name'];
?>

<!DOCTYPE html>
<html>
<head>
	<title>Student Dashboard</title>
</head>
<body>
	<script> var section = '<?php echo $section; ?>'; </script>
</body>
</html>
