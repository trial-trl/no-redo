<?php
/**
 * Description of CommonQuery
 *
 * Created on 24/09/2016, 23:57:39
 * @author Matheus Leonardo dos Santos Martins
 * @copyright (c) 2016, TRIAL
 * 
 * @version 1.0
 * @package SQL
 */

/* 
 * 21/01/2017
 *      03:19:19 => renamed namespace from SQL to Utils\SQL
 *      18:16:35 => renamed namespace from Utils\SQL to NoRedo\Utils\SQL
 */

namespace NoRedo\Utils\SQL;

use \PDO;

abstract class CommonQuery {
    
    public $conn;
    public $statement;
    
    /**
     * 
     * 
     * @param PDO $conn
     */
    public function __construct(PDO $conn) {
        $this->conn = $conn;
    }
    
    /**
     * 
     * 
     * @param PDO $conn
     */
    public function conn(PDO $conn) {
        $this->conn = $conn;
        return $this;
    }
    
}
