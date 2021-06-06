<?php
    abstract class DBConnector {
        private string $servername = "localhost";
        private string $username = "root";
        private string  $password = "";
        private string  $dbname = "attendance_database";
        
        protected $conn;

        function __construct(bool $config_flag=FALSE, array $args=null) {
            if ($config_flag) {
                $this->setConfig($args);
            }
            $this->conn = mysqli_connect($this->servername, $this->username, $this->password) or die('Cannot connect to server.');
            mysqli_select_db($this->conn, $this->dbname) or die ('Cannot found database.');
        }

        function __destruct() {
            mysqli_close($this->conn);
        }

        private function setConfig(array $args): void {
            if (array_key_exists('servername', $args)) {
                $this->servername = $args['servername'];
            }
            if (array_key_exists('username', $args)) {
                $this->username = $args['username'];
            }
            if (array_key_exists('password', $args)) {
                $this->password = $args['password'];
            }
            if (array_key_exists('dbname', $args)) {
                $this->dbname = $args['dbname'];
            }
        }

        protected static function convertDBRecordstoArray(object $records): array {
            $result = array();
            while($row = mysqli_fetch_assoc($records)) {
                array_push($result, $row);
            }
            return $result;
        }
        
    }
?>
