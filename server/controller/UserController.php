<?php
    session_set_cookie_params(604800, "/");
	session_start();
    include ('server/service/UserService.php');
    include ('server/Constants.php');
    
    class UserController {
        
        public function login(array $input): string {
            if (array_key_exists('role', $_SESSION) && UserRole::isValid(trim($_SESSION['role']))) {
                throw new ResponseException("You have already logged in ".$_SESSION['email'], 409);
            }
            if (array_key_exists('email', $input) && array_key_exists('password', $input) && array_key_exists('role', $input)) {
                $email = strtolower(trim($input['email']));
                $password = trim($input['password']);
                $role = trim($input['role']);

                if (Utils::isEmail($email) && $password != "" && UserRole::isValid($role)) {
                    $user_service = new UserService();
                    $result = $user_service->login($email, $password, $role);

                    $_SESSION['user_id'] = $result['user_id'];
                    $_SESSION['role'] = $role;
                    $_SESSION['email'] = $email;
                    $_SESSION['first_name'] = $result['first_name'];

                    return '{"redirect": "'.strtolower($role).'.php"}';
                }
                else {
                    throw new ResponseException("Invalid credentials!", 406);
                }
            }
            else {
                throw new ResponseException("Incomplete credentials!", 406);
            }
        }

        public function resetPassword(array $input): string {
            if (array_key_exists('email', $input)) {
                $email = strtolower(trim($input['email']));

                if (Utils::isEmail($email)) {
                    $user_service = new UserService();
                    $result = $user_service->resetPassword($email);
                    
                    return '{"success": "Your password has been reset.<br>Please check your mail."}';
                }
                else {
                    throw new ResponseException("Invalid credentials!", 406);
                }
            }
            else {
                throw new ResponseException("Incomplete credentials!", 406);
            }
        }

        public function logout(array $input): string {
            if (array_key_exists('role', $_SESSION) && UserRole::isValid(trim($_SESSION['role']))) {
                session_destroy();
                return '{"redirect": "index.php"}';
            }
            else {
                throw new ResponseException("You are not logged in!", 401);
            }
        }

        public function getHoliday(array $input): string {
            if (array_key_exists('role', $_SESSION) && UserRole::isValid(trim($_SESSION['role']))) {
                $user_service = new UserService();
                $result = $user_service->getHoliday();
                return json_encode($result);
            }
            else {
                throw new ResponseException("Unauthorised Access!", 403);
            }
        }

        public function changePassword(array $input): string {
            if (array_key_exists('role', $_SESSION) && UserRole::isValid(trim($_SESSION['role']))) {
                if (array_key_exists("old_password", $input) && array_key_exists("old_password", $input)) {
                    $old_password = trim($input['old_password']);
                    $new_password = trim($input['new_password']);
                    $email = trim($_SESSION['email']);

                    if ($old_password != "" && Utils::validatePassword($new_password)) {
                        $user_service = new UserService();
                        $result = $user_service->changePassword($email, $old_password, $new_password);
                        return '{"success": "Password updated successfully."}';
                    }
                    else {
                        throw new ResponseException("Invalid Data!", 406);
                    }
                }
                else {
                    throw new ResponseException("Incomplete Data!", 406);
                }
            }
            else {
                throw new ResponseException("Unauthorised Access!", 403);
            }
        }

    }
?>