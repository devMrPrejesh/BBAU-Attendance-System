<?php
    require_once ('DBConnector.php');

    class StudentRepository extends DBConnector {

        public function findNameById(int $student_id): ?string {
            $result=mysqli_query($this->conn, "select student_name from student where student_id='$student_id'");
            if (mysqli_num_rows($result) != 1) {
                return null;
            }
            else {
                return mysqli_fetch_assoc($result)['student_name'];
            }
        }

        public function findPeriodSizeById(int $student_id): ?string {
            $result=mysqli_query($this->conn, "select number_of_subjects from student where student_id='$student_id'");
            if (mysqli_num_rows($result) != 1) {
                return null;
            }
            else {
                return mysqli_fetch_assoc($result)['number_of_subjects'];
            }
        }

        public function findAttendanceByIdOrderByDateandPeriod(int $student_id): array {
            $result=mysqli_query($this->conn, "SELECT * FROM student_attendance WHERE student_id = '$student_id' ORDER BY date, period");
            return Utils::convertDBRecordstoArray($result);
        }
        
        public function findClassRoomByIdOrderByDayandPeriod(int $student_id): array {
            $result=mysqli_query($this->conn, "SELECT c.* FROM classroom as c JOIN student as s ON c.class = s.class WHERE s.student_id='$student_id' ORDER BY day, period");
            return Utils::convertDBRecordstoArray($result);
        }

        public function findSubjectandTeacherNameById(int $student_id): array {
            $result=mysqli_query($this->conn, "SELECT DISTINCT c.subject, t.teacher_name FROM classroom as c JOIN student as s ON c.class = s.class JOIN teacher as t ON t.teacher_id=c.teacher_id WHERE s.student_id='$student_id'");
            return Utils::convertDBRecordstoArray($result);
        }

        public function findAllByID(int $student_id) {
            $result=mysqli_query($this->conn, "SELECT * FROM student WHERE student_id = '$student_id'");
            return mysqli_fetch_assoc($result);
        }
        
    }
?>