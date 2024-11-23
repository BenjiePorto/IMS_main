<?php

    class Database {
        private $host;
        private $db_name;
        private $username;
        private $password;
        public $conn;

        public function __construct()
        {
            if ($_SERVER['SERVER_NAME'] === 'localhost' || $_SERVER['SERVER_ADDR'] === '127.0.0.1' || $_SERVER['SERVER_ADDR'] === '192.168.1.72'){
                $this->host = "localhost";
                $this->db_name = "ims";
                $this->username = "root";
                $this->password = "";
            } else {
                $this->host = "localhost";
                $this->db_name = "";
                $this->username = "";
                $this->password = "";
            }
        }

        public function dbConnection() 
        {
            $this->conn = null;

            try {
                $dsn = "mysql:host=" . $this->host . ";dbname=" . $this->db_name . ";port=3307"; 
                $this->conn = new PDO($dsn, $this->username, $this->password);
                $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                echo "Database connected successfully.<br>";
            } catch (PDOException $exception) {
                die("Connection error: " . $exception->getMessage());
            }

            return $this->conn;
        }
    }
?>