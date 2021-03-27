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
        <title>Admin - Modify Teacher</title>
    </head>
    <body>
        <h1>Modify User</h1>
        <a href="../index.php">Home</a>
        <a href="../add/student.php">Add Student</a>
        <a href="../add/teacher.php">Add Teacher</a>
        <a href="student.php">Modify Student</a>
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
                unset($teacher_data['password']);
                $user_id = $teacher_data['teacher_id'];
                unset($teacher_data['teacher_id']);
                $query = "UPDATE teacher SET ";
                foreach ($teacher_data as $key => $value) {
                    $query .= "$key='$value',";
                }
                $query = rtrim($query, ",");
                $query .= " WHERE teacher_id='$user_id'";
                mysqli_query($con, $query);
                mysqli_query($con, "UPDATE user SET email_id='$email_id' WHERE user_id='$user_id' AND role='teacher'");
                echo "<div>Teacher updated.</div>";
            }
        ?>
    </body>
</html>
