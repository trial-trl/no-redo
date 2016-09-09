<?php
/**
 * Description of ConnectDB
 *
 * Created on 08/09/2016, 12:47:21
 * @author Matheus Leonardo dos Santos Martins
 * @copyright (c) 2016, TRIAL
 * 
 * @package TRIAL
 */

require_once 'utils/Constant.php';

class ConnectDB {
    
    private $database = DB_DATABASE;
    private $host = 'localhost';
    private $username = DB_USER;
    private $password = DB_PASSWORD;
    private $database_name;
    
    public function __construct($database_name) {
        $this->database_name = $database_name;
    }
    
    public function setDatabase($database) {
        $this->database = $database;
        return $this;
    }
    
    public function setHost($host) {
        $this->host = $host;
        return $this;
    }
    
    public function setUsername($username) {
        $this->username = $username;
        return $this;
    }
    
    public function setPassword($password) {
        $this->password = $password;
        return $this;
    }
    
    public function connect() {
        try {
            $con = new PDO("$this->database:host=$this->host;dbname=$this->database_name", $this->username, $this->password);
            $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $con;
        } catch (PDOException $e) {
            die($e->getMessage());
        }
    }
}