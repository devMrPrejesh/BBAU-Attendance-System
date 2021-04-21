<?php
    require_once ('DBConnector.php');

    class UserRepository extends DBConnector {

        public function existById(string $email_id): bool {
            $query = "SELECT * FROM user WHERE email_id='$email_id'";
            $result = mysqli_query($this->conn, $query);
            if (mysqli_num_rows($result) == 1) {
                return true;
            }
            else {
                return false;
            }
        }
        
        public function findUserIdByIdAndPasswordandRole(string $email_id, string $password, string $role): ?int {
            $query = "SELECT user_id FROM user WHERE email_id='$email_id' AND password='$password' AND role='$role'";
            $result = mysqli_query($this->conn, $query);
            if (mysqli_num_rows($result) == 1) {
                return mysqli_fetch_assoc($result)['user_id'];
            }
            else {
                return null;
            }
        }

        public function updatePasswordById(string $email_id, string $password): void {
            $query = "UPDATE user SET password = '$password' WHERE email_id = '$email_id'";
            mysqli_query($this->conn, $query);
        }

        public function findIdByUserIdAndRole(int $user_id, string $role): ?string {
            $query = "SELECT email_id FROM user WHERE user_id='$user_id' AND role='$role'";
            $result = mysqli_query($this->conn, $query);
            if (mysqli_num_rows($result) == 1) {
                return mysqli_fetch_assoc($result)['email_id'];
            }
            else {
                return null;
            }
        }

        public function findIdByUserIdAndPasswordAndRole(int $user_id, string $password, string $role): ?string {
            $query = "SELECT email_id FROM user WHERE user_id='$user_id' AND password='$password' AND role='$role'";
            $result = mysqli_query($this->conn, $query);
            if (mysqli_num_rows($result) == 1) {
                return mysqli_fetch_assoc($result)['email_id'];
            }
            else {
                return null;
            }
        }

    }
?>