<?php
    ob_start();
    session_start();
    if($_SESSION['role']!='student') header('location: ../index.php');
    include ('../database/StudentRepository.php');
    include ('../database/LeaveRepository.php');
    include ('../utils.php');
    
    $student_repository = new StudentRepository();
    $leave_repository = new LeaveRepository();
    $student_id = $_SESSION['user_id'];
    $teacher_id = $student_repository->findAllByID($student_id)['teacher_id'];
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Leave Dashboard</title>
    </head>
    <body>
        <h1>Leave Dashboard</h1>
        <h3>Hi <?php echo explode(" ", $student_repository->findNameById($student_id))[0]; ?></h3>
        <a href="index.php">Home</a>
        <a href="leave.php">Apply Leave</a>
        <a href="../holiday.php">Holiday</a>
        <a href="account.php">My Account</a>
        <a href="password.php">Change Password</a>
        <a href="../logout.php">Logout</a>
        <br><br>
        <h3>Apply for leave</h3>
        <form method="post"  enctype="multipart/form-data">
            <label for="from_date">From: </label>
            <input type="date" id="from_date" name="from_date" required><br>
            <label for="to_date">To: </label>
            <input type="date" id="to_date" name="to_date" required><br>
            <label for="reason">Reason: </label>
            <textarea id="reason" name="reason" max_length=150 required></textarea><br>
            <label for="from_date">Attachment: </label>
            <input type="file" id="upload" name="upload"><br>
            <input type="submit" name="submit_leave" value="Submit">
        </form>
        <?php
            if (count($_FILES) > 0) {
                $now = date("Y-m-d");
                if ($_POST['reason'] != '' and $_POST['from_date'] != '' and $_POST['to_date'] != '' and 
                $now <= $_POST['from_date'] and $_POST['to_date'] >= $_POST['from_date'] and 
                (int) $_FILES['upload']['size'] <= 16777216) { 
                    $attachment_data = null;
                    $attachment_type = null;
                    if (is_uploaded_file($_FILES['upload']['tmp_name'])) {
                        $attachment_data = base64_encode(file_get_contents($_FILES['upload']['tmp_name']));
                        $attachment_type = $_FILES['upload']['type'];
                    }
                    if ($leave_repository->save($student_id, $teacher_id, $_POST['reason'], $_POST['from_date'], $_POST['to_date'], $attachment_type, $attachment_data) != 0) {
                        $leave_repository->createEvent($_POST['from_date']);
                        echo "Leave added.";
                    }
                    else{
                        echo "Error occured.";
                    }
                }
                else {
                    echo "Incorrect input";
                }
            }
        ?>
        <br><br>
        <h3>Leave Details</h3>
        <table>
            <thead>
            <tr><th>From</th><th>To</th><th>Reason</th><th>Status</th><th>Remarks</th><th>Attachment</th></tr>
            </thead>
            <tbody>
                <?php
                    foreach($leave_repository->findByStudentId($student_id) as $leave) {
                        $leave_id = $leave['leave_id'];
                        echo "<tr><td>";
                        echo $leave['from_date'];
                        echo "</td><td>";
                        echo $leave['to_date'];
                        echo "</td><td>";
                        echo $leave['reason'];
                        echo "</td><td>";
                        echo $leave['status'];
                        echo "</td><td>";
                        echo $leave['remarks'];
                        if ($leave['attachment_type'] != null) {
                            echo "</td><td>";
                            echo "<a href='view_attachment.php?leave_id=$leave_id'>View</a>";
                        }
                        echo "</td><tr>";
                    }
                ?>
            </tbody>
        </table>
    </body>
</html>