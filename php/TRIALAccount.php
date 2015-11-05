<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

include_once 'utils/Utils.php';

class TRIALAccount {
    
    private $con;
    
    const MOBILE_CONTEXT = 'mobile';
    const WEB_CONTEXT = 'web';
    
    private $CURRENT_CONTEXT;
    
    public function __construct($context, $database_name) {
        
        $this->CURRENT_CONTEXT = $context;
        
        $instanceClass = new ConnectDB(DB_DATABASE, DB_USER, DB_PASSWORD, DB_PREFIX.DATABASE_USERS);
        $this->con = $instanceClass->connect();
    }
    
    private function finalizeCode($result, $tag_master) {
    	if ($this->CURRENT_CONTEXT === TRIALAccount::MOBILE_CONTEXT) {
            $response[$tag_master] = $result;
            echo json_encode($response);
        } else {
            return json_encode($result);
        }
        $this->con = null;
    }
    
    public function createTRIALAccount($name, $last_name, $birthday, $sex, $zip, $email, $pass, $type = 'user') {
        $check = selectDB($this->con, TABLE_USERS, 'email', 'WHERE email = :email', array(':email' => $email));
        if ($check != null) { $result['message'] = MESSAGE_EXIST; }
        else {
            $checkColumns = $type == 'user' ? $this->checkColumns($name, $last_name, $birthday, $sex, $zip, $email, $pass) : null;
            error_log(1, 0);
            if ($checkColumns === null) {
            error_log(2, 0);
                $dateTime = DateTime::createFromFormat('d/m/Y', $birthday);
            error_log(3, 0);
                if ($type == 'user') {
            error_log(4, 0);
                    insertDB($this->con, TABLE_USERS, 'name, last_name, birthday, sex, email, city, state, zip, password, how, permission, activated, ip, date_register, hour_register', array($name, $last_name, $dateTime->format('Y-m-d'), $sex, $email, '', '', $zip, $pass, '', 'USER', 'yes', getIP(), date('Y-m-d'), date('H:i:s')));
                } else {
            error_log(5, 0);
                    insertDB($this->con, TABLE_INSTITUTIONS, 'type, name, email, password, activated, register_date, register_time', array($name, $last_name, $email, $pass, 'yes', date('Y-m-d'), date('H:i:s')));
                }
            error_log(6, 0);
                $result['id'] = $this->con->lastInsertId();
                $result['message'] = MESSAGE_SAVED_WITH_SUCCESS;
            } else {
                $result['message'] = MESSAGE_ERROR;
                $result['echo_message'] = $checkColumns;
            }
        }
        return $this->finalizeCode($result, 'user');
    }
    
    private function checkColumns($name, $last_name, $birthday, $sex, $zip, $email, $pass) {
        return empty($name) ? "Informe seu nome" : empty($last_name) ? "Informe seu sobrenome" : empty($birthday) ? "Informe sua data de nascimento" : empty($sex) ? "Informe seu sexo" : empty($email) ? "Informe seu email" : empty($pass) ? "Informe uma senha" : empty($zip) ? "Informe seu CEP" : null;
    }
    
    public function authenticateUser($email, $password, $account = 'user') {
            error_log(1, 0);
        if ($account === 'user') {
            error_log(2, 0);
            $result = selectDB($this->con, TABLE_USERS, 'id, name, last_name, email, password, activated, permission', 'WHERE email = :email', array(':email' => $email));
        } else {
            error_log(3, 0);
            $result = selectDB($this->con, TABLE_INSTITUTIONS, 'id, type, name, email, password, activated', 'WHERE email = :email', array(':email' => $email));
        }
        if ($result != null) {
            error_log(4, 0);
            $equal = $result[0]['password'] == $password ? true : false;
            error_log(5, 0);
            $message = $equal == false ? MESSAGE_EMAIL_OR_PASSWORD_INVALID : !$this->accountIsActivated($result[0]['activated']) ? MESSAGE_NOT_ACTIVATED : MESSAGE_EXIST;
            error_log(6, 0);
            if ($this->CURRENT_CONTEXT === TRIALAccount::WEB_CONTEXT) { 
            error_log(7, 0);$this->concludeAuthenticationWeb($equal, $message, $result[0], $account); }
            error_log(8, 0);
            $result['message'] = $message;
        } else { $result['message'] = MESSAGE_NOT_EXIST; }
            error_log(9, 0);
        return $this->finalizeCode($result, 'user');
        
    }
    
    private function concludeAuthenticationWeb($is_password_equals, $message, $account, $type) {
        if ($is_password_equals) {
            if ($message != MESSAGE_NOT_ACTIVATED) {
            error_log(10, 0);
                $this->makePermanentLogin('idTRIAL, name, login, permission, TRL_type', array($account['id'], $account['name'], $account['email'], $account['permission'], $type), DURATION_INDEFINED);
            error_log(11, 0);
                //$this->updateIP();
            } else {
                setcookie('emailActivate', $account['email']);
            }
        }
    }
    
    public function updateIP() {
        $check = selectDB($this->con, TABLE_USERS, 'id', 'WHERE email = :email', array(':email' => filter_input(INPUT_COOKIE, 'login')));
        updateDB($this->con, TABLE_USERS, 'ip = :ip', 'id = :id', array(':ip' => getIP(), ':id' => $check[0]['id']));
    }
    
    private function makePermanentLogin($name_cookies, $value_cookies, $duration) {
        $exploded_names = explode(', ', $name_cookies);
        for ($i = 0, $total = count($exploded_names); $i < $total; $i++) {
            error_log($exploded_names[$i] . " = " . $value_cookies[$i]);
            if ($duration != DURATION_INDEFINED) {
                setcookie($exploded_names[$i], $value_cookies[$i], $duration, '/', '.trialent.com');
            } else {
                setcookie($exploded_names[$i], $value_cookies[$i], time() + (60 * 60 * 24 * 365), '/', '.trialent.com');
            }
        }
    }
    
    public function signOut() {
        setcookie('id', null, time() - 3600, '/', '.trialent.com');
        setcookie('login', null, time() - 3600, '/', '.trialent.com');
        setcookie('type', null, time() - 3600, '/', '.trialent.com');
    }
    
    public function getProfile($id) {
        $get = selectDB($this->con, TABLE_USERS, 'id, name, last_name, birthday, city, state, zip, email, permission', 'WHERE id = :id', array(':id' => $id));
        if ($get != null) {
            $get['message'] = MESSAGE_EXIST;
        } else {
            $get['message'] = MESSAGE_NOT_EXIST;
        }
        if ($this->CURRENT_CONTEXT === TRIALAccount::MOBILE_CONTEXT) {
            $response['profile'] = $get;
            echo json_encode($response);
        } else {
            return json_encode($get);
        }
    }
    
    public function getProfilesByPermission($permission) {
        $get = selectDB($this->con, TABLE_USERS, 'id, name, last_name, email, permission', 'WHERE permission = :permission ORDER BY name ASC', array(':permission' => $permission));
        if ($get != null) {
            $get['message'] = MESSAGE_EXIST;
        } else {
            $get['message'] = MESSAGE_NOT_EXIST;
        }
        if ($this->CURRENT_CONTEXT === TRIALAccount::MOBILE_CONTEXT) {
            $response['profile'] = $get;
            echo json_encode($response);
        } else {
            return json_encode($get);
        }
    }
    
    public function changeProfileData($id, $name, $last_name, $email) {
        $update = updateDB($this->con, TABLE_USERS, 'name = :name, last_name = :last_name, email = :email', 'WHERE id = :id', array(':name' => $name, ':last_name' => $last_name, ':email' => $email, ':id' => $id));
        if ($this->CURRENT_CONTEXT === TRIALAccount::MOBILE_CONTEXT) {
            $result['message'] = MESSAGE_SAVED_WITH_SUCCESS;
            $response['profile'] = $result;
            echo json_encode($response);
        } else {
            echo "Informações de perfil atualizadas!";
        }
    }
    
    public function changeLocalizationData($id, $city, $state, $zip) {
        $update = updateDB($this->con, TABLE_USERS, 'city = :city, state = :state, zip = :zip', 'WHERE id = :id', array(':city' => $city, ':state' => $state, ':zip' => $zip, ':id' => $id));
        if ($this->CURRENT_CONTEXT === TRIALAccount::MOBILE_CONTEXT) {
            $result['message'] = MESSAGE_SAVED_WITH_SUCCESS;
            $response['profile'] = $result;
            echo json_encode($response);
        } else {
            echo "Informações de localização atualizadas";
        }
    }
    
    public function activateAccount($id_account) {
        updateDB($this->con, DATABASE_USERS, 'activated = :activated', 'id = :id', array(':activated' => 'yes', ':id' => $id_account));
        echo "Pronto. Conta ativada com sucesso!";
    }
    
    public function accountIsActivated($column_activated) {
        if ($column_activated === 'no') { 
            return false;
        }
        return true;
    }
    
}