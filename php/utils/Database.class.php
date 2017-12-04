<?php
/**
 * Description of Database
 *
 * Created on 07/12/2016, 18:07:16
 * @author Matheus Leonardo dos Santos Martins
 * @copyright (c) 2016, TRIAL
 * 
 * @version 1.1
 * @package TRIAL
 */

/*
 * 13/01/2017, 19:37:20 => DB changed to Database; exchanged variables to constants only.
 * 
 * 21/01/2017, 19:51:39 => added namespace NoRedo\Utils
 * 
 * 18/02/2017
 *      22:33:24
 *          changed => const [DATABASE, HOST, USER, PASSWORD] to private static [$driver, $host, $user, $password];
 *          added => setAuth(string $user, string $password), setDriver(string $driver), setHost(string $host)
 */

namespace NoRedo\Utils;

use \PDO;

class Database {
    
    private static $driver = 'mysql';
    private static $host = '216.172.161.44';
    private static $user = 'trial105';//root
    private static $password = 'i1eDq8*5lVwHLk*!gB';//
    
    public static function setAuth(string $user, string $password) {
        self::$user = $user;
        self::$password = $password;
    }
    
    public static function setDriver(string $driver) {
        self::$driver = $driver;
    }
    
    public static function setHost(string $host) {
        self::$host = $host;
    }
    
    public static function connect($database) {
        try {
            $con = new PDO(self::$driver . ':host=' . self::$host . ';dbname=' . $database, self::$user, self::$password);
            $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $con;
        } catch (PDOException $e) {
            die($e->getMessage());
        }
    }
    
}
