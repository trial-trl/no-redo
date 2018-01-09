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

/* 
 * 18/10/2016, 01:22:53 => now construct takes a cURL argument, and in addiction to response returns now techinical infos about the request
 * 
 * 21/01/2017, 20:03:19 => added namespace NoRedo\Utils
 */

namespace NoRedo\Utils;

use \Error;

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
