<?php
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

        public function applyAttendance(array $input): ResponseEntity {
            $this->authenticate();
            if (!Utils::isNotWeekend(date("Y-m-d"))) { throw new ResponseException(ExceptionMSG::WEEKEND, 406); }
            
            if (array_key_exists("period", $input)) {
                $period = trim($input['period']);
                unset($input['period']);
                $class = NULL;
                
                if ($period != "") {
                    if (array_key_exists("class", $input)) {
                        if (trim($input['class']) != "") { $class = trim($input['class']); }
                        unset($input['class']);
                    }
                    $teacher_id = trim($_SESSION['user_id']);
                    $teacher_service = new TeacherService();
                    $teacher_service->applyAttendance($teacher_id, $period, $input, $class);
                    return new ResponseEntity(ResponseMSG::ATTENDANCE_ADDED, 201);
                }
                else {
                    $message = Utils::constructMSG(ExceptionMSG::INVALID_DATA, "Period");
                    throw new ResponseException($message, 406);
                }
            }
            else {
                $message = Utils::constructMSG(ExceptionMSG::INCOMPLETE_DATA, "Period or Class");
                throw new ResponseException($message, 406);
            }
        }

        public function decideLeave(array $input): ResponseEntity {
            $this->authenticate();
            if (array_key_exists("leave_id", $input) && array_key_exists("value", $input) && array_key_exists("remark", $input)) {
                $leave_id = trim($input['leave_id']);
                $value = trim($input['value']);
                $remark = trim($input['remark']);
                
                if (ctype_digit($leave_id) && in_array($value, LeaveDecide::FINAL_STATUS) && $remark != "") {
                    $teacher_service = new TeacherService();
                    $teacher_service->decideLeave($leave_id, $value, $remark);
                    $meesage = Utils::constructMSG(ResponseMSG::LEAVE_DECIDE, strtolower($value));
                    return new ResponseEntity($meesage);
                }
                else {
                    $message = Utils::constructMSG(ExceptionMSG::INVALID_DATA, "Leave ID, Remark or Value");
                    throw new ResponseException($message, 406);
                }
            }
            else {
                $message = Utils::constructMSG(ExceptionMSG::INCOMPLETE_DATA, "Leave ID, Remark or Value");
                throw new ResponseException($message, 406);
            }
        }

        public function getAccountDetails(array $input): ResponseEntity {
            $this->authenticate();
            $teacher_id = trim($_SESSION['user_id']);
            $email = trim($_SESSION['email']);
            $teacher_service = new TeacherService();
            $result = $teacher_service->getAccountDetails($teacher_id, $email);
            return new ResponseEntity(json_encode($result));
        }
        
        public function getAttendanceAndTimetable(array $input): ResponseEntity {
            $this->authenticate();
            $teacher_id = trim($_SESSION['user_id']);
            $teacher_service = new TeacherService();
            $result = $teacher_service->getAttendanceAndTimetable($teacher_id);
            return new ResponseEntity(json_encode($result));
        }

        public function getAttendedClass(array $input): ResponseEntity {
            $this->authenticate();
            if (!Utils::isNotWeekend(date("Y-m-d"))) { throw new ResponseException(ExceptionMSG::WEEKEND, 406); }
            
            if (array_key_exists("period", $input)) {
                $period = trim($input['period']);
                $class = NULL;

                if ($period != "") {
                    if (array_key_exists("class", $input) && trim($input['class']) != "") { $class = trim($input['class']); }
                    $teacher_id = trim($_SESSION['user_id']);
                    $teacher_service = new TeacherService();
                    $result = $teacher_service->getAttendedClass($teacher_id, $period, $class);
                    return new ResponseEntity(json_encode($result));
                }
                else {
                    $message = Utils::constructMSG(ExceptionMSG::INVALID_DATA, "Period");
                    throw new ResponseException($message, 406);
                }
            }
            else {
                $message = Utils::constructMSG(ExceptionMSG::INCOMPLETE_DATA, "Period or Class");
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

        public function getLeave(array $input): ResponseEntity {
            $this->authenticate();
            $teacher_id = trim($_SESSION['user_id']);
            $teacher_service = new TeacherService();
            $result = $teacher_service->getLeave($teacher_id);
            return new ResponseEntity(json_encode($result));
        }

        public function getLeaveAttachment(string $leave_id): array {
            $this->authenticate();
            if (!ctype_digit($leave_id)) {
                $message = Utils::constructMSG(ExceptionMSG::INVALID_DATA, "Leave ID");
                throw new ResponseException($message, 406);
            }
            $teacher_id = trim($_SESSION['user_id']);
            $teacher_service = new TeacherService();
            $path = $teacher_service->getLeaveAttachment($leave_id, $teacher_id);
            $content_type = LeaveAttachment::CONTENT_TYPE[pathinfo($path, PATHINFO_EXTENSION)];
            return array("path" => $path, "content-type" => $content_type);
        }

        public function getPeriodSize(array $input): ResponseEntity {
            $this->authenticate();
            $teacher_id = trim($_SESSION['user_id']);
            $teacher_service = new TeacherService();
            $period_size = $teacher_service->getPeriodSize($teacher_id);
            return new ResponseEntity('{"period_size": "'.$period_size.'"}');
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
                    $message = Utils::constructMSG(ExceptionMSG::INVALID_DATA, "Student ID, From or To dates");
                    throw new ResponseException($message, 406);
                }
            }
            else {
                $message = Utils::constructMSG(ExceptionMSG::INCOMPLETE_DATA, "Student ID, From or To dates");
                throw new ResponseException($message, 406);
            }
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

        public function updateInLeave(array $input): ResponseEntity {
            $this->authenticate();
            $teacher_id = trim($_SESSION['user_id']);
            $teacher_service = new TeacherService();
            $result = $teacher_service->updateInLeave($teacher_id);
            return new ResponseEntity(json_encode($result));
        }

    }
?>
