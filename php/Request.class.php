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

require_once 'RequestResponse.class.php';

// 18/10/2016, 01:19:02 - 01:28:38 => class Request changed: now have only the static function make(), and returns now techinical infos about the request
class Request {
    
    // 19/12/2016, 17:52:30 => added cURL $options
    public static function make($url, $data = null, $options = []) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        if ($data != null) {
            $type_data = gettype($data);
            if ($type_data === 'array' || $type_data === 'string' || $type_data === 'object') {
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $type_data === 'string' ? $data : http_build_query($data));
            } else {
                throw new InvalidArgumentException('$data argument isn\'t some of the expected types! The accepted ones are: string, array, or object value');
            }
        }
        if (count($options) > 0) {
            foreach ($options as $option) {
                curl_setopt($ch, $option[0], $option[1]);
                
            }
        }
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        return new RequestResponse($ch);
    }
    
}
