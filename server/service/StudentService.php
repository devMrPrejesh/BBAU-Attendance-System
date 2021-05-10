<?php
    include ('server/repository/StudentRepository.php');
    include ('server/repository/UserRepository.php');
    include ('server/repository/TeacherRepository.php');
    include ('server/repository/LeaveRepository.php');
    
    class StudentService {
        
        public function getAttendanceAndTimetable(int $student_id): array {
            $student_repository = new StudentRepository();

            $data = array("year" => date("Y"));
            $period_size = $student_repository->findByID($student_id)['number_of_subjects'];

            $records = $student_repository->findAttendanceByIdOrderByDateandPeriod($student_id);
            if (count($records) == 0) {
                $data['records'] = "No Attendance is available.";
            }
            else {
                $data['records'] = Utils::constructRecords($records, $period_size);
            }
            
            $classroom = $student_repository->findClassRoomByIdOrderByDayandPeriod($student_id);
            $data['timetable'] = Utils::constructTimetable($classroom, $period_size);
            $data['subjectTOteacher'] = $student_repository->findSubjectandTeacherNameById($student_id);

            return $data;
        }

        public function getLeave(int $student_id): array {
            $teacher_repository = new TeacherRepository();
            $leave_repository = new LeaveRepository();
            $result = $leave_repository->findByStudentId($student_id);

            foreach($result as $index => $leave) {
                $leave['teacher_name'] = $teacher_repository->findByID($leave['teacher_id'])['teacher_name'];
                unset($leave['leave_id']);
                unset($leave['teacher_id']);
                unset($leave['student_id']);
                $result[$index] = $leave;
            }
            
            return $result;
        }

        public function applyLeave(int $student_id, string $reason, string $from_date, string $to_date, array $uploaded_file=null): void {
            $student_repository = new StudentRepository();
            $leave_repository = new LeaveRepository();

            $attachment_data = null;
            $attachment_type = null;
            $teacher_id = $student_repository->findByID($student_id)['teacher_id'];

            if ($uploaded_file != null) {
                $attachment_data = base64_encode(file_get_contents($uploaded_file['tmp_name']));
                $attachment_type = $uploaded_file['type'];
            }            
            
            $result = $leave_repository->save($teacher_id, $student_id, $reason, $from_date, $to_date, $attachment_type, $attachment_data);
            
            if (!$result) {
                throw new ResponseException("Cannot add your leave", 501);
            }
        }

        public function getAccountDetails(int $student_id, string $email): array {
            $student_repository = new StudentRepository();
            $result = $student_repository->findByID($student_id);
            $result['email'] = $email;
            return $result;
        }

        public function setAccountDetails(int $student_id, string $email, array $modified_data): void {
            if (array_key_exists('email', $modified_data)) {
                $new_email = $modified_data['email'];
                unset($modified_data['email']);
                $user_repository = new UserRepository();
                $user_repository->updateIdById($email, $new_email);
            }
            $student_repository = new StudentRepository();
            $student_repository->updateStudentById($student_id, $modified_data);
        }
        
    }
?>