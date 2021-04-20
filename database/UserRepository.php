<?php
    require_once ('DBConnector.php');

    class UserRepository extends DBConnector {

        public function existById(string $id): bool {
            $result=mysqli_query($this->conn, "SELECT * FROM user WHERE email_id='$id'");
            if (mysqli_num_rows($result) != 1) {
                return false;
            }
            else {
                return true;
            }
        }
        
        public function findUserIdByEmailIdandPasswordandRole(string $email_id, string $password, string $role): ?int {
            $result=mysqli_query($this->conn, "SELECT user_id FROM user WHERE email_id='$email_id' AND password='$password' AND role='$role'");
            if (mysqli_num_rows($result) != 1) {
                return null;
            }
            else {
                return mysqli_fetch_assoc($result)['user_id'];
            }
        }

        public function updatePasswordByEmailId(string $email_id, string $password): bool {
            return mysqli_query($this->conn, "UPDATE user SET password = '$password' WHERE email_id = '$email_id'");
        }

        public function findIdByUserIdAndRole(int $user_id, string $role): ?string {
            $result=mysqli_query($this->conn, "SELECT email_id FROM user WHERE user_id='$user_id' AND role='$role'");
            if (mysqli_num_rows($result) != 1) {
                return null;
            }
            else {
                return mysqli_fetch_assoc($result)['email_id'];
            }
        }

        public function findIdByUserIdAndPasswordAndRole(int $user_id, string $password, string $role): ?string {
            $result=mysqli_query($this->conn, "SELECT email_id FROM user WHERE user_id='$user_id' AND password='$password' AND role='$role'");
            if (mysqli_num_rows($result) != 1) {
                return null;
            }
            else {
                return mysqli_fetch_assoc($result)['email_id'];
            }
        }

    }
?>