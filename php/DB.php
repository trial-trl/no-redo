<?php
/**
 * Description of DB
 *
 * Created on 07/12/2016, 18:07:16
 * @author Matheus Leonardo dos Santos Martins
 * @copyright (c) 2016, TRIAL
 * 
 * @package TRIAL
 * 
 * @version 1.1
 */

class DB {
    
    const DATABASE = 'mysql';
    const USER = 'root';//trial105
    const PASSWORD = '';//i1eDq8*5lVwHLk*!gB
    
    private static $database = DB_DATABASE;
    private static $host = 'localhost';
    private static $username = DB_USER;
    private static $password = DB_PASSWORD;
    
    public static function connect($database) {
        try {
            $con = new PDO(self::$database . ':host=' . self::$host . ';dbname=' . $database, self::$username, self::$password);
            $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $con;
        } catch (PDOException $e) {
            die($e->getMessage());
        }
    }
    
}
