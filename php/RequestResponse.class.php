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
    
    public function __construct($ch) {
        $response = curl_exec($ch);
        parent::__construct($response !== false);
        if ($response === false) {
            $errno = curl_errno($ch);
            if ($errno) {
                $this->setError(new Error(curl_strerror($errno), $errno));
            }
        } else {
            $this->setResult([
                'response' => $response,
                'http_code' => curl_getinfo($ch, CURLINFO_HTTP_CODE)
            ]);
        }
    }
    
}
