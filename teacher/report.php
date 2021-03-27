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
        <title>Student Report</title>
        <style>
            table, tr, th, td {
                border: 1px solid black;
            }
        </style>
    </head>
    <body>
        <h1>Student Report</h1>
        <a href="index.php">Home</a>
        <a href="students.php">Students</a>
        <a href="attendance.php">Attendance</a>
        <a href="account.php">Acoount</a>
        <a href="password.php">Password</a>
        <a href="../logout.php">Logout</a>

        <form method="post" action=""> 
            <label for="student_id">Enter Student ID</label>
            <input type="text" id="student_id" name="student_id">
            <?php
                    $dates = mysqli_fetch_row(mysqli_query($con, "SELECT MIN(date), MAX(date) FROM student_attendance"));
                    $min_date = $dates[0];
                    $max_date = $dates[1];
                    echo "<label for='from'>From</label>";
                    echo "<input type='date' id='from' name='from' min='$min_date' max='$max_date'>";
                    echo "<label for='to'>To</label>";
                    echo "<input type='date' id='to' name='to' min='$min_date' max='$max_date'>";
            ?>
            <input type="submit" name="fetch" value="Fetch"/>
        </form>
        <br>
        <?php
            if (isset($_POST['fetch'])) {
                if ($_POST['student_id'] != '' and $_POST['from'] <= $_POST['to']) {
                    $teacher_id = $_SESSION['user_id'];
                    $student_id = $_POST['student_id'];
                    $from = $_POST['from'];
                    $to = $_POST['to'];
                    $proctor_id = mysqli_fetch_row(mysqli_query($con, "SELECT teacher_id FROM student WHERE student_id='$student_id'"))[0];
                    $query = mysqli_query($con, "SELECT DISTINCT subject FROM classroom WHERE teacher_id='$teacher_id' And student_id='$student_id'");
                    if ($teacher_id == $proctor_id) {
                        echo "<div>All subject attendance:</div>";
                        drawAttendanceTable($con, $student_id, "student", $from, $to);
                    }
                    else if (mysqli_num_rows($query) != 0) {
                        $subject = mysqli_fetch_row($query)[0];
                        echo "<div>$subject attendance:</div>";
                        drawAttendanceTable($con, $student_id, "student", $from, $to, $subject);
                    }
                    else {
                        echo "<div>You are not a subject or class teacher for student.</div>";
                    }
                }
                else {
                    echo "<div>Either Student ID is empty or From date is greater than or equal to To date.</div>";
                }
            }
        ?>
    </body>
</html>
