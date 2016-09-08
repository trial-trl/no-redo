<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class ConnectDB {
    
    private $database = 'mysql';
    private $host = 'localhost';
    private $username = 'root';
    private $password = '';
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