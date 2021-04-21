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
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Student Details</title>
        <style>
            table, tr, th, td {
                border: 1px solid black;
            }
        </style>
    </head>
    <body>
        <h1>Student Details</h1>
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
            <label for="value">Enter</label>
            <select name="query">
                <option value="1">Class</option>
                <option value="2">Student ID</option>
                <option value="3">Student Name</option>
            </select>
            <input type="text" id="value" name="value">
            <input type="submit" name="find" value="Find" >
        </form>
        <?php
            if (isset($_POST["find"])) {
                if ($_POST['value'] != "") {
                    $value = $_POST['value'];
                    switch ($_POST["query"]) {
                        case 1:
                            $student_details = $student_repository->findByClass($value);
                            break;
                        case 2:
                            $student_details = array($student_repository->findByID($value));
                            break;
                        default:
                            $student_details = $student_repository->findLikeName($value);
                    }
                    
                    if (count($student_details) > 0) {
                        echo "<table><thead><tr><th>Student ID</th><th>Name</th><th>Department</th><th>Class</th><tr></thead>";
                        foreach ($student_details as $student) {
                            echo "<tr>";
                            $student_id = $student['student_id'];
                            $student_name = $student['student_name'];
                            $department = $student['department'];
                            $class = $student['class'];
                            
                            echo "<td>$student_id</td>";
                            echo "<td>$student_name</td>";
                            echo "<td>$department</td>";
                            echo "<td>$class</td>";
                            echo "</tr>";
                        }
                        echo "</table>";
                    }
                    else {
                        echo "<div>No data found</div>";
                    }
                }
                else {
                    echo "<div>Field can't be empty</div>";
                }
            }
        ?>
    </body>
</html>
