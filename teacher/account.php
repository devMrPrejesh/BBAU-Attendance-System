<?php
    ob_start();
    session_start();
    if($_SESSION['role']!='teacher') header('location: ../index.php');
    include ('../connect.php');
    include ('../utils.php');
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Account Details</title>
    </head>
    <body>
        <h1>Account Details</h1>      
        <a href="index.php">Home</a>
        <a href="students.php">Students</a>
        <a href="attendance.php">Attendance</a>
        <a href="report.php">Report</a>
        <a href="password.php">Password</a>
        <a href="../logout.php">Logout</a>
        <br><br>
        <?php
            $teacher_id = $_SESSION['user_id'];
            showAccount($con, $teacher_id, "teacher");
        ?>
    <body>
</html>
