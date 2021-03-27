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
        <title>Admin - Add Student</title>
    </head>
    <body>
        <h1>Add User</h1>
        <a href="../index.php">Home</a>
        <a href="teacher.php">Add Teacher</a>
        <a href="../modify/student.php">Modify Student</a>
        <a href="../modify/teacher.php">Modify Teacher</a>
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
                $password = $student_data['password'];
                unset($student_data['password']);
                $user_id = $student_data['student_id'];
                unset($student_data['student_id']);
                $query = "INSERT INTO student VALUES ('$user_id'";
                foreach ($student_data as $value) {
                    $query .= ",'$value'";
                }
                $query .= ")";
                mysqli_query($con, $query);
                mysqli_query($con, "INSERT INTO user VALUES ('$email_id','$password','$user_id','student')");
                echo "<div>Student added.</div>";
            }
        ?>
    </body>
</html>
