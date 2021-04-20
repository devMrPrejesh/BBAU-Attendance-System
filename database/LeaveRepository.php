<?php
    require_once ('DBConnector.php');

    class LeaveRepository extends DBConnector {

        public function findByIdAndStudentId(int $leave_id, $student_id): ?array {
            $result=mysqli_query($this->conn, "SELECT * FROM leave_system WHERE leave_id='$leave_id' AND student_id='$student_id'");
            if (mysqli_num_rows($result) != 1) {
                return null;
            }
            else {
                return mysqli_fetch_assoc($result);
            }
        }

        public function save(int $teacher_id, int $student_id, string $reason, string $from_date, string $to_date, string $attacment_type=null, string $attacment_data=null): int {
            return mysqli_query($this->conn, "INSERT INTO leave_system(teacher_id, student_id, reason, from_date, to_date, status, attachment_type, attachment_data) VALUES ('$teacher_id', '$student_id', '$reason', '$from_date', '$to_date', 'INTIATED', '$attacment_type', '$attacment_data')");
        }
        
        public function createEvent($from_date): bool {
            $from_date = "2021/04/20  15:30:00.000000";
            $leave_id = $this->findLastId();
            mysqli_query($this->conn, "DELIMITER $$");
            $result =  mysqli_query($this->conn, "CREATE EVENT checkLeaveStatusofID$leave_id ON SCHEDULE AT '$from_date' DO BEGIN UPDATE leave_system SET status='APPROVED' WHERE leave_id = $leave_id; END");
            mysqli_query($this->conn, "DELIMITER ;");
            return $result;
        }

        public function findLastId(): int {
            return mysqli_fetch_assoc(mysqli_query($this->conn, "SELECT MAX(leave_id) FROM leave_system"))['MAX(leave_id)'];
        }

        public function findByStudentId($student_id) {
            $result=mysqli_query($this->conn, "SELECT * FROM leave_system WHERE student_id='$student_id' ORDER BY from_date, to_date");
            return Utils::convertDBRecordstoArray($result);
        }

    }
?>
