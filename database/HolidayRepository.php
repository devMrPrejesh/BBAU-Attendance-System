<?php
    require_once ('DBConnector.php');

    class HolidayRepository extends DBConnector {

        public function getAll(): array {
            $query = "SELECT * FROM holiday_calendar";
            $result=mysqli_query($this->conn, $query);
            return Utils::convertDBRecordstoArray($result);
        }
        
    }
?>