<?php
    ob_start();
    session_start();
    if($_SESSION['role']!='student') header('location: ../index.php');
    include ('../connect.php');
    include ('../utils.php');
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Student Dashboard</title>
        <style>
            table, tr, th, td {
                border: 1px solid black;
            }
        </style>
    </head>
    <body>
        <h1>Student Dashboard</h1>
        <a href="account.php">My Account</a>
        <a href="password.php">Password</a>
        <a href="../logout.php">Logout</a>
        <?php 
            $student_id = $_SESSION['user_id'];
            drawAttendanceTable($con, $student_id, 'student');
        ?>
    </body>
</html>
