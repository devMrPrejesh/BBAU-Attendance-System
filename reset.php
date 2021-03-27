<?php include ('connect.php'); ?>
<?php include ('utils.php'); ?>

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
      
        <?php
            if (isset($_POST['reset'])) {
                $email_id = $_POST['email'];
                $row = 0;
                $query = mysqli_query($con, "select * from user where email_id = '$email_id'");
                $row = mysqli_num_rows($query);

                if ($row == 0) {
        ?>
            <div>Email ID not registered.<br>Contact Admin Department.</div>
        <?php
                }
                else {
                    $password = genratePassword();
                    $query = mysqli_query($con, "UPDATE user SET password = '$password' WHERE email_id = '$email_id'");
                    //send to mail
        ?>
            <div>Your new password is send on mail.<br>Change your password immediately.</div>
        <?php
                }
            }
        ?>
    </body>
</html>
