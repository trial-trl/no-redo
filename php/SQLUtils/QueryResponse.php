<?php
/**
 * Description of QueryResponse
 * 
 * Created on 06/09/2016, ~21:49:40
 * @author Matheus Leonardo dos Santos Martins
 * @copyright (c) 2016, TRIAL
 * 
 * @package SQLUtils
 */
class QueryResponse {
    
    public function existRows() {
        return null;
    }
    
    public function error() {
        return true;
    }
    
    public function getError() {
        return new QueryError();
    }

}

class QueryError {
    
    private $code;
    private $sqlstate;
    private $message;
    
    public function __construct(array $error_info) {
        $this->code = $error_info[0];
        $this->sqlstate = $error_info[1];
        $this->message = $error_info[2];
    }
    
    public function getCode() {
        return $this->code;
    }
    
    public function getSQLState() {
        return $this->sqlstate;
    }
    
    public function getMessage() {
        return $this->message;
    }
    
}