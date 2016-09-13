<?php
/**
 * Description of RequestResponse
 *
 * Created on 12/09/2016, 21:37:27
 * @author Matheus Leonardo dos Santos Martins
 * @copyright (c) 2016, TRIAL
 * 
 * @package TRIAL
 */
class RequestResponse extends Response {
    
    public function __construct($response) {
        parent::__construct($response !== false);
        if ($response === false) {
            if ($errno = curl_errno($ch)) {
                $this->setError(new Error($errno, curl_strerror($errno)));
            }
        } else {
            if ($response !== true) {
                $this->setResult($response);
            }
        }
    }
    
}
