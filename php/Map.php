<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 * 
 * Last edition: 18/06/2016
 */

include_once $_SERVER['DOCUMENT_ROOT'] . '/repository/php/ConnectDB.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/repository/php/utils/Constant.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/repository/php/utils/Utils.php';

date_default_timezone_set('America/Sao_Paulo');
$request = json_decode(file_get_contents('php://input'));

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

if ($request != null) {
    $r = $request->{'r'};
    $requestType = 'mobile';
} else {
    if ($_SERVER['REQUEST_METHOD'] == 'GET') {
	$r = filter_input(INPUT_GET, 'r');
    } else {
	$r = filter_input(INPUT_POST, 'r');
    }
    $requestType = 'web';
    $REQUEST = $_SERVER['REQUEST_METHOD'] == 'GET' ? INPUT_GET : INPUT_POST;
}

$instance_class = new Map();
if ($requestType === 'mobile') {
    switch ($r) {
        case 'gstats':
            echo json_encode($instance_class->getStates());
            break;
        case 'gcntes':
            echo json_encode($instance_class->getCounties($request->{'state'}));
            break;
    }
} else if ($requestType === 'web') {
    switch ($r) {
        case 'gstats':
            echo json_encode($instance_class->getStates());
            break;
        case 'gcntes':
            echo json_encode($instance_class->getCounties(filter_input($REQUEST, 'state')));
            break;
    }
}