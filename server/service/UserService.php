<?php
    include ('server/repository/UserRepository.php');
    include ('server/repository/HolidayRepository.php');
    
    class UserService {
        
        public function login(string $email, string $password, string $role): array {
            $user_repository = new UserRepository();
            $result = $user_repository->findUserIdAndFirstNameByIdAndPasswordandRole($email, $password, $role);
            unset($user_repository);
            if ($result != null) {
                return $result;
            }
            else {
                throw new ResponseException("Incorrect credentials!", 401);
            }
        }
        
        public function resetPassword(string $email): void {
            $user_repository = new UserRepository();
            if ($user_repository->existById($email)) {
				$password = Utils::generatePassword();
				$user_repository->updatePasswordById($email, $password);
                $source = "server/mail_templates/reset_password_template.html";
                $substitute = array(
                    "{password}" => $password, 
                    "{redirect_link}" => "http://localhost/university-attendance-system/index.php"
                );
                
				if (!Utils::sendMail($_POST['email'], "Reset Password", $source, TRUE, $substitute)) {
                    unset($user_repository);
                    throw new ResponseException("Mail can't be send!",501);
                }
			}
			else {
                unset($user_repository);
                throw new ResponseException("Incorrect credentials!", 401);
            }
        }

        public function getHoliday(): array {
            $holiday_repository = new HolidayRepository();
            return $holiday_repository->getAllOrderById();
        }

        public function changePassword(string $email, string $old_password, string $new_password): void {
            $user_repository = new UserRepository();
            if ($user_repository->existByIdAndPassword($email, $old_password)) {
                $user_repository->updatePasswordById($email, $new_password);
            }
            else {
                throw new ResponseException("Invalid Old Password!", 401);
            }
        }
        
    }
?>