<?php
    require_once ('DBConnector.php');

    class HolidayRepository extends DBConnector {

        public function getAll(): array {
            $result=mysqli_query($this->conn, "SELECT * FROM holiday_calendar");
            return Utils::convertDBRecordstoArray($result);
        }
        
    }
?>