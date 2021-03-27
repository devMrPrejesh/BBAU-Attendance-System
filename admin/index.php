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
        <title>Admin</title>
    </head>
    <body>
        <h1>Hi Admin</h1>
        <a href="add/student.php">Add Student</a>
        <a href="add/teacher.php">Add Teacher</a>
        <a href="modify/student.php">Modify Student</a>
        <a href="modify/teacher.php">Modify Teacher</a>
        <a href="delete/student.php">Delete Student</a>
        <a href="delete/teacher.php">Delete Teacher</a>
        <a href="account.php">Account</a>
        <a href="password.php">Change Password</a>
        <a href="../logout.php">Logout</a>
    </body>
</html>
