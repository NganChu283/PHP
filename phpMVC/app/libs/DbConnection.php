<?php

    class DBConnection{
        private $host;
        private $user;
        private $pass;
        private $dbname;
        private $conn;

        public function __construct($host, $user, $pass, $dbname) {
            $this->host = $host;
            $this->user = $user;
            $this->pass = $pass;
            $this->dbname = $dbname;

            try {
            $sqlStr = "mysql:host=$this->host;dbname=$this->dbname;charset=utf8mb4";
            $this->conn = new PDO($sqlStr, $this->user, $this->pass);
            } catch (PDOException $e) {
             $this->conn = null;
            }   
        }
        
        
        

        public function getConnection() {
            return $this->conn;
        }

    }


?>