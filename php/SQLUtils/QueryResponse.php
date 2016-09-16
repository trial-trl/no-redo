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

require_once __DIR__ . '/../utils/Response.php';
require_once 'QueryError.php';

class QueryResponse extends Response {
    
    private $statement;
    
    public function __construct(&$statement, $bind, $success) {
        $this->statement = $statement;
        parent::__construct($this->statement->execute($bind));
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

}