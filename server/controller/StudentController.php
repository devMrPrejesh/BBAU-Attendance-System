<?php
    session_set_cookie_params(604800, "/");
	session_start();
    include ('server/service/StudentService.php');
    
    class StudentController {

        private function authenticate(): void {
            if (!(array_key_exists('role', $_SESSION) && UserRole::isValid(trim($_SESSION['role'])))) {
                throw new ResponseException(ExceptionMSG::AUTHENTICATION_REQUIRED, 401);
            }
            elseif (UserRole::STUDENT !== trim($_SESSION['role'])) {
                throw new ResponseException(ExceptionMSG::FORBIDDEN, 403);
            }
        }

        public function applyLeave(array $input): ResponseEntity {
            $this->authenticate();
            if (array_key_exists('reason', $input) && array_key_exists('from', $input) && array_key_exists('to', $input)) {
                $reason = trim($input['reason']);
                $from_date = trim($input['from']);
                $to_date = trim($input['to']);
                $attachment = null;
                $weekend = array(0, 6);

                if (array_key_exists('uploaded_file', $input)) {
                    $attachment = $input['uploaded_file'];
                }

                if ($reason != "" && Utils::isDate($from_date) && Utils::isDate($to_date) && date("Y-m-d") <= $from_date && 
                $from_date <= $to_date && !in_array(date("w", strtotime($from_date)), $weekend) && 
                !in_array(date("w", strtotime($to_date)), $weekend) && ($attachment == null || ($attachment['error'] == 0 && 
                $attachment['size'] <= LeaveAttachment::SIZE && in_array($attachment['type'], LeaveAttachment::CONTENT_TYPE)))) {
                    $student_id = trim($_SESSION['user_id']);
                    $student_service = new StudentService();
                    $result = $student_service->applyLeave($student_id, $reason, $from_date, $to_date, $attachment);
                    return new ResponseEntity(json_encode($result), 201);
                }
                else {
                    $message = Utils::constructMSG(ExceptionMSG::INVALID_DATA, "From, To Date, Reason or Attachment");
                    throw new ResponseException($message, 406);
                }
            }
            else {
                $message = Utils::constructMSG(ExceptionMSG::INCOMPLETE_DATA, "From, To Date or Reason");
                throw new ResponseException($message, 406);
            }
        }

        public function getAccountDetails(array $input): ResponseEntity {
            $this->authenticate();
            $student_id = trim($_SESSION['user_id']);
            $email = trim($_SESSION['email']);
            $student_service = new StudentService();
            $result = $student_service->getAccountDetails($student_id, $email);
            return new ResponseEntity(json_encode($result));
        }

        public function getAttachment(string $leave_id): array {
            $this->authenticate();
            if (!ctype_digit($leave_id)) {
                $message = Utils::constructMSG(ExceptionMSG::INVALID_DATA, "Leave ID");
                throw new ResponseException($message, 406);
            }
            $student_id = trim($_SESSION['user_id']);
            $student_service = new StudentService();
            $path = $student_service->getAttachment($leave_id, $student_id);
            $content_type = LeaveAttachment::CONTENT_TYPE[pathinfo($path, PATHINFO_EXTENSION)];
            return array("path" => $path, "content-type" => $content_type);
        }
        
        public function getAttendanceAndTimetable(array $input): ResponseEntity {
            $this->authenticate();
            $student_id = trim($_SESSION['user_id']);
            $student_service = new StudentService();
            $result = $student_service->getAttendanceAndTimetable($student_id);
            return new ResponseEntity(json_encode($result));
        }

        public function getLeave(array $input): ResponseEntity {
            $this->authenticate();
            $student_id = trim($_SESSION['user_id']);
            $student_service = new StudentService();
            $result = $student_service->getLeave($student_id);
            return new ResponseEntity(json_encode($result));
        }

        public function updateInLeave(array $input): ResponseEntity {
            $this->authenticate();
            $student_id = trim($_SESSION['user_id']);
            $student_service = new StudentService();
            $result = $student_service->updateInLeave($student_id);
            return new ResponseEntity(json_encode($result));
        }

    }
?>
