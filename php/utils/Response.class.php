<?php
/**
 * Description of Response
 * 
 * Created on 12/09/2016, 21:26:30
 * @author Matheus Leonardo dos Santos Martins
 * @copyright (c) 2016, TRIAL
 * 
 * @version 1.0
 * @package SQL
 */

/*
 * 21/01/2017
 *      03:19:19 => added namespace Utils
 *      19:56:36 => renamed namespace from Utils to NoRedo\Utils
 */

namespace NoRedo\Utils;

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
    
    protected function setError(\Error $error) {
        $this->error = $error;
    }
    
}
