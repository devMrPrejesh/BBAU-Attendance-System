<?php
    include ('server/repository/UserRepository.php');
    include ('server/repository/HolidayRepository.php');
    
    class UserService {

        public function changeEmail(string $old_email, string $new_email): void {
            $user_repository = new UserRepository();
            if ($user_repository->existById($new_email)) {
                throw new ResponseException(ExceptionMSG::INVALID_NEW_EMAIL, 406);
            }
            else {
                $user_repository->updateIdById($old_email, $new_email);
            }
        }

        public function changePassword(string $email, string $old_password, string $new_password): void {
            $user_repository = new UserRepository();
            if ($user_repository->existByIdAndPassword($email, $old_password)) {
                $user_repository->updatePasswordById($email, $new_password);
            }
            else {
                throw new ResponseException(ExceptionMSG::INVALID_OLD_PASSWORD, 406);
            }
        }

        public function getHoliday(): array {
            $holiday_repository = new HolidayRepository();
            return $holiday_repository->getAllOrderById();
        }

        public function getProfilePath(string $email): string {
            $user_repository = new UserRepository();
            $result = $user_repository->findProfilePhotoByID($email);
            
            if ($result != null) {
                return UserProfile::PATH.$result;
            }
            else {
                return UserProfile::DEFAULT_PROFILE;
            }
        }
        
        public function login(string $email, string $password, string $role): array {
            $user_repository = new UserRepository();
            $result = $user_repository->findUserIdAndFirstNameByIdAndPasswordandRole($email, $password, $role);
            
            if ($result != null) {
                return $result;
            }
            else {
                $message = Utils::constructMSG(ExceptionMSG::INVALID_DATA, "Email-ID, Password or Role");
                throw new ResponseException($message, 406);
            }
        }
        
        public function resetPassword(string $email): void {
            $user_repository = new UserRepository();
            if ($user_repository->existById($email)) {
				$password = Utils::generatePassword();
				$user_repository->updatePasswordById($email, $password);
                $substitute = array(
                    "{password}" => $password, 
                    "{redirect_link}" => RedirectUrl::RESET_PASSWORD
                );
                
				if (!Utils::sendMail($_POST['email'], "Reset Password", MailTemplate::RESET_PASSWORD, TRUE, $substitute)) {
                    throw new ResponseException(ExceptionMSG::MAIL_FAILURE, 501);
                }
			}
			else {
                $message = Utils::constructMSG(ExceptionMSG::INVALID_DATA, "Email-ID");
                throw new ResponseException($message, 406);
            }
        }
        
    }
?>