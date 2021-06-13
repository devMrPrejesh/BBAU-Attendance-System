<?php
    require_once ('DBConnector.php');

    class TeacherRepository extends DBConnector {

        public function findAttendanceByIdOrderByDateAndPeriod(int $teacher_id): array {
            $query = "SELECT * FROM teacher_attendance WHERE teacher_id = '$teacher_id' ORDER BY date, period";
            $result = mysqli_query($this->conn, $query);
            return $this->convertDBRecordstoArray($result);
        }
        
        public function findById(int $teacher_id): ?array {
            $query = "SELECT * FROM teacher WHERE teacher_id = '$teacher_id'";
            $result = mysqli_query($this->conn, $query);
            if (mysqli_num_rows($result) == 1) {
                return $this->convertDBRecordstoArray($result)[0];
            }
            else {
                return NULL;
            }
        }
        
        public function findByIdAndDayAndPeriod(int $teacher_id, int $day, int $period): ?array {
            $query = "SELECT * FROM classroom WHERE teacher_id = '$teacher_id' AND day = '$day' AND period = '$period'";
            $result = mysqli_query($this->conn, $query);
            if (mysqli_num_rows($result) == 1) {
                return $this->convertDBRecordstoArray($result)[0];
            }
            else {
                return NULL;
            }
        }

        public function findByClassAndDayAndPeriod(string $class, int $day, int $period): ?array {
            $query = "SELECT * FROM classroom WHERE class = '$class' AND day = '$day' AND period = '$period'";
            $result = mysqli_query($this->conn, $query);
            if (mysqli_num_rows($result) == 1) {
                return $this->convertDBRecordstoArray($result)[0];
            }
            else {
                return NULL;
            }
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

        public function save(int $teacher_id, $name, $period_size, $class): void {
            $query = "INSERT INTO teacher VALUES ('$teacher_id', '$name', '$period_size', ";
            if ($class ==NULL) {
                $query .= "NULL)";
            }
            else {
                $query .= "'$class')";
            }
            mysqli_query($this->conn, $query);
        }

        public function saveAttendance(int $teacher_id, string $status, string $date, int $period): bool {
            $query = "INSERT INTO teacher_attendance VALUES ('$teacher_id','$status','$date','$period')";
            return mysqli_query($this->conn, $query);
        }
        
    }
?>
