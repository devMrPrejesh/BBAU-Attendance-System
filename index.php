<?php
	include ('database/UserRepository.php');
	include ('utils.php');

	ob_start();
    session_start();
	if ( array_key_exists('role', $_SESSION)) {
		switch ($_SESSION["role"]) {
			case "teacher":
				header('location: teacher/index.php');
				break;
			case "admin":
				header('location: admin/index.php');
				break;
			default:
				header('location: student/index.php');
		}
	}

	$user_repository = new UserRepository();
	if(isset($_POST['login']) and $_POST['email_id'] != "" and $_POST['password'] != "" and $_POST['role'] != "") {
		$user_id = $user_repository->findUserIdByIdandPasswordandRole($_POST['email_id'], $_POST['password'], $_POST['role']);
		if ($user_id == null) {
			$error_msg = "Incorrect credentials.";
		}
		else {
			session_start();
			$_SESSION['user_id'] = $user_id;
			switch ($_POST["role"]) {
				case "teacher":
					include ('database/TeacherRepository.php');
					$teacher_repository = new TeacherRepository();
					$_SESSION['user_name'] = $teacher_repository->findById($user_id)['teacher_name'];
					$_SESSION['role']="teacher";
					header('location: teacher/index.php');
					break;
				case "admin":
					include ('database/AdminRepository.php');
					$admin_repository = new AdminRepository();
					$_SESSION['user_name'] = $admin_repository->findById($user_id)['admin_name'];
					$_SESSION['role']="admin";
					header('location: admin/index.php');
					break;
				default:
					include ('database/StudentRepository.php');
					$student_repository = new StudentRepository();
					$_SESSION['user_name'] = $student_repository->findById($user_id)['student_name'];
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