<?php
    ob_start();
    session_start();
    if($_SESSION['role']!='student') header('location: ../index.php');
    include ('../database/StudentRepository.php');
    include ('../database/UserRepository.php');
    include ('../utils.php');
    
    $student_repository = new StudentRepository();
    $user_repository = new UserRepository();
    $student_id = $_SESSION['user_id'];
    $email_id =$user_repository->findIdByUserIdAndRole($student_id, $_SESSION['role']);
    $acoount_details = $student_repository->findByID($student_id);
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
        <a href="leave.php">Apply Leave</a>
        <a href="../holiday.php">Holiday</a>
        <a href="account.php">My Account</a>
        <a href="password.php">Change Password</a>
        <a href="../logout.php">Logout</a>
        <br><br>
        <?php
            Utils::showAccount($email_id, $acoount_details);
        ?>
    <body>
</html>
