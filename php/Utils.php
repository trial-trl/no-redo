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
    return $shd;
}
    
function insertDB($con, $table, $columns, $values) {
    $names = createParameterNames($columns);
    $input_parameters = createArrayParameters($names['exploded'], $values);
    $con->prepare("INSERT INTO $table ($columns) VALUES (".$names['imploded'].")")->execute($input_parameters);
    $result['id'] = $con->lastInsertId();
    return $result;
}
    
function updateDB($con, $table, $values, $where, $inputParameters) {
    return $con->prepare("UPDATE $table SET $values WHERE $where")->execute($inputParameters);
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

function createArrayParameters($names, $values) {
    $array = [];
    for ($i = 0, $total = count($values); $i < $total; $i++) {
        $array[$names[$i]] = $values[$i];
    }
    return $array;
}
    
function compare($value, $compare, $type_comparison) {
    if ($type_comparison === OPERATION_IDENTIC) {
        return $value === $compare;
    } else if ($type_comparison === OPERATION_MORE_OR_EQUAL) {
        return $value >= $compare;
    } else if ($type_comparison === OPERATION_LESS_OR_EQUAL) {
        return $value <= $compare;
    } else if ($type_comparison === OPERATION_DIFERENT) {
        return $value !== $compare;
    } else if ($type_comparison === OPERATION_MORE) {
        return $value > $compare;
    } else if ($type_comparison === OPERATION_LESS) {
        return $value < $compare;
    } else {
        return null;
    }
}

function createFederatedTable($con, $database, $table, $columns) {
    $new_table = "federated_$table";
    $shd = $con->prepare('CREATE TABLE federated_'.$table.' ($columns) ENGINE=FEDERATED; DEFAULT CHARSET=latin1 CONNECTION="mysql://".DB_USER.":".DB_PASSWORD."@localhost/$database/$table"');
    $shd->execute();
    return $new_table;
}

function sendEmail($from, $to, $subject, $message, $returnMessage) {
    $headers = "Content-Type: text/html; charset=UTF-8\n";
    $headers .= "From: TRIAL<$from>\n";
    $headers .= "X-Sender: <$from>\n";
    $headers .= "X-Mailer: PHP v".phpversion()."\n";
    $headers .= "X-IP: ".  filter_input(INPUT_SERVER, 'REMOTE_ADDR')."\n";
    $headers .= "Return-Path: <$from>\n";
    $headers .= "MIME-Version: 1.0\n";
        
    mail($to, $subject, $message, $headers);
        
    echo $returnMessage;
}

function constructActivationMessage($to) {
    $check = selectDB($this->con, TABLE_USERS, 'id, email', 'WHERE email = :to', array(':to' => $to));
    $message = "Utilize o link a seguir para ativar sua conta TRIAL e aproveitar todos os seus benefícios.<br />";
    $message .= "<strong>Link: </strong>http://www.trialent.com/active.php?i=".$check['id'];
    return $message;
}

function getIp() {
    return getenv('REMOTE_ADDR');
}

function imageExist($type_image, $id) {
    clearstatcache();
    $url = 'http://www.trialent.com/images/user/profile/'.$id.'/'.$id.'.jpg';
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_NOBODY, true);
    curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    if ($code == 200) {
        $img = $url;
    } else {
        switch ($type_image) {
            case IMAGE_PROFILE:
                $img = 'http://www.trialent.com/images/arrow.png';
                break;
        }
    }
    return $img;
}