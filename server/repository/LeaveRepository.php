<?php
    require_once ('DBConnector.php');

    class LeaveRepository extends DBConnector {

        private function createEvent(string $from_date): int {
            $query0 = "SELECT MAX(leave_id) FROM leave_system";
            $result0= mysqli_query($this->conn, $query0);
            
            if (mysqli_num_rows($result0) != 1) {
                return -1;
            }
            $leave_id = mysqli_fetch_assoc($result0)['MAX(leave_id)'];
            if (date("Y-m-d") == $from_date) {
                return $leave_id;
            }
            
            $query1 = "CREATE EVENT checkLeaveStatusofID$leave_id ON SCHEDULE AT '$from_date 00:00:00' DO BEGIN CALL approveLeave($leave_id); END";
            if (mysqli_query($this->conn, $query1)) {
                return $leave_id;
            }
            else {
                return -1;
            }
        }

        public function checkDuration(int $student_id, string $from_date, string $to_date): bool {
            $query = "SELECT leave_id FROM leave_system WHERE student_id = '$student_id' AND status != 'REJECTED' AND NOT ((from_date > '$from_date' AND from_date > '$to_date') OR (to_date < '$from_date' AND to_date < '$to_date'))";
            $result = mysqli_query($this->conn, $query);
            if (mysqli_num_rows($result) > 0) {
                return FALSE;
            }
            else {
                return TRUE;
            }
        }

        public function checkStatusChange(int $user_id, bool $student_flag=TRUE): bool {
            $query = NULL;
            if ($student_flag) {
                $query = "SELECT leave_id FROM leave_system WHERE student_id = '$user_id' AND status_change = '-1'";
            }
            else {
                $query = "SELECT leave_id FROM leave_system WHERE teacher_id = '$user_id' AND status_change = '1'";
            }
            $result = mysqli_query($this->conn, $query);
            if (mysqli_num_rows($result) > 0) {
                return TRUE;
            }
            else {
                return FALSE;
            }
        }

        public function findAttachmentPathByIdAndStudentId(int $leave_id, int $student_id): ?string {
            $query = "SELECT attachment_path FROM leave_system WHERE leave_id='$leave_id' AND student_id='$student_id'";
            $result = mysqli_query($this->conn, $query);
            if (mysqli_num_rows($result) == 1) {
                return mysqli_fetch_assoc($result)['attachment_path'];
            }
            else {
                return NULL;
            }
        }

        public function findAttachmentPathByIdAndTeacherId(int $leave_id, int $teacher_id): ?string {
            $query = "SELECT attachment_path FROM leave_system WHERE leave_id='$leave_id' AND teacher_id='$teacher_id'";
            $result = mysqli_query($this->conn, $query);
            if (mysqli_num_rows($result) == 1) {
                return mysqli_fetch_assoc($result)['attachment_path'];
            }
            else {
                return NULL;
            }
        }

        public function findById(int $leave_id): ?array {
            $query = "SELECT * FROM leave_system WHERE leave_id = '$leave_id'";
            $result = mysqli_query($this->conn, $query);
            if (mysqli_num_rows($result) == 1) {
                return $this->convertDBRecordstoArray($result)[0];
            }
            else {
                return NULL;
            }
        }
        
        public function findByStudentId(int $student_id): array {
            $query0 = "UPDATE leave_system SET status_change = 0 WHERE student_id = '$student_id' AND status_change = -1";
            mysqli_query($this->conn, $query0);
            $query1 = "SELECT * FROM leave_system WHERE student_id='$student_id' ORDER BY from_date, to_date";
            $result = mysqli_query($this->conn, $query1);
            return $this->convertDBRecordstoArray($result);
        }

        public function findByTeacherId(int $teacher_id): array {
            $query0 = "UPDATE leave_system SET status = 'READ', status_change = 0 WHERE teacher_id = '$teacher_id' AND status_change = 1";
            mysqli_query($this->conn, $query0);
            $query1 = "SELECT * FROM leave_system WHERE teacher_id='$teacher_id' ORDER BY from_date, to_date";
            $result = mysqli_query($this->conn, $query1);
            return $this->convertDBRecordstoArray($result);
        }

        public function save(int $teacher_id, int $student_id, string $reason, string $from_date, string $to_date, string $attachment_path=NULL): int {
            $query = "INSERT INTO leave_system(teacher_id, student_id, reason, from_date, to_date, attachment_path) VALUES ('$teacher_id', '$student_id', '$reason', '$from_date', '$to_date', ";
            if  ($attachment_path != NULL) {
                $query .= "'$attachment_path')";
            }
            else {
                $query .= "NULL)";
            }
            if (mysqli_query($this->conn, $query)) {
                return $this->createEvent($from_date);
            }
            else {
                return -1;
            }
        }

        public function updateLeaveById(int $leave_id, string $value, string $remark): void {
            $query = "UPDATE leave_system SET status = '$value', status_change = -1, remarks = '$remark' WHERE leave_id = '$leave_id'";
            mysqli_query($this->conn, $query);
            mysqli_query($this->conn, "CALL approveLeave($leave_id);");
        }

    }
?>

