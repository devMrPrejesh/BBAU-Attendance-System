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
        <title>Account Details</title>
    </head>
    <body>
        <h1>Account Details</h1>
        <a href="index.php">Home</a>
        <a href="password.php">Password</a>
        <a href="../logout.php">Logout</a>
        <br><br>
        <?php
            $student_id = $_SESSION['user_id'];
            showAccount($con, $student_id, "student");
        ?>
    <body>
</html>
