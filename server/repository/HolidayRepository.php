<?php
    require_once ('DBConnector.php');

    class HolidayRepository extends DBConnector {

        public function existById(string $date): bool {
            $query = "SELECT * FROM holiday_calendar WHERE date='$date'";
            $result = mysqli_query($this->conn, $query);
            if (mysqli_num_rows($result) == 1) {
                return TRUE;
            }
            else {
                return FALSE;
            }
        }

        public function getAllOrderById(): array {
            $query = "SELECT * FROM holiday_calendar ORDER BY date";
            $result=mysqli_query($this->conn, $query);
            return $this->convertDBRecordstoArray($result);
        }
        
    }
?>
