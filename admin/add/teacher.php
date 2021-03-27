<?php
    ob_start();
    session_start();
    if($_SESSION['role']!='admin') header('location: ../../index.php');
    include ('../../connect.php');
    include ('../../utils.php');
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Admin - Add Teacher</title>
    </head>
    <body>
        <h1>Add User</h1>
        <a href="../index.php">Home</a>
        <a href="student.php">Add Student</a>
        <a href="../modify/student.php">Modify Student</a>
        <a href="../modify/teacher.php">Modify Teacher</a>
        <a href="../delete/student.php">Delete Student</a>
        <a href="../delete/teacher.php">Delete Teacher</a>
        <a href="../account.php">Account</a>
        <a href="../password.php">Change Password</a>
        <a href="../../logout.php">Logout</a>
        <br><br>
        <?php
            $teacher_data = createForm($con, "teacher");
            if (isset($teacher_data)) {
                $email_id = $teacher_data['email_id'];
                unset($teacher_data['email_id']);
                $password = $teacher_data['password'];
                unset($teacher_data['password']);
                $user_id = $teacher_data['teacher_id'];
                unset($teacher_data['teacher_id']);
                $query = "INSERT INTO teacher VALUES ('$user_id'";
                foreach ($teacher_data as $value) {
                    $query .= ",'$value'";
                }
                $query .= ")";
                mysqli_query($con, $query);
                mysqli_query($con, "INSERT INTO user VALUES ('$email_id','$password','$user_id','teacher')");
                echo "<div>Teacher added.</div>";
            }
        ?>
    </body>
</html>
