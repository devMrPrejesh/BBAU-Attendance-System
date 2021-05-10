<?php
    session_set_cookie_params(604800, "/");
	session_start();
    include ('server/service/TeacherService.php');
    include ('server/Constants.php');
    
    class TeacherController {

        private function authenticate(): void {
            if (!(array_key_exists('role', $_SESSION) && UserRole::TEACHER === trim($_SESSION['role']))) {
                throw new ResponseException("Unauthorised Access!", 403);
            }
        }
        
        public function getAttendanceAndTimetable(array $input): string {
            $this->authenticate();
            $teacher_id = trim($_SESSION['user_id']);
            $teacher_service = new TeacherService();
            $result = $teacher_service->getAttendanceAndTimetable($teacher_id);
            return json_encode($result);
        }

        public function getAccountDetails(array $input): string {
            $this->authenticate();
            $teacher_id = trim($_SESSION['user_id']);
            $email = trim($_SESSION['email']);
            $teacher_service = new TeacherService();
            $result = $teacher_service->getAccountDetails($teacher_id, $email);
            return json_encode($result);
        }

        public function setAccountDetails(array $input): string {
            $this->authenticate();
            
            if (empty($input)) {
                throw new ResponseException("Empty Data array", 406);
            }

            foreach ($input as $key => $value) {
                if (!EditableTeacherDetails::isValid($key) || trim($value) === "") {
                    throw new ResponseException("Invalid Data", 406);
                }
            }

            if (array_key_exists("email", $input) && !Utils::isEmail($input['email'])) {
                throw new ResponseException("Invalid Data", 406);
            }

            $teacher_id = trim($_SESSION['user_id']);
            $email = trim($_SESSION['email']);
            $teacher_service = new TeacherService();
            $teacher_service->setAccountDetails($teacher_id, $email, $input);
            
            if (array_key_exists("email", $input)) {
                $_SESSION['email'] = $input['email'];
            }
            return '{"success": "Data updated successfully."}';
        }

        public function getStudentDetails(array $input): string {
            $this->authenticate();
            
            if (array_key_exists("filterBy", $input) && array_key_exists("value", $input)) {
                $filter_by = trim($input['filterBy']);
                $value = trim($input['value']);
                
                if ($filter_by != "" && $value != "") {
                    $teacher_service = new TeacherService();
                    $result = $teacher_service->getStudentDetails($filter_by, $value);
                    return json_encode($result);
                }
                else {
                    throw new ResponseException("Invalid Data!", 406);
                }
            }
            else {
                throw new ResponseException("Incomplete Data!", 406);
            }
        }

        public function getClassDetails(array $input): string {
            $this->authenticate();
            $teacher_id = trim($_SESSION['user_id']);
            $teacher_service = new TeacherService();
            $result = $teacher_service->getClassDetails($teacher_id);
            return json_encode($result);
        }

        public function getStudentAttendance(array $input): string {
            $this->authenticate();
            
            if (array_key_exists("student_id", $input) && array_key_exists("from_date", $input) && array_key_exists("to_date", $input)) {
                $student_id = trim($input['student_id']);
                $from_date = trim($input['from_date']);
                $to_date = trim($input['to_date']);
                
                if ($student_id != "" && Utils::isDate($from_date) && Utils::isDate($to_date) && $from_date <= $to_date) {
                    $teacher_id = trim($_SESSION['user_id']);
                    $teacher_service = new TeacherService();
                    $result = $teacher_service->getStudentAttendance($teacher_id, $student_id, $from_date, $to_date);
                    return json_encode($result);
                }
                else {
                    throw new ResponseException("Invalid Data!", 406);
                }
            }
            else {
                throw new ResponseException("Incomplete Data!", 406);
            }
        }

        public function getPeriodSize(array $input): string {
            $this->authenticate();
            $teacher_id = trim($_SESSION['user_id']);
            $teacher_service = new TeacherService();
            $period_size = $teacher_service->getPeriodSize($teacher_id);
            return '{"period_size": "'.$period_size.'"}';
        }

        public function getAttendedClass(array $input): string {
            $this->authenticate();
            
            if (array_key_exists("period", $input) ) {
                $period = trim($input['period']);
                
                if ($period != "") {
                    $teacher_id = trim($_SESSION['user_id']);
                    $teacher_service = new TeacherService();
                    $result = $teacher_service->getAttendedClass($teacher_id, $period);
                    return json_encode($result);
                }
                else {
                    throw new ResponseException("Invalid Data!", 406);
                }
            }
            else {
                throw new ResponseException("Incomplete Data!", 406);
            }
        }

        public function applyAttendance(array $input): string {
            $this->authenticate();
            
            if (array_key_exists("period", $input)) {
                $period = trim($input['period']);
                unset($input['period']);
                $class = null;
                if (array_key_exists("class", $input)) {
                    unset($input['class']);
                    $class = trim($input['class']);
                }
                
                if ($period != "") {
                    $teacher_id = trim($_SESSION['user_id']);
                    $teacher_service = new TeacherService();
                    $teacher_service->applyAttendance($teacher_id, $period, $input, $class);
                    return '{"success": "Attendance added successfully!"}';
                }
                else {
                    throw new ResponseException("Invalid Data!", 406);
                }
            }
            else {
                throw new ResponseException("Incomplete Data!", 406);
            }
        }

    }
?>