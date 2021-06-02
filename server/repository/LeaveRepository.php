<?php
    require_once ('DBConnector.php');

    class LeaveRepository extends DBConnector {

        public function save(int $teacher_id, int $student_id, string $reason, string $from_date, string $to_date, ?string $attachment_path): int {
            $query = "INSERT INTO leave_system(teacher_id, student_id, reason, from_date, to_date, attachment_path) VALUES ('$teacher_id', '$student_id', '$reason', '$from_date', '$to_date', '$attachment_path')";
            if (mysqli_query($this->conn, $query)) {
                return $this->createEvent($from_date);
            }
            else {
                return -1;
            }
        }
        
        private function createEvent(string $from_date): int {
            $query0 = "SELECT MAX(leave_id) FROM leave_system";
            $result0= mysqli_query($this->conn, $query0);
            
            if (mysqli_num_rows($result0) != 1) {
                return -1;
            }
            
            $leave_id = mysqli_fetch_assoc($result0)['MAX(leave_id)'];
            $query1 = "CREATE EVENT checkLeaveStatusofID$leave_id ON SCHEDULE AT '$from_date' DO BEGIN CALL approveLeave($leave_id); END";
            if (mysqli_query($this->conn, $query1)) {
                return $leave_id;
            }
            else {
                return -1;
            }
        }

        public function findByStudentId(int $student_id): array {
            $query0 = "UPDATE leave_system SET status_change = 0 WHERE student_id = '$student_id' AND status_change = -1";
            mysqli_query($this->conn, $query0);
            $query1 = "SELECT * FROM leave_system WHERE student_id='$student_id' ORDER BY from_date, to_date";
            $result = mysqli_query($this->conn, $query1);
            return $this->convertDBRecordstoArray($result);
        }

        public function findAttachmentPathByIdAndStudentId(int $leave_id, int $student_id): ?string {
            $query = "SELECT attachment_path FROM leave_system WHERE leave_id='$leave_id' AND student_id='$student_id'";
            $result = mysqli_query($this->conn, $query);
            if (mysqli_num_rows($result) == 1) {
                return mysqli_fetch_assoc($result)['attachment_path'];
            }
            else {
                return null;
            }
        }

        public function findAttachmentPathByIdAndTeacherId(int $leave_id, int $teacher_id): ?string {
            $query = "SELECT attachment_path FROM leave_system WHERE leave_id='$leave_id' AND teacher_id='$teacher_id'";
            $result = mysqli_query($this->conn, $query);
            if (mysqli_num_rows($result) == 1) {
                return mysqli_fetch_assoc($result)['attachment_path'];
            }
            else {
                return null;
            }
        }

        public function checkDuration(int $student_id, string $from_date, string $to_date): bool {
            $query = "SELECT leave_id FROM leave_system WHERE student_id = '$student_id' AND NOT ((from_date > '$from_date' AND from_date > '$to_date') OR (to_date < '$from_date' AND to_date < '$to_date'))";
            $result = mysqli_query($this->conn, $query);
            if (mysqli_num_rows($result) > 0) {
                return FALSE;
            }
            else {
                return TRUE;
            }
        }

        public function checkStatusChange(int $student_id): bool {
            $query = "SELECT leave_id FROM leave_system WHERE student_id = '$student_id' AND status_change = '-1'";
            $result = mysqli_query($this->conn, $query);
            if (mysqli_num_rows($result) > 0) {
                return TRUE;
            }
            else {
                return FALSE;
            }
        }
    }
?>
