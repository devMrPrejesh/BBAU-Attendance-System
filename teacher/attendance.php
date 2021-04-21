<?php
    ob_start();
    session_start();
    if($_SESSION['role']!='teacher') header('location: ../index.php');
    include ('../database/TeacherRepository.php');
    include ('../database/StudentRepository.php');
    include ('../utils.php');

    $student_repository = new StudentRepository();
    $teacher_repository = new TeacherRepository();
    $teacher_id = $_SESSION['user_id'];
    $cur_date = date("Y-m-d");
    $day = date("N");
    $period_size = $teacher_repository->findByID($teacher_id)['number_of_classes'];

    function attendanceData(array $students, int $period, string $subject): void {
        $student_list = array();
        echo '<form method="post">';
        foreach ($students as $student) {
            $student_id = $student['student_id'];
            $student_name = $student['student_name'];
            array_push($student_list, $student_id);
            echo "<label for='$student_id'>$student_id $student_name</label>";
            echo "<input type='checkbox' id='$student_id' name='$student_id'><br>";
        }
        echo "<input type='hidden' name='students' value='".serialize($student_list)."'>";
        echo "<input type='hidden' name='period' value='$period'>";
        echo "<input type='hidden' name='subject' value='$subject'>";
        echo '<input type="submit" value="Submit" name="submitted"/></form>';
        echo "</form>";
    }
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Student Attendance</title>
    </head>
    <body>
        <h1>Student Attendance</h1>
        <h3>Hi <?php echo explode(" ", $_SESSION['user_name'])[0]; ?></h3>
        <a href="index.php">Home</a>
        <a href="leave.php">Approve Leave</a>
        <a href="../holiday.php">Holiday</a>
        <a href="students.php">Students</a>
        <a href="attendance.php">Attendance</a>
        <a href="report.php">Report</a>
        <a href="account.php">Account</a>
        <a href="password.php">Change Password</a>
        <a href="../logout.php">Logout</a>
        <form method="post">
            <?php
               
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
            <input type="submit" value="Show" name="show"/>
        </form>
        <br>
        <?php
            if(isset($_POST['show']) and  $_POST['period'] != '') {
                $period = $_POST['period'];
                $teachersclass = $teacher_repository->findByTeacherIdAndDayAndPeriod($teacher_id, $day, $_POST['period']);
                if ($teachersclass == null) {
                    echo "<form method='post'>";
                    echo "<label for='class'>Class</label>";
                    echo "<input type='text' id='class' name='class'>";
                    echo '<input type="submit" value="Submit" name="submitClass"/></form>';
                    if (isset($_POST['submitClass'])) {
                        $class = $_POST['class'];
                        $subject = $teacher_repository->findSubjectByClassAndDayAndPeriod($class, $day, $period);
                        echo "<div>Substitute $class-$subject</div>";
                        $students = $student_repository->findByClassExcludedOnLeave($class, $cur_date, $period);
                        $student_attendance = attendanceData($students, $period, $subject);
                    }
                }
                else {
                    $class = $teachersclass['class'];
                    $subject = $teachersclass['subject'];
                    echo "<div>$class-$subject</div>";
                    $students = $student_repository->findByClassExcludedOnLeave($class, $cur_date, $period);
                    $student_attendance = attendanceData($students, $period, $subject);
                }
            }
            else if (isset($_POST['submitted'])) {
                $subject = $_POST['subject'];
                $period = $_POST['period'];

                foreach (unserialize($_POST['students']) as $student_id) {
                    $status = null;
                    if (array_key_exists($student_id, $_POST)) {
                        $status = "present";
                    }
                    else {
                        $status = "absent";
                    }
                    $student_repository->saveAttendance($student_id, $subject, $status, $cur_date, $period);
                }
                $teacher_repository->saveAttendance($teacher_id, $subject, "present", $cur_date, $period);
            }
        ?>
    </body>
</html>
