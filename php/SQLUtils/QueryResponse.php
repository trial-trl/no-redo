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
    
    private $statement;
    private $success;
    private $query_error;
    private $result;
    
    public function __construct(&$statement, &$bind, $success) {
        $this->statement = $statement;
        $this->success = $this->statement->execute($bind);
        if ($this->success()) {
            if ($success) {
                $this->setResult($success());
            }
        } else {
            $this->setError(new QueryError($this->statement->errorInfo()));
        }
    }
    
    public function existRows() {
        return $this->success() ? $this->statement->rowCount() > 0 : false;
    }
    
    public function success() {
        return $this->success;
    }
    
    private function setResult($result) {
        $this->result = $result;
    }
    
    public function getResult() {
        return $this->result;
    }
    
    private function setError(QueryError $error) {
        $this->query_error = $error;
    }
    
    public function getError() {
        return $this->query_error;
    }

}

class QueryError implements JsonSerializable {
    
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

    public function jsonSerialize() {
        return ['code' => $this->getCode(), 'sqlstate' => $this->getSQLState(), 'message' => $this->getMessage];
    }

}