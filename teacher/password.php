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
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Change Password</title>
    </head>
    <body>
        <h1>Change Password</h1>
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
            $return_array = Utils::changePassword();
            if (isset($return_array)) {
                extract($return_array);
                $email_id = $user_repository->findIdByUserIdAndPasswordAndRole($teacher_id, $old_pass, 'teacher');
                if ($email_id != null) {
                    $user_repository->updatePasswordByEmailId($email_id, $new_pass);
                    echo "<div>Password changed.</div>";
                }
                else {
                    echo "<div>Incorrect old password.</div>";
                }
            }
        ?>
    <body>
</html>
