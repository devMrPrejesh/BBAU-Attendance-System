<?php
    ob_start();
    session_start();
    if($_SESSION['role']!='student') header('location: ../index.php');
    include ('../database/StudentRepository.php');
    include ('../utils.php');

    $student_repository = new StudentRepository();
    $student_id = $_SESSION['user_id'];
    $period_size = $student_repository->findPeriodSizeById($student_id);
    $records = $student_repository->findAttendanceByIdOrderByDateandPeriod($student_id);
    $timetable = $student_repository->findClassRoomByIdOrderByDayandPeriod($student_id);
    $subject_info = $student_repository->findSubjectandTeacherNameById($student_id);
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Student Dashboard</title>
        <style>
            table, tr, th, td {
                border: 1px solid black;
            }
        </style>
    </head>
    <body>
        <h1>Student Dashboard</h1>
        <h3>Hi <?php echo explode(" ", $student_repository->findNameById($student_id))[0]; ?></h3>
        <a href="index.php">Home</a>
        <a href="leave.php">Apply Leave</a>
        <a href="../holiday.php">Holiday</a>
        <a href="account.php">My Account</a>
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
                            echo $timetable[$i * 5 + $j]['subject'];
                            echo "</td>";
                        }
                        echo "</tr>";
                    }
                ?>
            </tbody>
        </table>
        <?php
            foreach ($subject_info as $info) {
                echo $info['subject'];
                echo ": ";
                echo $info['teacher_name'];
                echo "<br>";
            }
        ?>
    </body>
</html>
