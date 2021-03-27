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
        <title>Admin - Delete Student</title>
    </head>
    <body>
        <h1>Delete User</h1>
        <a href="../index.php">Home</a>
        <a href="../add/student.php">Add Student</a>
        <a href="../add/teacher.php">Add Teacher</a>
        <a href="../modify/student.php">Modify Student</a>
        <a href="../modify/teacher.php">Modify Teacher</a>
        <a href="teacher.php">Delete Teacher</a>
        <a href="../account.php">Account</a>
        <a href="../password.php">Change Password</a>
        <a href="../../logout.php">Logout</a>
        <br><br>
        <form method="post">
            <label for="student_id">Enter Student ID:</label>
            <input type="number" id="student_id" name="student_id">
            <input type="submit" name="delete" value="Delete">
        </form>
        <?php
            if (isset($_POST['delete'])) {
                $student_id = $_POST['student_id'];
                mysqli_query($con, "DELETE FROM classroom WHERE student_id='$student_id'");
                mysqli_query($con, "DELETE FROM student_attendance WHERE student_id='$student_id'");
                mysqli_query($con, "DELETE FROM user WHERE role='student' AND user_id='$student_id'");
                echo "<div>Student deleted.</div>";
            }
        ?>
    </body>
</html>
