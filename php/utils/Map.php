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
 * @version 1.11NC
 */

namespace NoRedo\Utils;

use NoRedo\Utils\Database, NoRedo\Utils\SQL\Query, NoRedo\Utils\SQL\Select;

/*
 * 21/10/2016
 *      02:02:26 => updated ConnectDB deprecated code in __construct
 *      02:10:22 => added buildResponse(); all deprecated code was updated
 *      02:12:40 => all $_SERVER codes changed to filter_input()
 * 
 * 01/05/2017 (v1.11NC) | Updated all codes to recent NoRedo repository codes
 *      18:55:44
 *              moved to Utils directory
 *              removed buildResponse() and replaced to Query::helper
 *              added namespace NoRedo\Utils and 'use' keyword
 *      19:01:15
 *              added con() method
 *      19:02:23 => all methods are now static methods
 */
class Map {
    
    private static $con;
    
    /**
     * @return \PDO
     * 
     * @version 1.0
     * @since 1.11NC
     */
    public static function con() : \PDO {
        if (!self::$con) {
            self::$con = Database::connect(DB_PREFIX . 'map');
        }
        return self::$con;
    }
    
    /**
     * @return array States
     * 
     * @version 1.01
     * @since 1.0
     */
    public static function getStates() : array {
        return Query::helper((new Select(self::con()))->table('states')->columns('id, abbr AS name')->run(), function ($query) {
            if ($query->existRows()) {
                $result = $query->getResult();
                $result['message'] = Message::EXIST;
            } else {
            	$result['message'] = Message::NOT_EXIST;
            }
            return $result;
        });
    }
    
    /**
     * @return array Counties
     * 
     * @version 1.01
     * @since 1.0
     */
    public static function getCounties($state) : array {
        if (gettype($state) === 'string') {
            $query = (new Select(self::con()))->table('states AS s')->columns('c.id, c.county')->leftJoin(['counties AS c ON c.state = s.id'])->where('s.abbr = :abbr')->values([':abbr' => $state]);
        } else {
            $query = (new Select(self::con()))->table('counties')->columns('id, county')->where('state = :state')->values([':state' => $state]);
        }
        return Query::helper($query->run(), function ($query) {
            $result = $query->getResult();
            $result['message'] = $query->existRows() ? Message::EXIST : Message::NOT_EXIST;
            return $result;
        });
    }
    
}

$request = json_decode(file_get_contents('php://input'));
$r = $request->{'r'} ?? filter_input($method = ($_SERVER['REQUEST_METHOD'] === 'GET' ? INPUT_GET : INPUT_POST), 'r');
if (!empty($r)) {
    if (!empty($request)) {
        switch ($r) {
            case 'gstats':
                echo json_encode(Map::getStates());
                break;
            case 'gcntes':
                echo json_encode(Map::getCounties($request->{'state'}));
                break;
        }
    } else {
        switch ($r) {
            case 'gstats':
                echo json_encode(Map::getStates());
                break;
            case 'gcntes':
                echo json_encode(Map::getCounties(filter_input($method, 'state')));
                break;
        }
    }
}