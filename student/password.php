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
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Change Password</title>
    </head>
    <body>
        <h1>Change Password</h1>
        <h3>Hi <?php echo explode(" ", $student_repository->findNameById($student_id))[0]; ?></h3>
        <a href="index.php">Home</a>
        <a href="leave.php">Apply Leave</a>
        <a href="../holiday.php">Holiday</a>
        <a href="account.php">My Account</a>
        <a href="password.php">Change Password</a>
        <a href="../logout.php">Logout</a>
        <br><br>
        <?php
            $return_array = Utils::changePassword();
            if (isset($return_array)) {
                extract($return_array);
                $email_id = $user_repository->findIdByUserIdAndPasswordAndRole($student_id, $old_pass, 'student');
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
