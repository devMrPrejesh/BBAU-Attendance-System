<?php
    session_set_cookie_params(604800, "/");
	session_start();
    include ('server/service/StudentService.php');
    include ('server/Constants.php');
    
    class StudentController {

        private function authenticate(): void {
            if (!(array_key_exists('role', $_SESSION) && UserRole::STUDENT === trim($_SESSION['role']))) {
                throw new ResponseException("Unauthorised Access!", 403);
            }
        }
        
        public function getAttendanceAndTimetable(array $input): string {
            $this->authenticate();
            $student_id = trim($_SESSION['user_id']);
            $student_service = new StudentService();
            $result = $student_service->getAttendanceAndTimetable($student_id);
            return json_encode($result);
        }

        public function getLeave(array $input): string {
            $this->authenticate();
            $student_id = trim($_SESSION['user_id']);
            $student_service = new StudentService();
            $result = $student_service->getLeave($student_id);
            return json_encode($result);
        }

        public function applyLeave(array $input): string {
            $this->authenticate();
            if (array_key_exists('reason', $input) && array_key_exists('from_date', $input) && array_key_exists('to_date', $input)) {
                $reason = strtolower(trim($input['reason']));
                $from_date = trim($input['from_date']);
                $to_date = trim($input['to_date']);
                $uploaded_file = null;

                if (array_key_exists('uploaded_file', $input)) {
                    $uploaded_file = $input['uploaded_file'];
                }

                if ($reason != "" && Utils::isDate($from_date) && Utils::isDate($to_date) && date("Y-m-d") <= $from_date && 
                $from_date <= $to_date && ($uploaded_file == null || ($uploaded_file['error'] == 0 && 
                $uploaded_file['size'] <= LeaveAttachment::SIZE && in_array($uploaded_file['type'], LeaveAttachment::CONTENTTYPE)))) {
                    $student_id = trim($_SESSION['user_id']);
                    $student_service = new StudentService();
                    $student_service->applyLeave($student_id, $reason, $from_date, $to_date, $uploaded_file);
                    return '{"success": "Your leave is added."}';
                }
                else {
                    throw new ResponseException("Invalid Data!", 406);
                }
            }
            else {
                throw new ResponseException("Incomplete Data!", 406);
            }
        }

        public function getAccountDetails(array $input): string {
            $this->authenticate();
            $student_id = trim($_SESSION['user_id']);
            $email = trim($_SESSION['email']);
            $student_service = new StudentService();
            $result = $student_service->getAccountDetails($student_id, $email);
            return json_encode($result);
        }

        public function setAccountDetails(array $input): string {
            $this->authenticate();
            
            if (empty($input)) {
                throw new ResponseException("Empty Data array", 406);
            }

            foreach ($input as $key => $value) {
                if (!EditableStudentDetails::isValid($key) || trim($value) === "") {
                    throw new ResponseException("Invalid Data", 406);
                }
            }

            if (array_key_exists("email", $input) && !Utils::isEmail($input['email'])) {
                throw new ResponseException("Invalid Data", 406);
            }

            $student_id = trim($_SESSION['user_id']);
            $email = trim($_SESSION['email']);
            $student_service = new StudentService();
            $student_service->setAccountDetails($student_id, $email, $input);
            
            if (array_key_exists("email", $input)) {
                $_SESSION['email'] = $input['email'];
            }
            return '{"success": "Data updated successfully."}';
        }

    }
?>