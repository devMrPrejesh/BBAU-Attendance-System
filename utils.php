<?php
    function genratePassword() {
        $special_chars = array("@", "#", "$", "_", "-", "+", "*", "&");
        $pass_chars = array(
            chr(rand(0, 25) + 65),
            chr(rand(0, 25) + 97),
            chr(rand(0, 25) + 97),
            chr(rand(0, 9) + 48),
            $special_chars[rand(0, sizeof($special_chars)-1)],
            chr(rand(0, 9) + 48),
            chr(rand(0, 25) + 97),
            chr(rand(0, 25) + 65),
            chr(rand(0, 25) + 97),
            chr(rand(0, 25) + 97),
        );
        return join("", $pass_chars);
    }

    function drawAttendanceTable($con, $user_id, $role, $from=null, $to=null, $subject=null) {
        // Unable to print NA in last table data cell other than last one and errro when no data
        $total_periods = 0;
        $present_periods = 0;
        $column_name = $role == "teacher" ? "number_of_classes" : "number_of_subjects";
        $period_size = mysqli_fetch_row(mysqli_query($con, "select $column_name from $role where ".$role."_id='$user_id'"))[0];
        $query = "select * from ".$role."_attendance where ".$role."_id='$user_id'";
        if ($from != null) {
            $query .= " AND date >= '$from'";
        }
        if ($to != null) {
            $query .= " AND date <= '$to'";
        }
        if ($subject != null) {
            $query .= " AND subject = '$subject'";
        }
        $query .= " ORDER BY date, period";
        $attendance_records = mysqli_query($con, $query);
        ?>
            <table>
                <tr>
                    <th rowspan=2>Date</th>
                    <th colspan=<?php echo $period_size; ?>>Period</th>
                </tr>
                <tr>
                    <?php for ($x = 1; $x <= $period_size; $x++) { 
                        echo "<th>$x</th>";
                    } ?>
                </tr>
                <?php
                    $current_date = null;
                    $x = 1;
                    while ($row = mysqli_fetch_array($attendance_records)) {
                        if ($row["date"] != $current_date) {
                            $x=1;
                            $current_date = $row["date"];
                            echo "<tr><td>$current_date</td>";
                        }
                        for (; $x < $row['period']; $x++) {
                            echo "<td>NA</td>";
                        }
                        $status =$row['status'];
                        echo "<td>$status</td>";
                        if ($status == "present") $present_periods++;
                        $total_periods++;
                        $x++;
                    }
                    for (; $x <= $period_size; $x++) {
                        echo "<td>NA</td>";
                    }
                    echo "</tr>";
                ?>
            </table>
        <?php
            if ($total_periods != 0) {
                $percent = $present_periods*100/$total_periods;
                echo "<div>Total percentage: $percent</div>";
            }
            else {
                echo "<div>No Attendance Intiated.</div>";
            }
    }

    function showAccount($con, $user_id, $role) {
        $email_id = mysqli_fetch_row(mysqli_query($con, "select email_id from user where role='$role' AND user_id='$user_id'"))[0];
        $account_details = mysqli_fetch_array(mysqli_query($con, "select * from $role where ".$role."_id='$user_id'"));
        echo "<div>Email ID: $email_id</div>";
        foreach ($account_details as $key => $value) {
            if (is_numeric($key)) continue;
            $key = ucwords(str_replace("_", " ", $key));
            echo "<div>$key: $value</div>";
        }
    }

    function changePassword($con, $user_id, $role) {
        ?>
        <form method="post">
            <label for="old_password">Enter Old Password</label>
            <input type="password" id="old_password" name="old_password">
            <br>
            <label for="new_password">Enter New Password</label>
            <input type="password" id="new_password" name="new_password">
            <br>
            <label for="confirm_password">Enter Confirm New Password</label>
            <input type="password" id="confirm_password" name="confirm_password">
            <br>
            <input type="submit" value="Change Password" name="change"/>
        </form>
        <h4>Instructions for changing password:</h4>
        <h5><ul>
            <li>Password must be in between 8 and 12.</li>
            <li>Password must contain atleast one capital and one small character.</li>
            <li>Password must be alpha-numeric.</li>
            <li>Password must contain atlaest one of the special characters(@, #, $, _, -, +, *, &).</li>
        </ul></h5>
        <?php
            if (isset($_POST['change'])) {
                $old_pass = $_POST['old_password'];
                $new_pass = $_POST['new_password'];
                $confirm_pass = $_POST['confirm_password'];
                if ($old_pass != '' and $new_pass != '' and $confirm_pass != '') {
                    if ($new_pass == $confirm_pass) {
                        if (preg_match('/[A-Z]/', $new_pass) and 
                            preg_match('/[a-z]/', $new_pass) and 
                            preg_match('/[0-9]/', $new_pass) and 
                            preg_match('/[@#$_\-+*&]/', $new_pass)) {
                            $query = mysqli_query($con, "SELECT email_id FROM user WHERE user_id='$user_id' AND password='$old_pass' AND role = '$role'");
                            if (mysqli_num_rows($query) != 0) {
                                $email_id = mysqli_fetch_row($query)[0];
                                mysqli_query($con, "UPDATE user SET password='$new_pass' WHERE email_id='$email_id'");
                                echo "<div>Password changed.</div>";
                            }
                            else {
                                echo "<div>Incorrect old password.</div>";
                            }
                        }
                        else {
                            echo "<div>Password doesn't follow the format.</div>";
                        }
                    }
                    else{
                        echo "<div>New Password and Confirm New Password aren't equal.</div>";
                    }
                }
                else {
                    echo "<div>Fields can't be empty.</div>";
                }
            }
    }

    function createForm($con, $role) {
        ?>
        <form method='post'>
            <label for='email_id'>Email ID</label>
            <input type='email' id='email_id' name='email_id' required><br>
            <?php
                $form_data = array('email_id' => "");
                $query = mysqli_query($con, "DESC $role");
                while ($row = mysqli_fetch_array($query)) {
                    $raw_header = $row["Field"];
                    $header = ucwords(str_replace("_", " ", $row["Field"]));
                    echo "<label for='$raw_header'>$header</label>";
                    if (strpos($row['Type'], "int") != '') {
                        echo "<input type='number' id='$raw_header' name='$raw_header' required><br>";
                    }
                    else {
                        echo "<input type='text' id='$raw_header' name='$raw_header' required><br>";
                    }
                    $form_data[$raw_header] = "";
                }
            ?>
            <input type='submit' value='Insert' name='insert'>
        </form>
        <?php
        if (isset($_POST['insert'])) {
            foreach ($form_data as $key => $value) {
                $form_data[$key] = $_POST[$key];
            }
            $form_data['password'] = genratePassword();
            return $form_data;
        }
    }
?>