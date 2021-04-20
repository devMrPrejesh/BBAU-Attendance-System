<?php
    require_once ('DBConnector.php');

    class HolidayRepository extends DBConnector {

        public function getAll(): array {
            $result=mysqli_query($this->conn, "select * from holiday_calendar");
            return Utils::convertDBRecordstoArray($result);
        }
        
    }
?>