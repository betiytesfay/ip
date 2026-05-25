<?php
class Database {
    private $host = "localhost";
    private $user = "root";
    private $pass = "";
    private $db = "dms";
    public $conn;
    
    public function __construct() {
        $this->conn = mysqli_connect($this->host, $this->user, $this->pass, $this->db);
        if (!$this->conn) {
            die("Connection failed: " . mysqli_connect_error());
        }
    }
    
    public function query($sql) {
        return mysqli_query($this->conn, $sql);
    }
    
    public function escape($string) {
        return mysqli_real_escape_string($this->conn, $string);
    }
    
    public function __destruct() {
        mysqli_close($this->conn);
    }
}
?>