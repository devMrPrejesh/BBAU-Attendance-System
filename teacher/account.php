<?php
    ob_start();
    session_start();
    if($_SESSION['role']!='teacher') header('location: ../index.php');
    include ('../database/TeacherRepository.php');
    include ('../database/UserRepository.php');
    include ('../utils.php');
    
    $teacher_repository = new TeacherRepository();
    $user_repository = new UserRepository();
    $teacher_id = $_SESSION['user_id'];
    $email_id =$user_repository->findIdByUserIdAndRole($teacher_id, $_SESSION['role']);
    $acoount_details = $teacher_repository->findByID($teacher_id);
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Account Details</title>
    </head>
    <body>
        <h1>Account Details</h1>
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
        <br><br>
        <?php
            Utils::showAccount($email_id, $acoount_details);
        ?>
    <body>
</html>
