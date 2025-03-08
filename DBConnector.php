<?php

class DatabaseConnection {
    private $servername;
    private $username;
    private $password;
    private $dbname;
    private $conn;
    
    /**
     * @param string $servername 
     * @param string $username 
     * @param string $password 
     * @param string $dbname 
     */
    public function __construct($servername, $username, $password, $dbname) {
        $this->servername = $servername;
        $this->username = $username;
        $this->password = $password;
        $this->dbname = $dbname;
        $this->connect();
    }
    
   
    private function connect() {
        $this->conn = new mysqli($this->servername, $this->username, $this->password, $this->dbname);
        
        if ($this->conn->connect_error) {
            throw new Exception("Connection failed: " . $this->conn->connect_error);
        }
    }
    
    /**
     * @return mysqli 
     */
    public function getConnection() {
        return $this->conn;
    }
    
    /**
     * Close database connection
     */
    public function closeConnection() {
        if ($this->conn) {
            $this->conn->close();
        }
    }
    
    public function __destruct() {
        $this->closeConnection();
    }
}

