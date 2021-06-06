<?php
    session_set_cookie_params(604800, "/");
	session_start();
    include ('server/service/UserService.php');
    
    class UserController {

        public function changeEmail(array $input): ResponseEntity {
            if (array_key_exists('role', $_SESSION) && UserRole::isValid(trim($_SESSION['role']))) {
                if (array_key_exists("new_email", $input)) {
                    $old_email = trim($_SESSION['email']);
                    $new_email = trim($input['new_email']);

                    if (Utils::isEmail($new_email)) {
                        $user_service = new UserService();
                        $result = $user_service->changeEmail($old_email, $new_email);
                        $_SESSION['email'] = $new_email;
                        return new ResponseEntity(ResponseMSG::CHANGE_EMAIL);
                    }
                    else {
                        $message = Utils::constructMSG(ExceptionMSG::INVALID_DATA, "Email");
                        throw new ResponseException($message, 406);
                    }
                }
                else {
                    $message = Utils::constructMSG(ExceptionMSG::INCOMPLETE_DATA, "Email");
                    throw new ResponseException($message, 406);
                }
            }
            else {
                throw new ResponseException(ExceptionMSG::AUTHENTICATION_REQUIRED, 401);
            }
        }

        public function changePassword(array $input): ResponseEntity {
            if (array_key_exists('role', $_SESSION) && UserRole::isValid(trim($_SESSION['role']))) {
                if (array_key_exists("old_password", $input) && array_key_exists("new_password", $input)) {
                    $old_password = trim($input['old_password']);
                    $new_password = trim($input['new_password']);
                    $email = trim($_SESSION['email']);

                    if ($old_password != "" && Utils::validatePassword($new_password)) {
                        $user_service = new UserService();
                        $result = $user_service->changePassword($email, $old_password, $new_password);
                        return new ResponseEntity(ResponseMSG::CHANGE_PASSWORD);
                    }
                    else {
                        $message = Utils::constructMSG(ExceptionMSG::INVALID_DATA, "Password");
                        throw new ResponseException($message, 406);
                    }
                }
                else {
                    $message = Utils::constructMSG(ExceptionMSG::INCOMPLETE_DATA, "Password");
                    throw new ResponseException($message, 406);
                }
            }
            else {
                throw new ResponseException(ExceptionMSG::AUTHENTICATION_REQUIRED, 401);
            }
        }

        public function getHoliday(array $input): ResponseEntity {
            if (array_key_exists('role', $_SESSION) && UserRole::isValid(trim($_SESSION['role']))) {
                $user_service = new UserService();
                $result = $user_service->getHoliday();
                return new ResponseEntity(json_encode($result));
            }
            else {
                throw new ResponseException(ExceptionMSG::AUTHENTICATION_REQUIRED, 401);
            }
        }

        public function getProfilePath(): array {
            if (array_key_exists('role', $_SESSION) && UserRole::isValid(trim($_SESSION['role']))) {
                $email = trim($_SESSION['email']);
                $user_service = new UserService();

                $path = $user_service->getProfilePath($email);
                $content_type = UserProfile::CONTENT_TYPE[pathinfo($path, PATHINFO_EXTENSION)];
                
                return array("path" => $path, "content-type" => $content_type);
            }
            else {
                throw new ResponseException(ExceptionMSG::AUTHENTICATION_REQUIRED, 401);
            }
        }
        
        public function login(array $input): ResponseEntity {
            if (array_key_exists('role', $_SESSION) && UserRole::isValid(trim($_SESSION['role']))) {
                $message = Utils::constructMSG(ExceptionMSG::ALREADY_LOGGED, $_SESSION['email']);
                throw new ResponseException($message, 409);
            }
            
            if (array_key_exists('email', $input) && array_key_exists('password', $input) && array_key_exists('role', $input)) {
                $email = trim($input['email']);
                $password = trim($input['password']);
                $role = trim($input['role']);

                if (Utils::isEmail($email) && $password != "" && UserRole::isValid($role)) {
                    $user_service = new UserService();
                    $result = $user_service->login($email, $password, $role);

                    $_SESSION['user_id'] = $result['user_id'];
                    $_SESSION['role'] = $role;
                    $_SESSION['email'] = $email;
                    $_SESSION['first_name'] = $result['first_name'];

                    return new ResponseEntity(Utils::constructMSG(ResponseMSG::REDIRECT_URL, constant("RedirectUrl::$role")));
                }
                else {
                    $message = Utils::constructMSG(ExceptionMSG::INVALID_DATA, "Email-ID, Password or Role");
                    throw new ResponseException($message, 406);
                }
            }
            else {
                $message = Utils::constructMSG(ExceptionMSG::INCOMPLETE_DATA, "Email-ID, Password or Role");
                throw new ResponseException($message, 406);
            }
        }

        public function logout(array $input): ResponseEntity {
            if (array_key_exists('role', $_SESSION) && UserRole::isValid(trim($_SESSION['role']))) {
                session_destroy();
                return new ResponseEntity(Utils::constructMSG(ResponseMSG::REDIRECT_URL, RedirectUrl::LOGIN));
            }
            else {
                throw new ResponseException(ExceptionMSG::AUTHENTICATION_REQUIRED, 401);
            }
        }

        public function resetPassword(array $input): ResponseEntity {
            if (array_key_exists('email', $input)) {
                $email = trim($input['email']);

                if (Utils::isEmail($email)) {
                    $user_service = new UserService();
                    $user_service->resetPassword($email);
                    return new ResponseEntity(ResponseMSG::RESET_PASSWORD);
                }
                else {
                    $message = Utils::constructMSG(ExceptionMSG::INVALID_DATA, "Email-ID");
                    throw new ResponseException($message, 406);
                }
            }
            else {
                $message = Utils::constructMSG(ExceptionMSG::INCOMPLETE_DATA, "Email-ID");
                throw new ResponseException($message, 406);
            }
        }

    }
?>
