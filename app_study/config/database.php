
<?php
class Database {
    private $host = "localhost";
    private $db = "quizlet_clone";
    private $user = "root";
    private $pass = "";
    public $conn;

    public function connect(){
        $this->conn = null;
        try{
            $this->conn = new PDO(
                "mysql:host=".$this->host.";dbname=".$this->db,
                $this->user,
                $this->pass
            );
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }catch(PDOException $e){
            echo "Connection error: ".$e->getMessage();
        }
        return $this->conn;
    }
}
