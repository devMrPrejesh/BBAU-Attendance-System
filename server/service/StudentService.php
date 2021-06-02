<?php
    include ('server/repository/StudentRepository.php');
    include ('server/repository/TeacherRepository.php');
    include ('server/repository/LeaveRepository.php');
    
    class StudentService {

        public function applyLeave(int $student_id, string $reason, string $from_date, string $to_date, ?array $uploaded_file): array {
            $student_repository = new StudentRepository();
            $leave_repository = new LeaveRepository();
            $teacher_repository = new TeacherRepository();

            if (!$leave_repository->checkDuration($student_id, $from_date, $to_date)) {
                $message = Utils::constructMSG(ExceptionMSG::INVALID_DATA, "Duration");
                throw new ResponseException($message, 406);
            }

            $attachment_path = null;
            $teacher_id = $student_repository->findByID($student_id)['teacher_id'];

            if ($uploaded_file != null) {
                $attachment_path = Utils::addUploadedFile($uploaded_file['name'], $uploaded_file['tmp_name']);
            }
            
            $leave_id = $leave_repository->save($teacher_id, $student_id, $reason, $from_date, $to_date, $attachment_path);
            
            if ($leave_id === -1) {
                if ($uploaded_file != null) { unlink(LeaveAttachment::PATH.$attachment_path); }
                throw new ResponseException(ExceptionMSG::LEAVE_FAILURE, 501);
            }
            else {
                $result = array('leave_id' => $leave_id);
                $result['approver_name'] = $teacher_repository->findByID($teacher_id)['teacher_name'];
                return $result;
            }
        }

        public function getAccountDetails(int $student_id, string $email): array {
            $student_repository = new StudentRepository();
            $teacher_repository = new TeacherRepository();
            $result = $student_repository->findByID($student_id);

            $result['teacher_name'] = $teacher_repository->findByID($result['teacher_id'])['teacher_name'];
            unset($result['teacher_id']);
            $result['email'] = $email;

            return $result;
        }
        
        public function getAttachment(int $leave_id, int $student_id): string {
            $leave_repository = new LeaveRepository();
            $attachment_path = $leave_repository->findAttachmentPathByIdAndStudentId($leave_id, $student_id);
            
            if ($attachment_path != null) {
                return UserProfile::PATH.$attachment_path;
            }
            else {
                $message = Utils::constructMSG(ExceptionMSG::INVALID_DATA, "File Access");
                throw new ResponseException($message, 406);
            }
        }
        
        public function getAttendanceAndTimetable(int $student_id): array {
            $student_repository = new StudentRepository();
            $data = array();
            $period_size = $student_repository->findByID($student_id)['number_of_subjects'];
            $records = $student_repository->findAttendanceByIdOrderByDateandPeriod($student_id);

            if (count($records) == 0) {
                $data['records'] = array();
            }
            else {
                $data['records'] = Utils::constructRecords($records, $period_size);
            }
            
            $classroom = $student_repository->findClassRoomByIdOrderByDayandPeriod($student_id);
            $data['timetable'] = Utils::constructTimetable($classroom, $period_size, "subject");
            $info = $student_repository->findSubjectandTeacherNameById($student_id);
            $data['timetableInfo'] = Utils::constructTimetableInfo($info, "subject", "teacher_name");
            return $data;
        }

        public function getLeave(int $student_id): array {
            $teacher_repository = new TeacherRepository();
            $leave_repository = new LeaveRepository();
            $result = $leave_repository->findByStudentId($student_id);

            foreach($result as $index => $leave) {
                $leave['approver_name'] = $teacher_repository->findByID($leave['teacher_id'])['teacher_name'];
                unset($leave['teacher_id']);
                unset($leave['student_id']);
                unset($leave['status_change']);
                if ($leave['attachment_path'] != null) {
                    $leave['attachment_flag'] = TRUE;
                }
                else {
                    $leave['attachment_flag'] = FALSE;
                }
                unset($leave['attachment_path']);
                $result[$index] = $leave;
            }
            
            return $result;
        }
        
        public function updateInLeave(int $student_id): array {
            $leave_repository = new LeaveRepository();
            $result = array("result" => $leave_repository->checkStatusChange($student_id));
            return $result;
        }

    }
?>