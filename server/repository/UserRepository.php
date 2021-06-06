<?php
    require_once ('DBConnector.php');

    class UserRepository extends DBConnector {

        public function existById(string $email): bool {
            $query = "SELECT * FROM user WHERE email='$email'";
            $result = mysqli_query($this->conn, $query);
            if (mysqli_num_rows($result) == 1) {
                return TRUE;
            }
            else {
                return FALSE;
            }
        }

        public function existByIdAndPassword(string $email, string $password): bool {
            $query = "SELECT * FROM user WHERE email='$email' AND BINARY password='$password'";
            $result = mysqli_query($this->conn, $query);
            if (mysqli_num_rows($result) == 1) {
                return TRUE;
            }
            else {
                return FALSE;
            }
        }

        public function findIdByUserIdAndRole(string $user_id, string $role): ?string {
            $query = "SELECT email FROM user WHERE user_id='$user_id' AND role='$role'";
            $result = mysqli_query($this->conn, $query);
            if (mysqli_num_rows($result) == 1) {
                return mysqli_fetch_assoc($result)['email'];
            }
            else {
                return null;
            }
        }

        public function findProfilePhotoByID(string $email): ?string {
            $query = "SELECT profile_photo FROM user WHERE email = '$email'";
            $result = mysqli_query($this->conn, $query);
            if (mysqli_num_rows($result) == 1) {
                return mysqli_fetch_assoc($result)['profile_photo'];
            }
            else {
                return null;
            }
        }
        
        public function findUserIdAndFirstNameByIdAndPasswordAndRole(string $email, string $password, string $role): ?array {
            $query = "SELECT user_id, first_name FROM user WHERE email='$email' AND BINARY password='$password' AND role='$role'";
            $result = mysqli_query($this->conn, $query);
            if (mysqli_num_rows($result) == 1) {
                return $this->convertDBRecordstoArray($result)[0];
            }
            else {
                return null;
            }
        }

        public function updateIdById(string $email, string $new_email): void {
            $query = "UPDATE user SET email = '$new_email' WHERE email='$email'";
            mysqli_query($this->conn, $query);
        }

        public function updatePasswordById(string $email, string $password): void {
            $query = "UPDATE user SET password = '$password' WHERE email='$email'";
            mysqli_query($this->conn, $query);
        }

    }
?>
