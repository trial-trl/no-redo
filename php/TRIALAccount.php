<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class TRIALAccount {
    
    private $con;
    
    const MOBILE_CONTEXT = 'mobile';
    const WEB_CONTEXT = 'web';
    
    public function __construct($context) {
        $instanceClass = new ConnectDB(DB_DATABASE, DB_USER, DB_PASSWORD, DB_PREFIX.DATABASE_USERS);
        $this->con = $instanceClass->connect();
    }
    
    public function createTRIALAccount($name, $last_name, $birthday, $sex, $zip, $email, $pass) {
        $check = selectDB($this->con, TABLE_USERS, 'email', 'WHERE email = :email', array(':email' => $email));
        if ($check != null) { 
            $result['message'] = MESSAGE_EXIST;
        } else {
            $result = insertDB($this->con, TABLE_USERS, 'name, last_name, birthday, sex, email, city, state, zip, password, how, permission, activated, ip, date_register, hour_register', array(ucwords($name), ucwords($last_name), date_format(new DateTime($birthday), 'Y-m-d'), strtoupper($sex), $email, null, null, $zip, password_hash($pass, PASSWORD_DEFAULT), '', 'USER', 'no', getIP(), date('Y-m-d'), date('H:i:s')));
            $result['message'] = MESSAGE_SAVED_WITH_SUCCESS;
        }
        return $result;
    }
    
    public function createInstitutionalTRIALAccount($type, $name, $cnpj, $headquarters, $phone, $email, $password) {
        $check = selectDB($this->con, TABLE_INSTITUTIONS, 'email', 'WHERE email = :email', array(':email' => $email));
        if ($check != null) { 
            $result['message'] = MESSAGE_EXIST;
        } else {
            $result = insertDB($this->con, TABLE_INSTITUTIONS, 'type, name, cnpj, headquarters, phone, email, password, activated, register_date, register_time', array($type, $name, $cnpj, $headquarters, $phone, $email, password_hash($password, PASSWORD_DEFAULT), 'no', date('Y-m-d'), date('H:i:s')));
            $result['message'] = MESSAGE_SAVED_WITH_SUCCESS;
        }
        return $result;
    }
    
    public function authenticateUserById($id, $password) {
        $account = selectDB($this->con, TABLE_USERS, 'name, last_name, email, password, activated, permission', 'WHERE id = :id', array(':id' => $id))[0];
        if ($account != null) {
            $equal = password_verify($password, $account['password']) ? true : $password === $account['password'];
            if ($equal) {
            	$result['message'] = $this->accountIsActivated($account['activated']) ? MESSAGE_EXIST : MESSAGE_NOT_ACTIVATED;
	        $this->concludeAuthenticationWeb($equal, $result['message'], $account, TRIAL_ACCOUNT_TYPE_USER);
	        $result['id'] = $account['id'];
	        $result['name'] = $account['name'];
	        $result['last_name'] = $account['last_name'];
	        $result['permission'] = $account['permission'];
            } else {
            	$result['message'] = MESSAGE_ERROR_PASSWORD_INCORRECT;
            }
        } else {
            $result['message'] = MESSAGE_NOT_EXIST;
        }
	return $result;
    }
    
    public function authenticateUser($email, $password, $type_account = TRIAL_ACCOUNT_TYPE_USER) {
        if ($type_account === TRIAL_ACCOUNT_TYPE_USER) {
            $account = selectDB($this->con, TABLE_USERS, 'id, name, last_name, email, password, activated, permission', 'WHERE email = :email', array(':email' => $email))[0];
        } else {
            $account = selectDB($this->con, TABLE_INSTITUTIONS, 'id, type, name, email, password, activated', 'WHERE email = :email', array(':email' => $email))[0];
        }
        if ($account != null) {
            $equal = password_verify($password, $account['password']) ? true : $password === $account['password'];
            if ($equal) {
            	$result['message'] = $this->accountIsActivated($account['activated']) ? MESSAGE_EXIST : MESSAGE_NOT_ACTIVATED;
	        $this->concludeAuthenticationWeb($equal, $message, $account, $type_account);
	        $result['id'] = $account['id'];
	        $result['name'] = $account['name'];
	        $result['last_name'] = $account['last_name'];
	        $result['permission'] = $account['permission'];
            } else {
            	$result['message'] = MESSAGE_ERROR_PASSWORD_INCORRECT;
            }
        } else {
            $result['message'] = MESSAGE_NOT_EXIST;
        }
	return $result;
    }
    
    private function concludeAuthenticationWeb($is_password_equals, $message, $account, $type) {
        if ($is_password_equals) {
            if ($message != MESSAGE_NOT_ACTIVATED) {
                if ($type === TRIAL_ACCOUNT_TYPE_USER) {
                    $this->makePermanentLogin(COOKIE_ID_TRIAL . ',' . COOKIE_NAME . ',' . COOKIE_EMAIL . ',' . COOKIE_PERMISSION . ',' . COOKIE_TYPE, array($account['id'], $account['name'], $account['email'], $account['permission'], $type), DURATION_INDEFINED);
                } else {
                    $this->makePermanentLogin(COOKIE_TI_ID_TRIAL . ',' . COOKIE_TI_NAME . ',' . COOKIE_TI_EMAIL . ',' . COOKIE_TYPE, array($account['id'], $account['name'], $account['email'], $type), DURATION_INDEFINED);
                }
            }
        }
    }
    
    public function updateIP() {
        $check = selectDB($this->con, TABLE_USERS, 'id', 'WHERE email = :email', array(':email' => filter_input(INPUT_COOKIE, 'login')));
        updateDB($this->con, TABLE_USERS, 'ip = :ip', 'id = :id', array(':ip' => getIP(), ':id' => $check[0]['id']));
    }
    
    private function makePermanentLogin($name_cookies, $value_cookies, $duration) {
        $exploded_names = explode(',', $name_cookies);
        for ($i = 0, $total = count($exploded_names); $i < $total; $i++) {
            setcookie($exploded_names[$i], $value_cookies[$i], $duration != DURATION_INDEFINED ? $duration : time() + (60 * 60 * 24 * 365), '/', '.trialent.com');
        }
    }
    
    public function logout() {
        $time = time() - 3600;
        setcookie(COOKIE_ID_TRIAL, null, $time, '/', '.trialent.com');
        setcookie(COOKIE_NAME, null, $time, '/', '.trialent.com');
        setcookie(COOKIE_EMAIL, null, $time, '/', '.trialent.com');
        setcookie(COOKIE_TI_ID_TRIAL, null, $time, '/', '.trialent.com');
        setcookie(COOKIE_TI_NAME, null, $time, '/', '.trialent.com');
        setcookie(COOKIE_TI_EMAIL, null, $time, '/', '.trialent.com');
        setcookie(COOKIE_PERMISSION, null, $time, '/', '.trialent.com');
        setcookie(COOKIE_TYPE, null, $time, '/', '.trialent.com');
    }
    
    public function signOut() {
        $time = time() - 3600;
        setcookie(COOKIE_ID_TRIAL, null, $time, '/', '.trialent.com');
        setcookie(COOKIE_NAME, null, $time, '/', '.trialent.com');
        setcookie(COOKIE_EMAIL, null, $time, '/', '.trialent.com');
        setcookie(COOKIE_TI_ID_TRIAL, null, $time, '/', '.trialent.com');
        setcookie(COOKIE_TI_NAME, null, $time, '/', '.trialent.com');
        setcookie(COOKIE_TI_EMAIL, null, $time, '/', '.trialent.com');
        setcookie(COOKIE_PERMISSION, null, $time, '/', '.trialent.com');
        setcookie(COOKIE_TYPE, null, $time, '/', '.trialent.com');
    }
    
    public function changeProfileImage($id, $image) {
        move_uploaded_file($image['tmp_name'], 'images/user/profile/' . $id . '/' . $id . '.jpg');
    }
    
    public function getProfileImage($id) {
        return urlExist(IMAGE_PROFILE, 'http://www.trialent.com/images/user/profile/' . $id . '/' . $id . '.jpg');
    }
    
    public function getProfile($id) {
        $get = selectDB($this->con, TABLE_USERS, 'id, name, last_name, birthday, city, state, zip, email, permission', 'WHERE id = :id', array(':id' => $id));
        $get['message'] = $get != null ? MESSAGE_EXIST : MESSAGE_NOT_EXIST;
        return $get;
    }
    
    public function getProfiles($ids) {
        $get = selectDB($this->con, TABLE_USERS, 'id, name, last_name, birthday, city, state, zip, email, permission', 'WHERE id IN (' . $ids . ')', null);
        $get['message'] = $get != null ? MESSAGE_EXIST : MESSAGE_NOT_EXIST;
        return $get;
    }
    
    public function getProfilesByPermission($permission) {
        $get = selectDB($this->con, TABLE_USERS, 'id, name, last_name, email, permission', 'WHERE permission = :permission ORDER BY name ASC', array(':permission' => $permission));
        $get['message'] = $get != null ? MESSAGE_EXIST : MESSAGE_NOT_EXIST;
        return $get;
    }
    
    public function changeProfileData($id, $name, $last_name, $email) {
        $update = updateDB($this->con, TABLE_USERS, 'name = :name, last_name = :last_name, email = :email', 'WHERE id = :id', array(':name' => $name, ':last_name' => $last_name, ':email' => $email, ':id' => $id));
        $result['message'] = MESSAGE_SAVED_WITH_SUCCESS;
        $result['echo_message'] = 'Informações de perfil atualizadas!';
        return $result;
    }
    
    public function changeLocalizationData($id, $city, $state, $zip) {
        $update = updateDB($this->con, TABLE_USERS, 'city = :city, state = :state, zip = :zip', 'WHERE id = :id', array(':city' => $city, ':state' => $state, ':zip' => $zip, ':id' => $id));
        $result['message'] = MESSAGE_SAVED_WITH_SUCCESS;
        $result['echo_message'] = 'Informações de localização atualizadas!';
        return $result;
    }
    
    public function changePassword($id, $old_password, $new_password) {
        $update = updateDB($this->con, TABLE_USERS, 'password = :password', 'WHERE id = :id AND password = :old_password', array(':password' => $new_password, ':id' => $id, ':old_password' => $old_password));
        $result['message'] = MESSAGE_SAVED_WITH_SUCCESS;
        $result['echo_message'] = 'Senha alterada!';
        return $result;
    }
    
    public function activateAccount($id_account) {
        updateDB($this->con, DATABASE_USERS, 'activated = :activated', 'id = :id', array(':activated' => 'yes', ':id' => $id_account));
        $result['message'] = MESSAGE_SAVED_WITH_SUCCESS;
        $result['echo_message'] = 'Pronto. Conta ativada com sucesso!';
        return $result;
    }
    
    public function accountIsActivated($column_activated) {
        if ($column_activated === 'no') { 
            return true;
        }
        return false;
    }
    
}