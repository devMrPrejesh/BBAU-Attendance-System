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
        <title>Admin - Modify Student</title>
    </head>
    <body>
        <h1>Modify User</h1>
        <a href="../index.php">Home</a>
        <a href="../add/student.php">Add Student</a>
        <a href="../add/teacher.php">Add Teacher</a>
        <a href="teacher.php">Modify Teacher</a>
        <a href="../delete/student.php">Delete Student</a>
        <a href="../delete/teacher.php">Delete Teacher</a>
        <a href="../account.php">Account</a>
        <a href="../password.php">Change Password</a>
        <a href="../../logout.php">Logout</a>
        <br><br>
        <?php
            //teavher id is foreign key required a select tag 
            $student_data = createForm($con, "student");
            if (isset($student_data)) {
                $email_id = $student_data['email_id'];
                unset($student_data['email_id']);
                unset($student_data['password']);
                $user_id = $student_data['student_id'];
                unset($student_data['student_id']);
                $query = "UPDATE student SET ";
                foreach ($student_data as $key => $value) {
                    $query .= "$key='$value',";
                }
                $query = rtrim($query, ",");
                $query .= " WHERE student_id='$user_id'";
                mysqli_query($con, $query);
                mysqli_query($con, "UPDATE user SET email_id='$email_id' WHERE user_id='$user_id' AND role='student'");
                echo "<div>Student updated.</div>";
            }
        ?>
    </body>
</html>
