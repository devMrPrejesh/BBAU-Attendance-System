<?php
    require_once ('DBConnector.php');

    class TeacherRepository extends DBConnector {

        public function findByID(int $teacher_id): ?array {
            $query = "SELECT * FROM teacher WHERE teacher_id = '$teacher_id'";
            $result = mysqli_query($this->conn, $query);
            if (mysqli_num_rows($result) == 1) {
                return $this->convertDBRecordstoArray($result)[0];
            }
            else {
                return null;
            }
        }
        
        public function findAttendanceByIdOrderByDateAndPeriod(int $teacher_id): array {
            $query = "SELECT * FROM teacher_attendance WHERE teacher_id = '$teacher_id' ORDER BY date, period";
            $result = mysqli_query($this->conn, $query);
            return $this->convertDBRecordstoArray($result);
        }
        
        public function findClassRoomByIdOrderByDayAndPeriod(int $teacher_id): array {
            $query = "SELECT * FROM classroom WHERE teacher_id='$teacher_id' ORDER BY day, period";
            $result = mysqli_query($this->conn, $query);
            return $this->convertDBRecordstoArray($result);
        }

        public function findSubjectAndClassById(int $teacher_id): array {
            $query = "SELECT DISTINCT subject, class FROM classroom WHERE teacher_id='$teacher_id'";
            $result = mysqli_query($this->conn, $query);
            return $this->convertDBRecordstoArray($result);
        }
        
        public function findByIdAndDayAndPeriod(int $teacher_id, int $day, int $period): ?array {
            $query = "SELECT * FROM classroom WHERE teacher_id = '$teacher_id' AND day = '$day' AND period = '$period'";
            $result = mysqli_query($this->conn, $query);
            if (mysqli_num_rows($result) == 1) {
                return $this->convertDBRecordstoArray($result)[0];
            }
            else {
                return null;
            }
        }

        public function findByClassAndDayAndPeriod(string $class, int $day, int $period): ?array {
            $query = "SELECT * FROM classroom WHERE class = '$class' AND day = '$day' AND period = '$period'";
            $result = mysqli_query($this->conn, $query);
            if (mysqli_num_rows($result) == 1) {
                return $this->convertDBRecordstoArray($result)[0];
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

        public function saveAttendance(int $teacher_id, string $status, string $date, int $period): bool {
            $query = "INSERT INTO teacher_attendance VALUES ('$teacher_id','$status','$date','$period')";
            return mysqli_query($this->conn, $query);
        }

        public function updateTeacherById(int $teacher_id, array $data): void {
            $query = "UPDATE teacher SET ";
            foreach ($data as $column => $value) {
                $query .= "$column = '$value', ";
            }
            $query = substr($query, 0, -2)." WHERE teacher_id = '$teacher_id'";
            mysqli_query($this->conn, $query);
        }
        
    }
?>