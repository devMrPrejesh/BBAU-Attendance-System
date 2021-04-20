<?php
	include ('database/UserRepository.php');
	include ('utils.php');

	$user_repository = new UserRepository();
	if(isset($_POST['login']) and $_POST['email_id'] != "" and $_POST['password'] != "" and $_POST['role'] != "") {
		$user_id = $user_repository->findUserIdByEmailIdandPasswordandRole($_POST['email_id'], $_POST['password'], $_POST['role']);
		if ($user_id == null) {
			$error_msg = "Incorrect credentials.";
		}
		else {
			session_start();
			$_SESSION['user_id'] = $user_id;
			switch ($_POST["role"]) {
				case "teacher":
					$_SESSION['role']="teacher";
					header('location: teacher/index.php');
					break;
				case "admin":
					$_SESSION['role']="admin";
					header('location: admin/index.php');
					break;
				default:
					$_SESSION['role']="student";
					header('location: student/index.php');
			}
		}
	}
?>

<!DOCTYPE html>
<html>
	<head>
		<title>Login</title>
	</head>
	<body>
		<h1>Login</h1>

		<?php if(isset($error_msg)) echo $error_msg; ?>

		<form method="post">
			<label for="email_id">E-mail ID</label>
			<input type="text" name="email_id" id="email_id" placeholder="Enter E-mail ID"/>
			<br>
			<label for="password">Password</label>
			<input type="password" name="password" id="password" placeholder="Enter Password"/>
			<br>
			<label>Role</label>
			<label>
			    <input type="radio" name="role" value="student" checked> Student
			</label>
			<label>
			    <input type="radio" name="role" value="teacher"> Teacher
			</label>
			<label>
				<input type="radio" name="role" value="admin"> Admin
			</label>
			<br>
			<input type="submit" value="Login" name="login"/>
		</form>
		<br>
		Have forgot your password? <a href="reset.php">Reset here.</a>
	</body>
</html>