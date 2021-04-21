<?php
    require_once ('DBConnector.php');

    class StudentRepository extends DBConnector {

        public function findByID(int $student_id): ?array {
            $query = "SELECT * FROM student WHERE student_id = '$student_id'";
            $result = mysqli_query($this->conn, $query);
            $result = Utils::convertDBRecordstoArray($result);
            if (count($result) == 1) {
                return $result[0];
            }
            else {
                return null;
            }
        }

        public function findAttendanceByIdOrderByDateandPeriod(int $student_id): array {
            $query = "SELECT * FROM student_attendance WHERE student_id = '$student_id' ORDER BY date, period";
            $result = mysqli_query($this->conn, $query);
            return Utils::convertDBRecordstoArray($result);
        }

        public function findAttendanceByIdAndSubjectOrderByDateandPeriod(int $student_id, string $subject): array {
            $query = "SELECT * FROM student_attendance WHERE student_id = '$student_id' AND subject = '$subject' ORDER BY date, period";
            $result = mysqli_query($this->conn, $query);
            return Utils::convertDBRecordstoArray($result);
        }
        
        public function findClassRoomByIdOrderByDayandPeriod(int $student_id): array {
            $query = "SELECT c.* FROM classroom AS c JOIN student AS s ON c.class = s.class WHERE s.student_id='$student_id' ORDER BY day, period";
            $result = mysqli_query($this->conn, $query);
            return Utils::convertDBRecordstoArray($result);
        }

        public function findSubjectandTeacherNameById(int $student_id): array {
            $query = "SELECT DISTINCT c.subject, t.teacher_name FROM classroom AS c JOIN student AS s ON c.class = s.class JOIN teacher AS t ON t.teacher_id=c.teacher_id WHERE s.student_id='$student_id'";
            $result = mysqli_query($this->conn, $query);
            return Utils::convertDBRecordstoArray($result);
        }

        public function findByClass(string $class): array {
            $query = "SELECT * FROM student WHERE class='$class'";
            $result = mysqli_query($this->conn, $query);
            return Utils::convertDBRecordstoArray($result);
        }

        public function findLikeName(string $name): array {
            $query = "SELECT * FROM student WHERE student_name LIKE '%$name%'";
            $result = mysqli_query($this->conn, $query);
            return Utils::convertDBRecordstoArray($result);
        }

        public function findDateLimitByID(): array {
            $query = "SELECT MIN(date), MAX(date) FROM student_attendance";
            $result = mysqli_query($this->conn, $query);
            $result = Utils::convertDBRecordstoArray($result);
            if (count($result) == 1) {
                return $result[0];
            }
            else {
                return null;
            }
        }
        
        public function findSubjectByIdAndTeacherId(string $class, int $teacher_id): ?string {
            $query = "SELECT DISTINCT subject FROM classroom WHERE teacher_id = $teacher_id AND class = '$class'";
            $result = mysqli_query($this->conn, $query);
            if (mysqli_num_rows($result) == 1) {
                return mysqli_fetch_assoc($result)['subject'];
            }
            else {
                return null;
            }
        }

        public function findByClassExcludedOnLeave(string $class, string $date, string $period): array {
            $query = "SELECT * FROM student WHERE class = '$class' AND student_id NOT IN (SELECT student_id FROM student_attendance WHERE date = '$date' AND period = '$period')";
            $result = mysqli_query($this->conn, $query);
            return Utils::convertDBRecordstoArray($result);
        }

        public function saveAttendance(int $student_id, string $subject, string $status, string $date, int $period): bool {
            $query = "INSERT INTO student_attendance VALUES ('$student_id','$subject','$status','$date]','$period')";
            return mysqli_query($this->conn, $query);
        }

    }
?>