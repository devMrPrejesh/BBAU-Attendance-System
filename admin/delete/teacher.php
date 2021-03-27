<?php
    ob_start();
    session_start();
    if ($_SESSION['role']!='admin') header('location: ../../index.php');
    include ('../../connect.php');
    include ('../../utils.php');
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Admin - Delete Teacher</title>
    </head>
    <body>
        <h1>Delete User</h1>
        <a href="../index.php">Home</a>
        <a href="../add/student.php">Add Student</a>
        <a href="../add/teacher.php">Add Teacher</a>
        <a href="../modify/student.php">Modify Student</a>
        <a href="../modify/teacher.php">Modify Teacher</a>
        <a href="student.php">Delete Student</a>
        <a href="../account.php">Account</a>
        <a href="../password.php">Change Password</a>
        <a href="../../logout.php">Logout</a>
        <br><br>
        <form method="post">
            <label for="teacher_id">Enter Teacher ID:</label>
            <input type="number" id="teacher_id" name="teacher_id">
            <input type="submit" name="delete" value="Delete">
        </form>
        <?php
            if (isset($_POST['delete'])) {
                $teacher_id = $_POST['teacher_id'];
                mysqli_query($con, "DELETE FROM classroom WHERE teacher_id='$teacher_id'");
                mysqli_query($con, "DELETE FROM teacher_attendance WHERE teacher_id='$teacher_id'");
                mysqli_query($con, "DELETE FROM user WHERE role='teacher' AND user_id='$teacher_id'");
                echo "<div>Teacher deleted.</div>";
            }
        ?>
    </body>
</html>
