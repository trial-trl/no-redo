<?php

/* 
 * Description of Map
 * 
 * Last edition: 18/06/2016 22h08min
 *
 * Created on ??/??/2016
 * @author Matheus Leonardo dos Santos Martins
 * @copyright (c) 2016, TRIAL
 * 
 * @package TRIAL
 * 
 * @version 1.1NC
 */

include_once filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . '/repository/php/Files.php';

date_default_timezone_set('America/Sao_Paulo');
$request = json_decode(file_get_contents('php://input'));

/*
 * 21/10/2016
 *      02:02:26 => updated ConnectDB deprecated code in __construct
 *      02:10:22 => added buildResponse(); all deprecated code was updated
 *      02:12:40 => all $_SERVER codes changed to filter_input()
 */
class Map {
    
    private $con;
    
    public function __construct() {
        $this->con = (new ConnectDB(DB_PREFIX . 'map'))->connect();
    }
    
    private function buildResponse($query, $callback) : array {
        $query_response = $query instanceof QueryResponse ? $query : $query->run();
        if ($query_response->success()) {
            return $callback($query_response);
        } else {
            return ['error' => $query_response->getError(), 'message' => Message::ERROR];
        }
    }
    
    public function getStates() : array {
        return $this->buildResponse((new Select($this->con))->table('states')->columns('id, abbr AS name')->run(), function ($query) {
            if ($query->existRows()) {
                $result = $query->getResult();
                $result['message'] = Message::EXIST;
            } else {
            	$result['message'] = Message::NOT_EXIST;
            }
            return $result;
        });
    }
    
    public function getCounties($state) : array {
        return $this->buildResponse((new Select($this->con))->table('counties')->columns('id, county')->where('state = :state')->values([':state' => $state])->run(), function ($query) {
            if ($query->existRows()) {
                $result = $query->getResult();
                $result['message'] = Message::EXIST;
            } else {
            	$result['message'] = Message::NOT_EXIST;
            }
            return $result;
        });
    }
    
}

if ($request != null) {
    $r = $request->{'r'};
    $requestType = 'mobile';
} else {
    if (filter_input(INPUT_SERVER, 'REQUEST_METHOD') == 'GET') {
	$r = filter_input(INPUT_GET, 'r');
    } else {
	$r = filter_input(INPUT_POST, 'r');
    }
    $requestType = 'web';
$REQUEST = filter_input(INPUT_SERVER, 'REQUEST_METHOD') == 'GET' ? INPUT_GET : INPUT_POST;
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