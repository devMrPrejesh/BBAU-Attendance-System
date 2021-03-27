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
        <title>Teacher Dashboard</title>
        <style>
            table, tr, th, td {
                border: 1px solid black;
            }
        </style>
    </head>
    <body>
        <h1>Teacher Dashboard</h1>
        <a href="students.php">Students</a>
        <a href="attendance.php">Attendance</a>
        <a href="report.php">Report</a>
        <a href="account.php">Acoount</a>
        <a href="password.php">Password</a>
        <a href="../logout.php">Logout</a>
        <?php
            $teacher_id = $_SESSION['user_id'];
            drawAttendanceTable($con, $teacher_id, 'teacher');
        ?>
    </body>
</html>
