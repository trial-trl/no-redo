<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Map {
    
    private $con;
    
    public function __construct() {
        $con = new ConnectDB(DB_DATABASE, DB_USER, DB_PASSWORD, DB_PREFIX . 'map');
        $this->con = $con->connect();
    }
    
    public function getStates() {
        $states = selectDB($this->con, 'states', 'id, abbr AS name', null, null);
        $states['message'] = $states != null ? MESSAGE_EXIST : MESSAGE_NOT_EXIST;
        return $states;
    }
    
    public function getCounties($state) {
        $counties = selectDB($this->con, 'counties', 'id, county', 'WHERE state = :state', array(':state' => $state));
        $counties['message'] = $counties != null ? MESSAGE_EXIST : MESSAGE_NOT_EXIST;
        return $counties;
    }
    
}