<?php
/**
 * Description of Error
 * 
 * Created on 12/09/2016, 21:18:05
 * @author Matheus Leonardo dos Santos Martins
 * @copyright (c) 2016, TRIAL
 * 
 * @package Utils
 */
class Error {
    
    private $code;
    private $message;
    
    public function __construct($code, $message) {
        $this->code = $code;
        $this->message = $message;
    }
    
    public function getCode() {
        return $this->code;
    }
    
    public function getMessage() {
        return $this->message;
    }
    
}
