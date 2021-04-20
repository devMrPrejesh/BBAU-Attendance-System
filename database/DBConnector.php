<?php
    abstract class DBConnector {
        private string $server = "localhost";
        private string $user_name = "root";
        private string  $password = "";
        private string  $db_name = "bbau_attendance_system";
        
        protected $conn;

        function __construct(string $server=null, string $user_name=null, string $password=null, string $db_name=null) {
            $this->setConfig($server, $user_name, $password, $db_name);
            $this->conn = mysqli_connect($this->server, $this->user_name, $this->password) or die('Cannot connect to server');
            mysqli_select_db($this->conn, $this->db_name) or die ('Cannot found database');
        }

        private function setConfig($server, $user_name, $password, $db_name) {
            if ($server) {
                $this->server = $server;
            }
            if ($user_name) {
                $this->user_name = $user_name;
            }
            if ($password) {
                $this->password = $password;
            }
            if ($db_name) {
                $this->db_name = $db_name;
            }
        }

        function __destruct() {
            mysqli_close($this->conn);
        }
        
    }
?>