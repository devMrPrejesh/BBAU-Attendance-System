<?php
    ob_start();
    session_start();
    if($_SESSION['role']!='teacher') header('location: ../index.php');
    include ('../connect.php');
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
        <a href="index.php">Home</a>
        <a href="attendance.php">Attendance</a>
        <a href="report.php">Report</a>
        <a href="account.php">Acoount</a>
        <a href="../logout.php">Logout</a>
        <form method="post">
            <label for="value">Enter</label>
            <select name="query">
                <option value="1">Class-Section</option>
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
                            $arr = explode('-', $value);
                            $student_details = mysqli_query($con, "SELECT DISTINCT s.* FROM classroom c INNER JOIN student s ON c.student_id=s.student_id WHERE class = '$arr[0]' AND section = '$arr[1]'");
                            break;
                        case 2:
                            $student_details = mysqli_query($con, "select * from student where student_id='$value'");
                            break;
                        default:
                            echo "select * from student where student_name LIKE '%$value%'";
                            $student_details = mysqli_query($con, "select * from student where student_name LIKE '%$value%'");
                    }
                    $student_header = mysqli_query($con, "DESC student");
                    $col_size = mysqli_num_rows($student_header);
                    echo "<table><tr><th>Email ID</th>";
                    while ($row = mysqli_fetch_array($student_header)) {
                        $header = ucwords(str_replace("_", " ", $row["Field"]));
                        echo "<th>$header</th>";
                    }
                    echo "</tr>";
                    while ($row = mysqli_fetch_array($student_details)) {
                        echo "<tr>";
                        $student_id = $row['student_id'];
                        $email_id = mysqli_fetch_row(mysqli_query($con, "select email_id from user where role='student' AND user_id='$student_id'"))[0];
                        echo "<td>$email_id</td>";
                        for ($x=0; $x < $col_size; $x++) {
                            $data = $row[$x];
                            echo "<td>$data</td>";
                        }
                        echo "</tr>";
                    }
                }
                else {
                    echo "<div>Field can't be empty</div>";
                }
            }
        ?>
    </body>
</html>
