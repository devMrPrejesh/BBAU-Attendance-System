<?php
    include ('server/repository/StudentRepository.php');
    include ('server/repository/UserRepository.php');
    include ('server/repository/TeacherRepository.php');
    include ('server/repository/LeaveRepository.php');
    include ('server/repository/HolidayRepository.php');
    
    class TeacherService {
        
        public function getAttendanceAndTimetable(int $teacher_id): array {
            $teacher_repository = new TeacherRepository();

            $data = array("year" => date("Y"));
            $period_size = $teacher_repository->findByID($teacher_id)['number_of_classes'];

            $records = $teacher_repository->findAttendanceByIdOrderByDateandPeriod($teacher_id);
            if (count($records) == 0) {
                $data['records'] = "No Attendance is available.";
            }
            else {
                $data['records'] = Utils::constructRecords($records, $period_size);
            }
            
            $classroom = $teacher_repository->findClassRoomByIdOrderByDayandPeriod($teacher_id);
            $data['timetable'] = Utils::constructTimetable($classroom, $period_size);
            $data['subjectTOclass'] = $teacher_repository->findSubjectAndClassById($teacher_id);

            return $data;
        }

        public function getAccountDetails(int $teacher_id, string $email): array {
            $teacher_repository = new TeacherRepository();
            $result = $teacher_repository->findByID($teacher_id);
            $result['email'] = $email;
            return $result;
        }

        public function setAccountDetails(int $teacher_id, string $email, array $modified_data): void {
            if (array_key_exists('email', $modified_data)) {
                $new_email = $modified_data['email'];
                unset($modified_data['email']);
                $user_repository = new UserRepository();
                $user_repository->updateIdById($email, $new_email);
            }
            $teacher_repository = new TeacherRepository();
            $teacher_repository->updateTeacherById($teacher_id, $modified_data);
        }

        public function getStudentDetails(string $filter_by, string $value): array {
            $student_repository = new StudentRepository();
            $teacher_repository = new TeacherRepository();
            $result = null;
            
            switch ($filter_by) {
                case "class":
                    $result = $student_repository->findByClass($value);
                break;
                case "id":
                    $student_details = $student_repository->findByID((int) $value);
                    $result = array();
                    if ($student_details != null) {
                        array_push($result, $student_details);
                    }
                break;
                case "name":
                    $result = $student_repository->findLikeName($value);
                break;
                default:
                    throw new ResponseException("Invalid Data!", 406);
            }
            
            foreach($result as $index => $student) {
                $student['proctor'] = $teacher_repository->findByID($student['teacher_id'])['teacher_name'];
                unset($student['number_of_subjects']);
                unset($student['teacher_id']);
                $result[$index] = $student;
            }
            
            return $result;
        }

        public function getClassDetails(int $teacher_id): array {
            $user_repository = new UserRepository();
            $student_repository = new StudentRepository();
            $result = $student_repository->findByTeacherId($teacher_id);
            
            if ($result == null) {
                throw new ResponseException("You have no class assigned!", 403);
            }

            $data = array();
            $student_id = $result[0]['student_id'];
            $period_size = $result[0]['number_of_subjects'];
            
            $data['class'] = $result[0]['class'];
            $classroom = $student_repository->findClassRoomByIdOrderByDayandPeriod($student_id);
            $data['timetable'] = Utils::constructTimetable($classroom, $period_size);
            $data['subjectTOteacher'] = $student_repository->findSubjectandTeacherNameById($student_id);
            
            foreach($result as $index => $student) {
                $student['email'] = $user_repository->findIdByUserIdAndRole($student['student_id'], UserRole::STUDENT);
                unset($student['class']);
                unset($student['teacher_id']);
                unset($student['number_of_subjects']);
                $result[$index] = $student;
            }
            
            $data['student_details'] = $result;
            return $data;
        }

        public function getStudentAttendance(int $teacher_id, int $student_id, string $from_date, string $to_date): array {
            $student_repository = new StudentRepository();
            $student = $student_repository->findByID($student_id);
            
            if ($student == null) {
                throw new ResponseException("Invalid Student ID!", 406);
            }
            
            $period_size = $student['number_of_subjects'];
            $records = null;
            
            if ($student['teacher_id'] == $teacher_id) {
                $result = $student_repository->findAttendanceByIdOrderByDateandPeriod($student_id, $from_date, $to_date);
                $records = Utils::constructRecords($result, $period_size);
            }
            else if ($subject = $student_repository->findSubjectByIdAndTeacherId($student['class'], $teacher_id)) {
                $result = $student_repository->findAttendanceByIdAndSubjectOrderByDateandPeriod($student_id, $subject, $from_date, $to_date);
                $records = Utils::constructRecords($result, $period_size);
            }
            else {
                throw new ResponseException("You are not a subject or class teacher for student!", 406);
            }
            
            return $records;
        }

        public function getPeriodSize(int $teacher_id): int {
            $teacher_repository = new TeacherRepository();
            return $teacher_repository->findByID($teacher_id)['number_of_classes'];
        }

        private function checkAttendanceDay(int $teacher_id, int $period, string $current_date): void {
            $holiday_repository = new HolidayRepository();
            
            if ($period > $this->getPeriodSize($teacher_id)) {
                throw new ResponseException("Period has exceeded Period Size!", 406);
            }
            
            if ($holiday_repository->existById($current_date)) {
                throw new ResponseException("You cannot perform attendance on holiday!", 406);
            }
        }
        
        public function getAttendedClass(int $teacher_id, int $period): array {
            $teacher_repository = new TeacherRepository();
            $student_repository = new StudentRepository();
            $current_date = date("Y-m-d");
            $day = date("N");
            
            $this->checkAttendanceDay($teacher_id, $period, $current_date);
            
            $classroom = $teacher_repository->findByIdAndDayAndPeriod($teacher_id, $day, $period);
            if ($classroom['class'] == 'Free Period') {
                throw new ResponseException("It is your free period!", 406);
            }
            
            $data = array();
            $data['subject'] = $classroom['subject'];
            $data['class'] = $classroom['class'];
            $data['student_details'] = array();
            
            $result = $student_repository->findByClassExcludedOnLeave($classroom['class'], $current_date, $period);
            foreach($result as $index => $student) {
                $data['student_details'][$index]['student_id'] = $student['student_id'];
                $data['student_details'][$index]['student_name'] = $student['student_name'];
            }
            return $data;
        }

        public function applyAttendance(int $teacher_id, int $period, array $present_students, string $class=null): void {
            $teacher_repository = new TeacherRepository();
            $student_repository = new StudentRepository();
            $current_date = date("Y-m-d");
            $day = date("N");
            
            $this->checkAttendanceDay($teacher_id, $period, $current_date);
            $classroom = $teacher_repository->findByIdAndDayAndPeriod($teacher_id, $day, $period);
            
            if ($classroom['class'] == 'Free Period') {
                if ($class == null) {
                    throw new ResponseException("Invalid class!", 406);
                }
                $classroom = $teacher_repository->findByClassAndDayAndPeriod($class, $day, $period);
                if ($classroom == null) {
                    throw new ResponseException("Invalid class!", 406);
                }
            }
            $result = $student_repository->findByClassExcludedOnLeave($classroom['class'], $current_date, $period);
            foreach ($result as $student) {
                $status = null;
                $student_id = $student['student_id'];
                if (array_key_exists("student_".$student_id, $present_students)) {
                    $status = "present";
                }
                else {
                    $status = "absent";
                }
                if (!$student_repository->saveAttendance($student_id, $classroom['subject'], $status, $current_date, $period)) {
                    throw new ResponseException("Duplicate Attendance");
                }
            }
            if (!$teacher_repository->saveAttendance($teacher_id, $classroom['subject'], "present", $current_date, $period)) {
                throw new ResponseException("Duplicate Attendance");
            }
            
        }
        
    }
?>