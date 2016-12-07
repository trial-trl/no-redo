<?php
/**
 * Description of CommonQuery
 *
 * Created on 24/09/2016, 23:57:39
 * @author Matheus Leonardo dos Santos Martins
 * @copyright (c) 2016, TRIAL
 * 
 * @package SQLUtils
 */

namespace SQL;

use PDO;

require_once 'QueryResponse.php';

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
