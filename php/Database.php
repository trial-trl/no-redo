<?php
/**
 * Description of Database
 *
 * Created on 07/12/2016, 18:07:16
 * @author Matheus Leonardo dos Santos Martins
 * @copyright (c) 2016, TRIAL
 * 
 * @package TRIAL
 * 
 * @version 1.1
 */

/*
 * 13/01/2017, 19:37:20 => DB changed to Database; exchanged variables to constants only.
 */

class Database {
    
    const DATABASE = 'mysql';
    const HOST = 'localhost';
    const USER = 'root';//trial105
    const PASSWORD = '';//i1eDq8*5lVwHLk*!gB
    
    public static function connect($database) {
        try {
            $con = new PDO(self::DATABASE . ':host=' . self::HOST . ';dbname=' . $database, self::USER, self::PASSWORD);
            $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $con;
        } catch (PDOException $e) {
            die($e->getMessage());
        }
    }
    
}
