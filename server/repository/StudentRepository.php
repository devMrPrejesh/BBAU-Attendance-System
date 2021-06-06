<?php
    require_once ('DBConnector.php');

    class StudentRepository extends DBConnector {

        public function findAttendanceByIdAndSubjectOrderByDateAndPeriod(int $student_id, int $teacher_id, string $class, string $from_date, string $to_date): array {
            $query = "SELECT sa.status, sa.date FROM student_attendance AS sa, classroom as c WHERE sa.student_id = '$student_id' AND c.teacher_id = '$teacher_id' AND c.class = '$class' AND DAYOFWEEK(sa.date)-2 = c.day AND sa.period = c.period AND sa.date BETWEEN '$from_date' AND '$to_date' ORDER BY sa.date, sa.period";
            $result = mysqli_query($this->conn, $query);
            return $this->convertDBRecordstoArray($result);
        }

        public function findAttendanceByIdOrderByDateAndPeriod(int $student_id, string $from_date=null, string $to_date=null): array {
            $query = "SELECT * FROM student_attendance WHERE student_id = '$student_id' ";
            if ($from_date != null and $to_date) {
                $query .= "AND date BETWEEN '$from_date' AND '$to_date' ";
            }
            $query .= "ORDER BY date, period";
            $result = mysqli_query($this->conn, $query);
            return $this->convertDBRecordstoArray($result);
        }

        public function findByClass(string $class): array {
            $query = "SELECT * FROM student WHERE class='$class'";
            $result = mysqli_query($this->conn, $query);
            return $this->convertDBRecordstoArray($result);
        }

        public function findByClassExcludedOnLeave(string $class, string $date, string $period): array {
            $query = "SELECT * FROM student WHERE class = '$class' AND student_id NOT IN (SELECT student_id FROM student_attendance WHERE date = '$date' AND period = '$period')";
            $result = mysqli_query($this->conn, $query);
            return $this->convertDBRecordstoArray($result);
        }
        
        public function findById(int $student_id): ?array {
            $query = "SELECT * FROM student WHERE student_id = '$student_id'";
            $result = mysqli_query($this->conn, $query);
            if (mysqli_num_rows($result) == 1) {
                return $this->convertDBRecordstoArray($result)[0];
            }
            else {
                return null;
            }
        }

        public function findByTeacherId(int $teacher_id): ?array {
            $query = "SELECT * FROM student WHERE teacher_id = '$teacher_id'";
            $result = mysqli_query($this->conn, $query);
            if (mysqli_num_rows($result) != 0) {
                return $this->convertDBRecordstoArray($result);
            }
            else {
                return null;
            }
        }

        public function findClassRoomByIdOrderByDayAndPeriod(int $student_id): array {
            $query = "SELECT c.* FROM classroom AS c JOIN student AS s ON c.class = s.class WHERE s.student_id='$student_id' ORDER BY day, period";
            $result = mysqli_query($this->conn, $query);
            return $this->convertDBRecordstoArray($result);
        }

        public function findLikeName(string $name): array {
            $query = "SELECT * FROM student WHERE student_name LIKE '%$name%'";
            $result = mysqli_query($this->conn, $query);
            return $this->convertDBRecordstoArray($result);
        }

        public function findSubjectAndTeacherNameById(int $student_id): array {
            $query = "SELECT DISTINCT c.subject, t.teacher_name FROM classroom AS c JOIN student AS s ON c.class = s.class JOIN teacher AS t ON t.teacher_id=c.teacher_id WHERE s.student_id='$student_id'";
            $result = mysqli_query($this->conn, $query);
            return $this->convertDBRecordstoArray($result);
        }
        
        public function findSubjectByClassAndTeacherId(string $class, int $teacher_id): ?string {
            $query = "SELECT DISTINCT subject FROM classroom WHERE teacher_id = $teacher_id AND class = '$class'";
            $result = mysqli_query($this->conn, $query);
            if (mysqli_num_rows($result) == 1) {
                return mysqli_fetch_assoc($result)['subject'];
            }
            else {
                return null;
            }
        }

        public function saveAttendance(int $student_id, string $status, string $date, int $period): bool {
            $query = "INSERT INTO student_attendance VALUES ('$student_id','$status','$date','$period')";
            return mysqli_query($this->conn, $query);
        }

    }
?>
