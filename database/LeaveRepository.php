<?php
    require_once ('DBConnector.php');

    class LeaveRepository extends DBConnector {

        public function save(int $teacher_id, int $student_id, string $reason, string $from_date, string $to_date, string $attacment_type=null, string $attacment_data=null): bool {
            $query = "INSERT INTO leave_system(teacher_id, student_id, reason, from_date, to_date, status, attachment_type, attachment_data) VALUES ('$teacher_id', '$student_id', '$reason', '$from_date', '$to_date', 'INTIATED', '$attacment_type', '$attacment_data')";
            if (mysqli_query($this->conn, $query)) {
                return $this->createEvent($from_date);
            }
            else {
                return False;
            }
        }
        
        private function createEvent($from_date): bool {
            $query0 = "SELECT MAX(leave_id) FROM leave_system";
            $result0= mysqli_fetch_assoc(mysqli_query($this->conn, $query0));
            if (count($result0) != 1) {
                return False;
            }
            $leave_id = $query0['MAX(leave_id)'];
            $query1 = "DELIMITER $$";
            $query2 = "CREATE EVENT checkLeaveStatusofID$leave_id ON SCHEDULE AT '$from_date' DO BEGIN UPDATE leave_system SET status='APPROVED' WHERE leave_id = $leave_id; END";
            $query3 = "DELIMITER ;";
            if (mysqli_query($this->conn, $query1) and mysqli_query($this->conn, $query2) and 
            mysqli_query($this->conn, $query3)) {
                return True;
            }
            else {
                return False;
            }
        }

        public function findByStudentId($student_id) {
            $query = "SELECT * FROM leave_system WHERE student_id='$student_id' ORDER BY from_date, to_date";
            $result = mysqli_query($this->conn, $query);
            return Utils::convertDBRecordstoArray($result);
        }

    }
?>
