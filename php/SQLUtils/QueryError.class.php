<?php
/**
 * Description of QueryError
 * 
 * Created on 12/09/2016, 21:22:03
 * @author Matheus Leonardo dos Santos Martins
 * @copyright (c) 2016, TRIAL
 * 
 * @package SQLUtils
 */

namespace SQL;

use Error, JsonSerializable;

require_once __DIR__ . '/../utils/Error.class.php';

class QueryError extends Error implements JsonSerializable {
    
    private $sqlstate;
    
    public function __construct(array $error_info) {
        parent::__construct($error_info[2], $error_info[0]);
        $this->sqlstate = $error_info[1];
    }
    
    public function getSQLState() {
        return $this->sqlstate;
    }

    public function jsonSerialize() {
        return ['code' => $this->getCode(), 'sqlstate' => $this->getSQLState(), 'message' => $this->getMessage()];
    }

}