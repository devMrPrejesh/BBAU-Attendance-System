<?php
    class Utils {
        public static function constructMSG(string $message, string ...$args): string {
            foreach ($args as $arg) { $message = preg_replace("/\[%s\]/", $arg, $message, 1); }
            return $message;
        }

        public static function isEmail(string $email): bool {
            if (preg_match("/^[A-Za-z0-9\._-]+@[A-Za-z]+\.[A-Za-z]+$/", $email)){
                return TRUE;
            }
            else {
                return FALSE;
            }
        }

        public static function isDate(string $date): bool {
            if (preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $date)){
                return TRUE;
            }
            else {
                return FALSE;
            }
        }

        public static function generatePassword(): string {
            $special_chars = array("@", "#", "$", "_", "-", "+", "*", "&");
            $pass_chars = array(
                chr(rand(0, 25) + 65),
                chr(rand(0, 25) + 97),
                chr(rand(0, 25) + 97),
                chr(rand(0, 9) + 48),
                $special_chars[rand(0, sizeof($special_chars)-1)],
                chr(rand(0, 9) + 48),
                chr(rand(0, 25) + 97),
                chr(rand(0, 25) + 65),
                chr(rand(0, 25) + 97),
                chr(rand(0, 25) + 97),
            );
            return join("", $pass_chars);
        }

        public static function constructRecords(array $records, int $period_size): array {
            $data = array();
            $current_fullDate = $records[0]['date'];
            $date = strtotime($current_fullDate);
            $current_date = (int) date('d', $date);
            $current_month = (int) date('m', $date);
            $data[$current_month-1] = array();
            $status_array = array();
            
            foreach ($records as $record) {
                if ($current_fullDate != $record['date']) {
                    $date = strtotime($record['date']);
                    array_push($data[$current_month-1], self::checkStatus($current_date, $period_size, $status_array));
                    if ($current_month != (int) date('m', $date)){
                        $current_month = (int) date('m', $date);
                        $data[$current_month-1] = array();
                    }
                    $current_date = (int) date('d', $date);
                    $current_fullDate = $record['date'];
                    $status_array = array();
                }
                array_push($status_array, $record['status']);
            }
            array_push($data[$current_month-1], self::checkStatus($current_date, $period_size, $status_array));
            return $data;
        }

        private static function checkStatus(int $current_date, int $period_size, array $status_array): array {
            if (count($status_array) != $period_size) {
                return array("date"=>$current_date, "status"=>"PARTIAL", "lecture_details"=>$status_array);
            }
            if (count(array_unique($status_array)) != 1) {
                return array("date"=>$current_date, "status"=>"PARTIAL", "lecture_details"=>$status_array);
            }
            else{
                return array("date"=>$current_date, "status"=>$status_array[0]);
            }
        }
        
        public static function constructTimetable(array $timetable, int $period_size, string $key): array {
            $data = array();
            $data['periodSize'] = $period_size;
            for ($i=0; $i < 5; $i++) {
                $day_table = array();
                for ($j=0; $j < $period_size; $j++) {
                    array_push($day_table, $timetable[$i*$period_size+$j][$key]);
                }
                $data[$i] = $day_table;
            }
            return $data;
        }

        public static function constructTimetableInfo(array $info_data, string $keyLabel, string $valueLabel): array {
            $data = array();
            foreach ($info_data as $value) {
                $pair = array();
                $pair['key'] = $value[$keyLabel];
                $pair['value'] = $value[$valueLabel];
                array_push($data, $pair);
            }
            return $data;
        }
        
        public static function sendMail(string $email_id, string $subject, string $source, bool $template_flag=FALSE, array $substitute=null): bool {
            $headers = "From: BBAU Admin <singhaman.0628@gmail.com>\r\n";
            $headers .= "MIME-Version: 1.0\r\n";
            $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
            
            if ($template_flag) {
                $source = file_get_contents($source);
                foreach ($substitute as $key => $value) {
                    $source = str_replace($key, $value, $source);
                }
            }

            if (mail($email_id, $subject, $source, $headers)) {
                return TRUE;
            } else {
                return FALSE;
            }
        }

        public static function validatePassword(string $password): bool {
            $containLength = strlen($password) > 7 && strlen($password) < 13;
            $containUpper = preg_match('/[A-Z]/', $password);
            $containLower = preg_match('/[a-z]/', $password);
            $containNumber = preg_match('/[0-9]/', $password);
            $containSpecial = preg_match('/[@#$_\-+*&]/', $password);
            
            if ($containLength && $containLower && $containNumber && $containSpecial && $containUpper) {
                return TRUE;
            }
            else {
                return FALSE;
            }
        }

        private static function getNextFileName(): string {
            $lastFileNumber = 1;
            if ($handle = opendir(LeaveAttachment::PATH)) {
                while (($entry = readdir($handle)) !== FALSE) {
                    if ($entry != "." && $entry != ".." && str_starts_with($entry, "FILE")) {
                        preg_match("/^FILE([0-9]+)\..+$/", $entry, $result);
                        $result = intval($result[1]);
                        $lastFileNumber = $result > $lastFileNumber? $result: $lastFileNumber;
                    }
                }
                closedir($handle);
            }
            $lastFileNumber++;
            return "FILE".$lastFileNumber;
        }

        public static function addUploadedFile(string $file_name, string $tmp_name): string {
            $name = self::getNextFileName().".".pathinfo($file_name, PATHINFO_EXTENSION);
            if (move_uploaded_file($tmp_name, LeaveAttachment::PATH.$name)) {
                return $name;
            }
            else {
                throw new ResponseException(ExceptionMSG::FILE_NOT_MOVED, 501);
            }
        }
 
    }
?>