<?php

if(isset($_POST['login'])){
	try{
		if(empty($_POST['username'])) throw new Exception("Username is required!");
		if(empty($_POST['password'])) throw new Exception("Password is required!");

		include ('connect.php');

		$row=0;
		$result=mysqli_query($con, "select user_id from user where email_id='$_POST[username]' and password='$_POST[password]' and role='$_POST[role]'");
		$row=mysqli_num_rows($result);

		if($row == 1) {
			session_start();
			$_SESSION['user_id']=mysqli_fetch_row($result)[0];
			switch ($_POST["role"]) {
				case "teacher":
					$_SESSION['role']="teacher";
					header('location: teacher/index.php');
					break;
				case "student":
					$_SESSION['role']="student";
					header('location: student/index.php');
					break;
				case "admin":
					$_SESSION['role']="admin";
					header('location: admin/index.php');
					break;
				default:
					throw new Exception("Email ID, Password or Role is wrong, try again!");
					session_destroy();
					header('location: index.php');
			}
		}
		else {
			throw new Exception("Email ID, Password or Role is wrong, try again!");
			header('location: index.php');
		}
	}
	catch(Exception $e){
		$error_msg=$e->getMessage();
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
			<label for="username">Username</label>
			<input type="text" name="username" id="username" placeholder="Enter Username"/>
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