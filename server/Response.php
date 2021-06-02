<?php
    class ResponseEntity {
        private string $message;
        private int $status_code;
        
        public function __construct(string $message, int $status_code=200) {
            $this->message = $message;
            $this->status_code = $status_code;
        }
        
        public function getMessage(): string {
            return $this->message;
        }
        
        public function getStatusCode(): int {
            return $this->status_code;
        }
    }

    class ResponseException extends Exception {
        private int $status_code;
        
        public function __construct(string $message, int $status_code=400) {
            parent::__construct($message);
            $this->status_code = $status_code;
        }
        
        public function getStatusCode(): int {
            return $this->status_code;
        }
    }
?>