<?php
    ob_start();
    session_start();
    if($_SESSION['role']!='teacher') header('location: ../index.php');
    include ('../database/TeacherRepository.php');
    include ('../utils.php');

    $teacher_repository = new TeacherRepository();
    $teacher_id = $_SESSION['user_id'];
    $period_size = $teacher_repository->findById($teacher_id)['number_of_classes'];
    $records = $teacher_repository->findAttendanceByIdOrderByDateandPeriod($teacher_id);
    $timetable = $teacher_repository->findClassRoomByIdOrderByDayandPeriod($teacher_id);
    $subject_info = $teacher_repository->findSubjectandClassById($teacher_id);
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
        
        <table>
            <thead><tr><th>Subject</th><th>Status</th><th>Date</th><th>Period</th></tr></thead>
            <tbody>
            <?php
                foreach ($records as $record) {
                    echo "<tr><td>";
                    echo $record['subject'];
                    echo "</td><td>";
                    echo $record['status'];
                    echo "</td><td>";
                    echo $record['date'];
                    echo "</td><td>";
                    echo $record['period'];
                    echo "</td></tr>";
                }
            ?>
            </tbody>
        </table>
        <br><br>
        <table>
            <thead>
                <tr><th></th>
                <?php
                    for($i=0; $i < $period_size; $i++) {
                        echo "<th>$i</th>";
                    }
                ?>
            <thead>
            <tbody>
                <?php
                    $days = array('Mon', 'Tue', 'Wed', 'Thu', 'Fri');
                    for($i=0; $i < 5; $i++) {
                        echo "<tr><th>";
                        echo $days[$i];
                        echo "</th>";
                        for($j=0; $j < $period_size; $j++) {
                            echo "<td>";
                            echo $timetable[$i * 5 + $j]['class'];
                            echo "</td>";
                        }
                        echo "</tr>";
                    }
                ?>
            </tbody>
        </table>
        <?php
            foreach ($subject_info as $info) {
                echo $info['class'];
                echo ": ";
                echo $info['subject'];
                echo "<br>";
            }
        ?>
    </body>
</html>
