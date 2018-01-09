<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

function selectDB($con, $table, $columns, $whereClause, $inputParameters) {
    $response = []; $count = 0;
    $shd = $con->prepare("SELECT $columns FROM $table $whereClause");
    $shd->execute($inputParameters);
    if ($shd->rowCount()) {
        while ($r = $shd->fetch(PDO::FETCH_ASSOC)) {
            $response[$count] = $r;
            $count++;
        }
        $response['total'] = $count;
    }
    return $response;
}
    
function insertDB($con, $table, $columns, $values, $select = null) {
    if ($select == null) {
	$names = createParameterNames($columns);
	$input_parameters = createArrayParameters($names['exploded'], $values);
    	$sql = "INSERT INTO $table ($columns) VALUES (" . $names['imploded'] . ")";
    } else {
	$input_parameters = $values;
    	$sql = $columns != null ? "INSERT INTO $table ($columns) $select" : "INSERT INTO $table $select";
    }
    $con->prepare($sql)->execute($input_parameters);
    $result['id'] = $con->lastInsertId();
    return $result;
}
    
function updateDB($con, $table, $values, $where, $input_parameters) {
    $names = createParameterNames($values);
    $prepare_where = createParameterNames($where);
    $new_values = createUpdateDBString($values, $names['exploded']);
    $new_where = createUpdateDBString($where, $prepare_where['exploded'], ' AND ');
    $total = count($names['exploded']);
    for ($i = 0, $total_where = count($prepare_where['exploded']); $i < $total_where; $i++) {
        $new_total = $total + $i;
        $names['exploded'][$new_total] = $prepare_where['exploded'][$i];
    }
    $new_input_parameters = createArrayParameters($names['exploded'], $input_parameters);
    return $con->prepare("UPDATE $table SET $new_values WHERE $new_where")->execute($new_input_parameters);
}
    
function deleteDB($con, $table, $where, $inputParameters) {
    return $con->prepare("DELETE FROM $table WHERE $where")->execute($inputParameters);
}

function createParameterNames($string_with_names) {
    $exploded_names = explode(', ', $string_with_names);
    $new_names = [];
    for ($i = 0, $total = count($exploded_names); $i < $total; $i++) {
        $new_names[$i] = ':' . $exploded_names[$i];
    }
    $string_with_new_names['exploded'] = $new_names;
    $string_with_new_names['imploded'] = implode(', ', $new_names);
    return $string_with_new_names;
}

function appendPrefix($prefix, $needle, $explode = null, $implode_when_finish = true) {
    $values = $explode != null ? explode($explode, $needle) : $needle;
    $new_values = [];
    foreach ($values as $key => $value) {
        $new_values[$key] = $prefix . $value;
    }
    $result = $new_values;
    if ($implode_when_finish) {
    	$result['imploded'] = implode(', ', $new_values);
    }
    return $result;
}

function createUpdateDBString($string, $bind_names, $concat = ', ') {
    $new_string = explode(', ', $string);
    for ($i = 0, $total = count($new_string); $i < $total; $i++) {
        $new_string[$i] = $new_string[$i] . ' = ' . $bind_names[$i];
    }
    return implode($concat, $new_string);
}

function createArrayParameters($names, $values) {
    $array = [];
    for ($i = 0, $total = count($values); $i < $total; $i++) {
        $array[$names[$i]] = $values[$i];
    }
    return $array;
}

function createFederatedTable($con, $database, $table, $columns) {
    $new_table = 'federated_' . $table;
    $shd = $con->prepare('DROP TABLE IF EXISTS ' . $new_table . '; CREATE TABLE ' . $new_table . ' (' . $columns . ') ENGINE=FEDERATED DEFAULT CHARSET=latin1 CONNECTION="mysql://' . DB_USER . ':' . DB_PASSWORD . '@localhost/' . $database . '/' . $table . '"');
    $shd->execute();
    return $new_table;
}

function sendEmail($from, $to, $subject, $message) {
    $headers = "Content-Type: text/html; charset=UTF-8\n";
    $headers .= "From: TRIAL<$from>\n";
    $headers .= "X-Sender: <$from>\n";
    $headers .= "X-Mailer: PHP v".phpversion()."\n";
    $headers .= "X-IP: ".  filter_input(INPUT_SERVER, 'REMOTE_ADDR')."\n";
    $headers .= "Return-Path: <$from>\n";
    $headers .= "MIME-Version: 1.0\n";
        
    return mail($to, $subject, $message, $headers);
}

function constructActivationMessage($to) {
    $check = selectDB($this->con, TABLE_USERS, 'id, email', 'WHERE email = :to', array(':to' => $to));
    $message = "Utilize o link a seguir para ativar sua conta TRIAL e aproveitar todos os seus benefícios.<br />";
    $message .= "<strong>Link: </strong>http://www.trialent.com/active.php?i=".$check['id'];
    return $message;
}

function getIp() {
    $client = @$_SERVER['HTTP_CLIENT_IP'];
    $forward = @$_SERVER['HTTP_X_FORWARDED_FOR'];
    $remote = $_SERVER['REMOTE_ADDR'];
    if (filter_var($client, FILTER_VALIDATE_IP)) {
        $ip = $client;
    } else if (filter_var($forward, FILTER_VALIDATE_IP)) {
        $ip = $forward;
    } else {
        $ip = $remote;
    }
    return $ip;
}

function urlExist($type_url, $url) {
    clearstatcache();
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_NOBODY, true);
    curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    if ($code == 200) {
        $img = $url;
    } else {
        switch ($type_url) {
            case IMAGE_PROFILE:
                $img = '/no-redo/img/TRIAL/logo/icon/social/min/T_icon_social_invert.png';
                break;
        }
    }
    return $img;
}

function urlExists($url) {
    clearstatcache();
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_NOBODY, true);
    curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    return $code == 200;
}

function getBrowserInfo() {
    $ch = curl_init();
    $url = 'https://helloacm.com/api/parse-user-agent';
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $agent = json_decode(curl_exec($ch));
    curl_close($ch);
    return $agent;
}

function request($url, $data = null) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    if ($data != null) {
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    }
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);
    return $response;
}

function pathExists($dir, $create = true) {
    if (file_exists($dir)) {
        return true;
    } else {
        if ($create) {
            mkdir($dir, 0777, true);
            return true;
        }
        return false;
    }
}

function uploadBase64Image($image, $dir, $filename) {
    $decodedImage = base64_decode($image);
    if (!file_exists($dir)) {
        mkdir($dir, 0777, true);
    }
    $file = fopen($dir.'/'.$filename, 'wb');
    fwrite($file, $decodedImage);
    fclose($file);
}