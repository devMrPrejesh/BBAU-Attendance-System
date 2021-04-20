<?php
    class Utils {

        public static function convertDBRecordstoArray(object $records): array {
            $result = array();
            while($row = mysqli_fetch_assoc($records)) {
                array_push($result, $row);
            }
            return $result;
        }

        public static function generatePassword(): string {
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
        
        public static function showAccount(string $email_id, array $account_details): void {
            echo "<div>Email ID: $email_id</div>";
            foreach ($account_details as $key => $value) {
                if (is_numeric($key)) continue;
                $key = ucwords(str_replace("_", " ", $key));
                echo "<div>$key: $value</div>";
            }
        }

        private static function validatePassword(string $password): bool {
            $containLength = strlen($password) > 7 and strlen($password) < 13;
            $containUpper = preg_match('/[A-Z]/', $password);
            $containLower = preg_match('/[a-z]/', $password);
            $containNumber = preg_match('/[0-9]/', $password);
            $containSpecial = preg_match('/[@#$_\-+*&]/', $password);
            
            if ($containLength and $containLower and $containNumber and $containSpecial and $containUpper) {
                return TRUE;
            }
            else {
                return False;
            }
        }
        
        public static function changePassword(): ?array {
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
                            if (Utils::validatePassword($new_pass)) {
                                return array('old_pass' => $old_pass, 'new_pass' => $new_pass);
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
            return null;
        }

        public static function createForm($con, $role) {
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
                $form_data['password'] = generatePassword();
                return $form_data;
            }
        }
        
    }
?>