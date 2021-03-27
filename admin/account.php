<?php
    ob_start();
    session_start();
    if($_SESSION['role']!='admin') header('location: ../index.php');
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
        <a href="add/student.php">Add Student</a>
        <a href="add/teacher.php">Add Teacher</a>
        <a href="modify/student.php">Modify Student</a>
        <a href="modify/teacher.php">Modify Teacher</a>
        <a href="delete/student.php">Delete Student</a>
        <a href="delete/teacher.php">Delete Teacher</a>
        <a href="password.php">Password</a>
        <a href="../logout.php">Logout</a>
        <br><br>
        <?php
            $admin_id = $_SESSION['user_id'];
            showAccount($con, $admin_id, "admin");
        ?>
    <body>
</html>
