<?php
    session_set_cookie_params(604800, "/");
	session_start();
    include ('server/service/TeacherService.php');
    
    class TeacherController {

        private function authenticate(): void {
            if (!(array_key_exists('role', $_SESSION) && UserRole::isValid(trim($_SESSION['role'])))) {
                throw new ResponseException(ExceptionMSG::AUTHENTICATION_REQUIRED, 401);
            }
            elseif (UserRole::TEACHER !== trim($_SESSION['role'])) {
                throw new ResponseException(ExceptionMSG::FORBIDDEN, 403);
            }
        }
        
        public function getAttendanceAndTimetable(array $input): ResponseEntity {
            $this->authenticate();
            $teacher_id = trim($_SESSION['user_id']);
            $teacher_service = new TeacherService();
            $result = $teacher_service->getAttendanceAndTimetable($teacher_id);
            return new ResponseEntity(json_encode($result));
        }

        public function getAccountDetails(array $input): ResponseEntity {
            $this->authenticate();
            $teacher_id = trim($_SESSION['user_id']);
            $email = trim($_SESSION['email']);
            $teacher_service = new TeacherService();
            $result = $teacher_service->getAccountDetails($teacher_id, $email);
            return new ResponseEntity(json_encode($result));
        }

        public function getStudentDetails(array $input): ResponseEntity {
            $this->authenticate();
            
            if (array_key_exists("filterBy", $input) && array_key_exists("value", $input)) {
                $filter_by = trim($input['filterBy']);
                $value = trim($input['value']);
                
                if ($filter_by != "" && $value != "") {
                    $teacher_service = new TeacherService();
                    $result = $teacher_service->getStudentDetails($filter_by, $value);
                    return new ResponseEntity(json_encode($result));
                }
                else {
                    $message = Utils::constructMSG(ExceptionMSG::INVALID_DATA, "Filter By or Value");
                    throw new ResponseException($message, 406);
                }
            }
            else {
                $message = Utils::constructMSG(ExceptionMSG::INCOMPLETE_DATA, "Filter By or Value");
                    throw new ResponseException($message, 406);
            }
        }

        public function getClassDetails(array $input): ResponseEntity {
            $this->authenticate();
            $teacher_id = trim($_SESSION['user_id']);
            $teacher_service = new TeacherService();
            $result = $teacher_service->getClassDetails($teacher_id);
            return new ResponseEntity(json_encode($result));
        }

        public function getStudentAttendance(array $input): ResponseEntity {
            $this->authenticate();
            
            if (array_key_exists("student_id", $input) && array_key_exists("from_date", $input) && array_key_exists("to_date", $input)) {
                $student_id = trim($input['student_id']);
                $from_date = trim($input['from_date']);
                $to_date = trim($input['to_date']);
                
                if ($student_id != "" && Utils::isDate($from_date) && Utils::isDate($to_date) && $from_date <= $to_date) {
                    $teacher_id = trim($_SESSION['user_id']);
                    $teacher_service = new TeacherService();
                    $result = $teacher_service->getStudentAttendance($teacher_id, $student_id, $from_date, $to_date);
                    return new ResponseEntity(json_encode($result));
                }
                else {
                    throw new ResponseException("Invalid Data!", 406);
                }
            }
            else {
                throw new ResponseException("Incomplete Data!", 406);
            }
        }

        public function getPeriodSize(array $input): ResponseEntity {
            $this->authenticate();
            $teacher_id = trim($_SESSION['user_id']);
            $teacher_service = new TeacherService();
            $period_size = $teacher_service->getPeriodSize($teacher_id);
            return new ResponseEntity('{"period_size": "'.$period_size.'"}');
        }

        public function getAttendedClass(array $input): ResponseEntity {
            $this->authenticate();
            
            if (array_key_exists("period", $input) ) {
                $period = trim($input['period']);
                
                if ($period != "") {
                    $teacher_id = trim($_SESSION['user_id']);
                    $teacher_service = new TeacherService();
                    $result = $teacher_service->getAttendedClass($teacher_id, $period);
                    return new ResponseEntity(json_encode($result));
                }
                else {
                    throw new ResponseException("Invalid Data!", 406);
                }
            }
            else {
                throw new ResponseException("Incomplete Data!", 406);
            }
        }

        public function applyAttendance(array $input): ResponseEntity {
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
                    return new ResponseEntity('{"success": "Attendance added successfully!"}');
                }
                else {
                    throw new ResponseException("Invalid Data!", 406);
                }
            }
            else {
                throw new ResponseException("Incomplete Data!", 406);
            }
        }

        public function getStudentAttachment(string $leave_id): array {
            $this->authenticate();
            if (!ctype_digit($leave_id)) {
                $message = Utils::constructMSG(ExceptionMSG::INVALID_DATA, "Leave ID");
                throw new ResponseException($message, 406);
            }
            $teacher_id = trim($_SESSION['user_id']);
            $teacher_service = new TeacherService();
            $path = $teacher_service->getStudentAttachment($leave_id, $teacher_id);
            $content_type = LeaveAttachment::CONTENT_TYPE[pathinfo($path, PATHINFO_EXTENSION)];
            return array("path" => $path, "content-type" => $content_type);
        }

    }
?>