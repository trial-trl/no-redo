<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class ConnectDB {
    
    private $database;
    private $username;
    private $password;
    private $database_name;
    
    public function __construct($database, $username, $password, $database_name) {
        $this->database = $database;
        $this->username = $username;
        $this->password = $password;
        $this->database_name = $database_name;
    }
    
    public function connect() {
        try {
            $con = new PDO("$this->database:host=localhost;dbname=$this->database_name", $this->username, $this->password);
            $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $con;
        } catch (PDOException $e) {
            die($e->getMessage());
        }
    }
}