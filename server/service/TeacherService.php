<?php
    include ('server/repository/StudentRepository.php');
    include ('server/repository/UserRepository.php');
    include ('server/repository/TeacherRepository.php');
    include ('server/repository/LeaveRepository.php');
    include ('server/repository/HolidayRepository.php');
    
    class TeacherService {

        private function checkAttendanceDay(int $teacher_id, int $period): void {
            $holiday_repository = new HolidayRepository();
            
            if ($period > $this->getPeriodSize($teacher_id)) {
                throw new ResponseException("Period has exceeded Period Size!", 406);
            }
            
            if ($holiday_repository->existById(date("Y-m-d"))) {
                throw new ResponseException("You cannot perform attendance on holiday!", 406);
            }
        }

        public function applyAttendance(int $teacher_id, int $period, array $present_students, ?string $class): void {
            $teacher_repository = new TeacherRepository();
            $student_repository = new StudentRepository();
            $current_date = date("Y-m-d");
            $day = date("N") - 1;
            
            $this->checkAttendanceDay($teacher_id, $period);
            $classroom = $teacher_repository->findByIdAndDayAndPeriod($teacher_id, $day, $period);
            if ($classroom['class'] == 'NA') {
                if ($class == null) { throw new ResponseException(ExceptionMSG::CLASS_NA, 406); }
                else {
                    $classroom = $teacher_repository->findByClassAndDayAndPeriod($class, $day, $period);
                    if ($classroom == null || $classroom['class'] == 'NA') {
                        $message = Utils::constructMSG(ExceptionMSG::INVALID_DATA, "Class");
                        throw new ResponseException($message, 406);
                    }
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
                if (!$student_repository->saveAttendance($student_id, $status, $current_date, $period)) {
                    throw new ResponseException(ExceptionMSG::DUPLICATE_ATTENDANCE);
                }
            }
            if (!$teacher_repository->saveAttendance($teacher_id, "present", $current_date, $period)) {
                throw new ResponseException(ExceptionMSG::DUPLICATE_ATTENDANCE);
            }
            
        }

        public function getAccountDetails(int $teacher_id, string $email): array {
            $teacher_repository = new TeacherRepository();
            $result = $teacher_repository->findById($teacher_id);
            $result['email'] = $email;
            return $result;
        }
        
        public function getAttendanceAndTimetable(int $teacher_id): array {
            $teacher_repository = new TeacherRepository();
            $data = array();
            $period_size = $teacher_repository->findById($teacher_id)['number_of_classes'];
            $records = $teacher_repository->findAttendanceByIdOrderByDateandPeriod($teacher_id);

            if (count($records) == 0) {
                $data['records'] = array();
            }
            else {
                $data['records'] = Utils::constructRecords($records, $period_size);
            }
            
            $classroom = $teacher_repository->findClassRoomByIdOrderByDayandPeriod($teacher_id);
            $data['timetable'] = Utils::constructTimetable($classroom, $period_size, "class");
            $info = $teacher_repository->findSubjectAndClassById($teacher_id);
            $data['timetableInfo'] = Utils::constructTimetableInfo($info, "class", "subject");
            return $data;
        }
        
        public function getAttendedClass(int $teacher_id, int $period, ?string $class): array {
            $teacher_repository = new TeacherRepository();
            $student_repository = new StudentRepository();
            $day = date("N") - 1;
            
            $this->checkAttendanceDay($teacher_id, $period);
            $classroom = $teacher_repository->findByIdAndDayAndPeriod($teacher_id, $day, $period);
            if ($classroom['class'] == 'NA') {
                if ($class == null) { throw new ResponseException(ExceptionMSG::CLASS_NA, 406); }
                else {
                    $classroom = $teacher_repository->findByClassAndDayAndPeriod($class, $day, $period);
                    if ($classroom == null || $classroom['class'] == 'NA') {
                        $message = Utils::constructMSG(ExceptionMSG::INVALID_DATA, "Class");
                        throw new ResponseException($message, 406);
                    }
                }
            }
            
            $data = array();
            $data['subject'] = $classroom['subject'];
            $data['class'] = $classroom['class'];
            $data['student_details'] = array();
            
            $result = $student_repository->findByClassExcludedOnLeave($classroom['class'], date("Y-m-d"), $period);
            foreach($result as $index => $student) {
                $data['student_details'][$index]['student_id'] = $student['student_id'];
                $data['student_details'][$index]['student_name'] = $student['student_name'];
            }
            return $data;
        }

        public function getClassDetails(int $teacher_id): array {
            $user_repository = new UserRepository();
            $student_repository = new StudentRepository();
            $result = $student_repository->findByTeacherId($teacher_id);
            
            if ($result == null) {
                throw new ResponseException(ExceptionMSG::NOT_CLASS_TEACHER, 403);
            }

            $data = array();
            $student_id = $result[0]['student_id'];
            $period_size = $result[0]['number_of_subjects'];
            
            $data['class'] = $result[0]['class'];
            $classroom = $student_repository->findClassRoomByIdOrderByDayandPeriod($student_id);
            $data['timetable'] = Utils::constructTimetable($classroom, $period_size, "subject");
            $info = $student_repository->findSubjectandTeacherNameById($student_id);
            $data['timetableInfo'] = Utils::constructTimetableInfo($info, "subject", "teacher_name");
            
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

        public function getLeaveAttachment(int $leave_id, int $teacher_id): string {
            $leave_repository = new LeaveRepository();
            $attachment_path = $leave_repository->findAttachmentPathByIdAndTeacherId($leave_id, $teacher_id);
            
            if ($attachment_path != null) {
                return UserProfile::PATH.$attachment_path;
            }
            else {
                $message = Utils::constructMSG(ExceptionMSG::INVALID_DATA, "File Access");
                throw new ResponseException($message, 406);
            }
        }

        public function getPeriodSize(int $teacher_id): int {
            $teacher_repository = new TeacherRepository();
            return $teacher_repository->findById($teacher_id)['number_of_classes'];
        }

        public function getStudentAttendance(int $teacher_id, int $student_id, string $from_date, string $to_date): array {
            $student_repository = new StudentRepository();
            $student = $student_repository->findById($student_id);
            
            if ($student == null) {
                $message = Utils::constructMSG(ExceptionMSG::INVALID_DATA, "Student ID");
                throw new ResponseException($message, 406);
            }
            
            $period_size = $student['number_of_subjects'];
            $records = null;
            
            if ($student['teacher_id'] == $teacher_id) {
                $result = $student_repository->findAttendanceByIdOrderByDateandPeriod($student_id, $from_date, $to_date);
                $records = Utils::constructRecords($result, $period_size);
                $records["Info"] = "Attendance for all subjects.";
            }
            elseif ($subject = $student_repository->findSubjectByClassAndTeacherId($student['class'], $teacher_id)) {
                $result = $student_repository->findAttendanceByIdAndSubjectOrderByDateandPeriod($student_id, $teacher_id, $student['class'], $from_date, $to_date);
                $records = Utils::constructRecords($result, $period_size);
                $records["Info"] = "Attendance for $subject Classes.";
            }
            else {
                throw new ResponseException(ExceptionMSG::NON_ACCESS_TEACHER, 406);
            }
            
            return $records;
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
                    $student_details = $student_repository->findById((int) $value);
                    $result = array();
                    if ($student_details != null) {
                        array_push($result, $student_details);
                    }
                break;
                case "name":
                    $result = $student_repository->findLikeName($value);
                break;
                default:
                    $message = Utils::constructMSG(ExceptionMSG::INVALID_DATA, "Filter By");
                    throw new ResponseException($message, 406);
            }
            
            foreach($result as $index => $student) {
                $student['proctor'] = $teacher_repository->findById($student['teacher_id'])['teacher_name'];
                unset($student['number_of_subjects']);
                unset($student['teacher_id']);
                $result[$index] = $student;
            }
            
            return $result;
        }
        
    }
?>
