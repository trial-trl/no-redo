<?php
/**
 * Description of Response
 * 
 * Created on 12/09/2016, 21:26:30
 * @author Matheus Leonardo dos Santos Martins
 * @copyright (c) 2016, TRIAL
 * 
 * @package Utils
 */
class Response {
    
    private $success;
    private $result;
    private $error;
    
    public function __construct($success) {
        $this->success = $success;
    }
    
    public function success() {
        return $this->success;
    }
    
    public function getResult() {
        return $this->result;
    }
    
    public function getError() {
        return $this->error;
    }
    
    protected function setResult($result) {
        $this->result = $result;
    }
    
    protected function setError(Error $error) {
        $this->error = $error;
    }
    
}
