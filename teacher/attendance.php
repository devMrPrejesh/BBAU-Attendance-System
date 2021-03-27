<?php
    ob_start();
    session_start();
    if($_SESSION['role']!='teacher') header('location: ../index.php');
    include ('../connect.php');
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Attendance</title>
    </head>
    <body>
        <h1>Student Attendance</h1>
        <a href="index.php">Home</a>
        <a href="students.php">Students</a>
        <a href="report.php">Report</a>
        <a href="account.php">Acoount</a>
        <a href="password.php">Password</a>
        <a href="../logout.php">Logout</a>
        <form method="post">
            <?php
                $teacher_id = $_SESSION['user_id'];
                $student_count = null;
                $period_size = mysqli_fetch_row(mysqli_query($con, "select number_of_classes from teacher where teacher_id='$teacher_id'"))[0];
                $classroom = mysqli_query($con, "select DISTINCT class from classroom where teacher_id='$teacher_id'");
            ?>
            <label for="period">Period</label>
            <select id="period" name="period">
                <option value=''>--select--</option>
            <?php
                for ($x=1; $x<=$period_size; $x++) {
                    echo "<option value=$x>$x</option>";
                }
            ?>
            </select>
            
            <label for="class_section">Class & Section</label>
            <select id="class_section" name="class">
                <option value=''>--select--</option>
            <?php
                 while ($row = mysqli_fetch_array($classroom)) {
                    $class = $row["class"];
                    echo "<option value='$class'>$class</option>";
                }
            ?>
            <input type="submit" value="Show" name="show"/>
        </form>
        <br>
        <?php
            if(isset($_POST['show']) and  $_POST['period'] != '' and  $_POST['class'] != '') {
                $class = $_POST['class'];
                $student_ids = array();
                $students = mysqli_query($con, "SELECT DISTINCT s.student_id, s.student_name, c.subject FROM classroom c INNER join student s ON c.student_id=s.student_id WHERE c.teacher_id = '$teacher_id' AND class = '$class'");
                $_SESSION['period'] = $_POST['period'];
                echo '<form method="post">';
                while ($row = mysqli_fetch_array($students)) {
                    $student_id = $row['student_id'];
                    $student_name = $row['student_name'];
                    $_SESSION['subject'] = $row['subject'];
                    array_push($student_ids,$student_id);
                    echo "<label for='$student_id'>$student_id $student_name</label>";
                    echo "<input type='checkbox' id='$student_id' name='$student_id'><br>";
                }
                $_SESSION['student_ids'] = $student_ids;
                echo '<input type="submit" value="Submit" name="submitted"/></form>';
            }
            else if (isset($_POST['submitted'])) {
                $period = $_SESSION['period'];
                $subject = $_SESSION['subject'];
                $current_date = date("Y/m/d");
                mysqli_query($con, "INSERT INTO teacher_attendance VALUES ('$teacher_id', '$subject', 'present', '$current_date', '$period')");
                foreach ($_SESSION['student_ids'] as $student_id) {
                    $status = null;
                    if (array_key_exists(strval($student_id), $_POST)) {
                        $status = "present";
                    }
                    else {
                        $status = "absent";
                    }
                    mysqli_query($con, "INSERT INTO student_attendance VALUES ('$student_id', '$subject', '$status', '$current_date', '$period')");
                }
                echo "<div>Attendance submitted.</div>";
            }
        ?>
    </body>
</html>
