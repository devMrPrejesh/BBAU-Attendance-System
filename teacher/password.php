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
        <title>Change Password</title>
    </head>
    <body>
        <h1>Change Password</h1>
        <a href="index.php">Home</a>
        <a href="students.php">Students</a>
        <a href="attendance.php">Attendance</a>
        <a href="account.php">Acoount</a>
        <a href="report.php">Report</a>
        <a href="../logout.php">Logout</a>
        <br><br>
        <?php
            $teacher_id = $_SESSION['user_id'];
            changePassword($con, $teacher_id, 'teacher');
        ?>
    <body>
</html>
