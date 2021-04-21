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
    $dates = $student_repository->findDateLimitByID();
    if ($dates != null) {
        $min_date = $dates['MIN(date)'];
        $max_date = $dates['MAX(date)'];
    }
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Student Attendance Report</title>
        <style>
            table, tr, th, td {
                border: 1px solid black;
            }
        </style>
    </head>
    <body>
        <h1>Student Attendance Report</h1>
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

        <?php if ($dates != null) { ?>
        <form method="post" action=""> 
            <label for="student_id">Enter Student ID</label>
            <input type="text" id="student_id" name="student_id">
            <label for='from'>From</label>
            <input type='date' id='from' name='from'>
            <label for='to'>To</label>
            <input type='date' id='to' name='to'>
            <input type="submit" name="fetch" value="Fetch"/>
        </form>
        <?php } else { echo "<div>No attendance records.</div>"; } ?>
        <br>
        <?php
            if (isset($_POST['fetch'])) {
                if ($_POST['student_id'] != '' and $min_date <= $_POST['from'] and 
                $_POST['from'] <= $_POST['to'] and $_POST['to'] <= $max_date) {
                    $student_id = $_POST['student_id'];
                    $from = $_POST['from'];
                    $to = $_POST['to'];
                    $student_details = $student_repository->findByID($student_id);
                    if ($student_details['teacher_id'] == $teacher_id) {
                        $records = $student_repository->findAttendanceByIdOrderByDateandPeriod($student_id);
                    }
                    else if ($subject = $student_repository->findSubjectByIdAndTeacherId($student_details['class'], $teacher_id)) {
                        $records = $student_repository->findAttendanceByIdAndSubjectOrderByDateandPeriod($student_id, $subject);
                    }
                    else {
                        $error_msg = "<div>You are not a subject or class teacher for student.</div>";
                    }
                    if (isset($records)) {
                        $total = 0;
                        $present = 0;
                        echo "<table><thead><tr><th>Subject</th><th>Status</th><th>Date</th><th>Period</th></tr></thead><tbody>";
                        foreach ($records as $record) {
                            echo "<tr><td>";
                            echo $record['subject'];
                            echo "</td><td>";
                            echo $record['status'];
                            if ($record['status'] == "present") {
                                $total += 1;
                                $present += 1;
                            }
                            else if ($record['status'] == "absent") {
                                $total += 1;
                            }
                            echo "</td><td>";
                            echo $record['date'];
                            echo "</td><td>";
                            echo $record['period'];
                            echo "</td></tr>";
                        }
                        echo "</tbody></table>";
                        $percent = $present / $total * 100;
                        echo "<div>Total Pecentage:$percent</div>";
                    }
                    else {
                        echo $error_msg;
                    }
                }
                else {
                    echo "<div>Incorrect Input</div>";
                }
            }
        ?>
    </body>
</html>
