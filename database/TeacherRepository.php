<?php
    require_once ('DBConnector.php');

    class TeacherRepository extends DBConnector {

        public function findByID(int $teacher_id): ?array {
            $query = "SELECT * FROM teacher WHERE teacher_id = '$teacher_id'";
            $result = mysqli_query($this->conn, $query);
            $result = Utils::convertDBRecordstoArray($result);
            if (count($result) == 1) {
                return $result[0];
            }
            else {
                return null;
            }
        }
        
        public function findAttendanceByIdOrderByDateandPeriod(int $teacher_id): array {
            $query = "SELECT * FROM teacher_attendance WHERE teacher_id = '$teacher_id' ORDER BY date, period";
            $result = mysqli_query($this->conn, $query);
            return Utils::convertDBRecordstoArray($result);
        }
        
        public function findClassRoomByIdOrderByDayandPeriod(int $teacher_id): array {
            $query = "SELECT * FROM classroom WHERE teacher_id='$teacher_id' ORDER BY day, period";
            $result = mysqli_query($this->conn, $query);
            return Utils::convertDBRecordstoArray($result);
        }

        public function findSubjectandClassById(int $teacher_id): array {
            $query = "SELECT DISTINCT subject, class FROM classroom WHERE teacher_id='$teacher_id'";
            $result = mysqli_query($this->conn, $query);
            return Utils::convertDBRecordstoArray($result);
        }
        
        public function findByTeacherIdAndDayAndPeriod(int $teacher_id, int $day, int $period): ?array {
            $query = "SELECT * FROM classroom WHERE teacher_id = '$teacher_id' AND day = '$day' AND period = '$period'";
            $result = mysqli_query($this->conn, $query);
            if (mysqli_num_rows($result) == 1) {
                return Utils::convertDBRecordstoArray($result)[0];
            }
            else {
                return null;
            }
        }

        public function findSubjectByClassAndDayAndPeriod(string $class, int $day, int $period): ?string {
            $query = "SELECT subject FROM classroom WHERE class = '$class' AND day = '$day' AND period = '$period'";
            $result = mysqli_query($this->conn, $query);
            if (mysqli_num_rows($result) == 1) {
                return mysqli_fetch_assoc($result)['subject'];
            }
            else {
                return null;
            }
        }

        public function saveAttendance(int $teacher_id, string $subject, string $status, string $date, int $period): bool {
            $query = "INSERT INTO teacher_attendance VALUES ('$teacher_id','$subject','$status','$date]','$period')";
            return mysqli_query($this->conn, $query);
        }
        
    }
?>