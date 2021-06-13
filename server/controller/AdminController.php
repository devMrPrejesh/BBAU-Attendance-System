<?php
    session_start();
    include ('server/service/AdminService.php');
    
    class AdminController {

        private function authenticate(): void {
            if (!(array_key_exists('role', $_SESSION) && UserRole::isValid(trim($_SESSION['role'])))) {
                throw new ResponseException(ExceptionMSG::AUTHENTICATION_REQUIRED, 401);
            }
            elseif (UserRole::ADMIN !== trim($_SESSION['role'])) {
                throw new ResponseException(ExceptionMSG::FORBIDDEN, 403);
            }
        }

        public function insertStudent(array $input): ResponseEntity {
            $this->authenticate();
            if (array_key_exists('id', $input) && array_key_exists('name', $input) && 
            array_key_exists('class', $input) && array_key_exists('teacher_id', $input) && 
            array_key_exists('email', $input)) {
                $student_id = trim($input['id']);
                $name = trim($input['name']);
                $class = trim($input['class']);
                $teacher_id = trim($input['teacher_id']);
                $email = trim($input['email']);

                if (ctype_digit($student_id) && $name != "" && $class != "" && ctype_digit($teacher_id) && 
                Utils::isEmail($email)) {
                    $admin_service = new AdminService();
                    $admin_service->insertStudent($student_id, $name, $class, $teacher_id, $email);
                    return new ResponseEntity(Utils::constructMSG(ResponseMSG::USER_ADDED, "Student"));
                }
                else {
                    $message = Utils::constructMSG(ExceptionMSG::INVALID_DATA, "Data");
                    throw new ResponseException($message, 406);
                }
            }
            else {
                $message = Utils::constructMSG(ExceptionMSG::INCOMPLETE_DATA, "Data");
                throw new ResponseException($message, 406);
            }
        }

        public function insertTeacher(array $input): ResponseEntity {
            $this->authenticate();
            if (array_key_exists('id', $input) && array_key_exists('name', $input) && 
            array_key_exists('period_size', $input) && array_key_exists('email', $input)) {
                $teacher_id = trim($input['id']);
                $name = trim($input['name']);
                $class = NULL;
                $period_size = trim($input['period_size']);
                $email = trim($input['email']);

                if (ctype_digit($teacher_id) && $name != "" && ctype_digit($period_size) && 
                Utils::isEmail($email)) {
                    if (array_key_exists('class', $input) && trim($input['class']) != "") {
                        $class =  trim($input['class']);
                    }
                    $admin_service = new AdminService();
                    $admin_service->insertTeacher($teacher_id, $name, $period_size, $email, $class);
                    return new ResponseEntity(Utils::constructMSG(ResponseMSG::USER_ADDED, "Teacher"));
                }
                else {
                    $message = Utils::constructMSG(ExceptionMSG::INVALID_DATA, "Data");
                    throw new ResponseException($message, 406);
                }
            }
            else {
                $message = Utils::constructMSG(ExceptionMSG::INCOMPLETE_DATA, "Data");
                throw new ResponseException($message, 406);
            }
        }
        
    }
?>
