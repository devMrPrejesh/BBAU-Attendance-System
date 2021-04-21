<?php
    include ('utils.php');
    include ('database/UserRepository.php');
    
    $user_reposistory = new UserRepository();
    $response_msg;
    if (isset($_POST['reset']) and $_POST['email'] != "") {
        if ($user_reposistory->existById($_POST['email'])) {
            $password = Utils::generatePassword();
            $user_reposistory->updatePasswordById($_POST['email'], $password);
            Utils::sendMail($_POST['email'], "Reset Password", "Your password has been reset.\nYour new password is : $password\nChange your password immediately.");
            $response_msg = "<div>Your new password is sent on your e-mail id.<br>Change your password immediately.</div>";
        }
        else {
                $response_msg = "<div>Email ID not registered.<br>Contact Admin Department.</div>";
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Reset Password</title>
    </head>
    <body>
        <a href="index.php">Login</a>
        <h1>Reset password</h1>
        <form method="post">
            <h3>Reset your password</h3>
            <label for="email_id">Email</label>
            <input type="email" name="email" id="email_id" placeholder="Enter email"/>
            <br>
            <input type="submit" value="Reset" name="reset"/>
        </form>
        <br>
        <?php if (isset($response_msg)) { echo $response_msg; } ?>
    </body>
</html>
