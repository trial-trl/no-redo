<?php
/**
 * Description of Request
 *
 * Created on 12/09/2016, 19:15:23
 * @author Matheus Leonardo dos Santos Martins
 * @copyright (c) 2016, TRIAL
 * 
 * @package TRIAL
 */

require_once 'RequestResponse.php';

class Request {
    
    private $curl;
    
    public function __construct() {
        $this->curl = curl_init();
    }
    
    public function url($url) : Request {
        curl_setopt($this->curl, CURLOPT_URL, $url);
        return $this;
    }
    
    public function data($data) : Request {
        $type_data = gettype($data);
        if ($type_data === 'array' | 'string' | 'object') {
            curl_setopt($this->curl, CURLOPT_POST, true);
            curl_setopt($this->curl, CURLOPT_POSTFIELDS, $type_data === 'string' ? $data : http_build_query($data));
            return $this;
        } else {
            throw new InvalidArgumentException('$data argument isn\'t some of the expected types! The accepted ones are: string, array, or object value');
        }
    }
    
    public function make() : RequestResponse {
        return new RequestResponse(curl_exec($this->curl));
    }
    
}
