<?php
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class TRIALAccount {
    
    private $con;
    
    public function __construct() {
        $instanceClass = new ConnectDB(DB_DATABASE, DB_USER, DB_PASSWORD, DB_PREFIX.DATABASE_USERS);
        $this->con = $instanceClass->connect();
    }
    
    public function createTRIALAccount($name, $last_name, $birthday, $sex, $zip, $email, $pass) {
        $check = selectDB($this->con, TABLE_USERS, 'email', 'WHERE email = :email', [':email' => $email]);
        if ($check != null) {
            $result['message'] = MESSAGE_EXIST;
        } else {
            $result = insertDB($this->con, TABLE_USERS, 'name, last_name, birthday, sex, email, city, state, zip, password, how, permission, activated, ip, date_register, hour_register', [ucwords($name), ucwords($last_name), date_format(new DateTime($birthday), 'Y-m-d'), strtoupper($sex), $email, null, null, $zip, password_hash($pass, PASSWORD_DEFAULT), '', 'USER', 0, getIP(), date('Y-m-d'), date('H:i:s')]);
            $result['message'] = MESSAGE_SAVED_WITH_SUCCESS;
        }
        return $result;
    }
    
    public function createInstitutionalTRIALAccount($cnpj, $name, $infos, $email, $password) {
        $check = selectDB($this->con, TABLE_INSTITUTIONS, 'email, cnpj', 'WHERE cnpj = :cnpj AND email = :email', [':cnpj' => $cnpj, ':email' => $email]);
        if ($check != null) { 
            $result['message'] = MESSAGE_EXIST;
        } else {
            $result = insertDB($this->con, TABLE_INSTITUTIONS, 'cnpj, name, infos, email, password, activated, register_date, register_time', [$cnpj, $name, $infos, $email, password_hash($password, PASSWORD_DEFAULT), 0, date('Y-m-d'), date('H:i:s')]);
            $result['message'] = MESSAGE_SAVED_WITH_SUCCESS;
        }
        return $result;
    }
    
    public function createGovernmentalTRIALAccount($cnpj, $name, $infos, $email, $password) {
        $check = selectDB($this->con, TABLE_GOVERNMENTALS, 'email', 'WHERE email = :email', [':email' => $email]);
        if ($check != null) { 
            $result['message'] = MESSAGE_EXIST;
        } else {
            $result = insertDB($this->con, TABLE_GOVERNMENTALS, 'name, email, password, activated, register_date_time', [$name, $email, password_hash($password, PASSWORD_DEFAULT), 0, date('Y-m-d H:i:s')]);
            $result['message'] = MESSAGE_SAVED_WITH_SUCCESS;
        }
        return $result;
    }
    
    public function authenticateUserById($id, $password) {
        $account = selectDB($this->con, TABLE_USERS, 'name, last_name, email, password, activated, permission', 'WHERE id = :id', [':id' => $id])[0];
        if ($account != null) {
            if (password_verify($password, $account['password']) ? true : $password === $account['password']) {
	        $result = ['message' => $this->accountIsActivated($account['activated']) ? MESSAGE_EXIST : MESSAGE_NOT_ACTIVATED, 'id' => $account['id'], 'name' => $account['name'], 'last_name' => $account['last_name'], 'permission' => $account['permission']];
	        $this->concludeAuthenticationWeb($result['message'], $account, TRIAL_ACCOUNT_TYPE_USER);
            } else {
            	$result['message'] = MESSAGE_ERROR_PASSWORD_INCORRECT;
            }
        } else {
            $result['message'] = MESSAGE_NOT_EXIST;
        }
	return $result;
    }
    
    private function userAuth($email, $password, $permanent = 0) {
        $account = selectDB($this->con, TABLE_USERS, 'id, name, last_name, email, password, activated, permission', 'WHERE email = :email', [':email' => $email]);
        if ($account != null) {
            $account = $account[0];
            if (password_verify($password, $account['password']) ? true : $password === $account['password']) {
	        $result = ['message' => $this->accountIsActivated($account['activated']) ? MESSAGE_EXIST : MESSAGE_NOT_ACTIVATED, 'id' => $account['id'], 'name' => $account['name'], 'last_name' => $account['last_name'], 'permission' => $account['permission']];
	        $this->concludeAuthenticationWeb($result['message'], $account, TRIAL_ACCOUNT_TYPE_USER, $permanent);
            } else {
            	$result['message'] = MESSAGE_ERROR_PASSWORD_INCORRECT;
            }
        } else {
            $result['message'] = MESSAGE_NOT_EXIST;
        }
        return $result;
    }
    
    private function institutionAuth($email, $password, $permanent = 0) {
        $account = selectDB($this->con, TABLE_INSTITUTIONS, 'id, name, email, password, activated', 'WHERE email = :email', [':email' => $email]);
        if ($account != null) {
            $account = $account[0];
            if (password_verify($password, $account['password']) ? true : $password === $account['password']) {
	        $result = ['message' => $this->accountIsActivated($account['activated']) ? MESSAGE_EXIST : MESSAGE_NOT_ACTIVATED, 'id' => $account['id'], 'name' => $account['name'], 'permission' => isset($account['permission']) ? $account['permission'] : ''];
	        $this->concludeAuthenticationWeb($result['message'], $account, TRIAL_ACCOUNT_TYPE_INSTITUTION_MEMBER, $permanent);
            } else {
            	$result['message'] = MESSAGE_ERROR_PASSWORD_INCORRECT;
            }
        } else {
            $result['message'] = MESSAGE_NOT_EXIST;
        }
        return $result;
    }
    
    private function institutionMemberAuth($email, $password, $permanent = 0) {
        $account = selectDB($this->con, 'users AS u', 'u.id AS member_id, u.name AS member_name, u.password, u.permission AS member_permission, IF(COUNT(i.id) > 0, true, false) AS have_institution, COUNT(i.id) AS total_institutions, GROUP_CONCAT(i.id SEPARATOR \', \') AS id, GROUP_CONCAT(i.name SEPARATOR \', \') AS name, GROUP_CONCAT(i.email SEPARATOR \', \') AS email, GROUP_CONCAT(i.activated SEPARATOR \', \') AS activated', 'LEFT JOIN institutions_members AS im ON im.user = u.id LEFT JOIN institutions AS i ON i.id = im.institution WHERE u.email = :email', [':email' => $email]);
        if ($account != null) {
            $account = $account[0];
            if (password_verify($password, $account['password']) ? true : $password === $account['password']) {
	        $result = ['message' => $account['have_institution'] ? MESSAGE_EXIST : MESSAGE_MEMBER_WITHOUT_INSTITUTION, 'institutions' => ['have_institutions' => $account['have_institution']], 'member' => ['id' => $account['member_id'], 'name' => $account['member_name'], 'permission' => $account['member_permission']]];
	        $this->concludeAuthenticationWeb($result['message'], $account, TRIAL_ACCOUNT_TYPE_INSTITUTION_MEMBER, $permanent);
                if ($account['have_institution']) {
                    $result['institutions']['total_institutions'] = $account['total_institutions'];
                    $account = ['id' => explode(', ', $account['id']), 'name' => explode(', ', $account['name']), 'email' => explode(', ', $account['email'])];
                    foreach ($account['id'] as $key => $id) {
                        $result['institutions'][$key] = ['id' => $id, 'name' => $account['name'][$key], 'email' => $account['email'][$key]];
                    }
                }
            } else {
            	$result['message'] = MESSAGE_ERROR_PASSWORD_INCORRECT;
            }
        } else {
            $result['message'] = MESSAGE_NOT_EXIST;
        }
        return $result;
    }
    
    private function governmentAuth($email, $password, $permanent = 0) {
        $account = selectDB($this->con, TABLE_GOVERNMENTS, 'id, name, email, password, activated', 'WHERE email = :email', [':email' => $email]);
        if ($account != null) {
            $account = $account[0];
            if (password_verify($password, $account['password']) ? true : $password === $account['password']) {
	        $result = ['message' => $this->accountIsActivated($account['activated']) ? MESSAGE_EXIST : MESSAGE_NOT_ACTIVATED, 'id' => $account['id'], 'name' => $account['name'], 'permission' => isset($account['permission']) ? $account['permission'] : ''];
	        $this->concludeAuthenticationWeb($result['message'], $account, TRIAL_ACCOUNT_TYPE_GOVERNMENT, $permanent);
            } else {
            	$result['message'] = MESSAGE_ERROR_PASSWORD_INCORRECT;
            }
        } else {
            $result['message'] = MESSAGE_NOT_EXIST;
        }
        return $result;
    }
    
    public function authenticateUser($email, $password, $type_account = TRIAL_ACCOUNT_TYPE_USER, $permanent = 0) {
        switch ($type_account) {
            case TRIAL_ACCOUNT_TYPE_USER:
                $result = $this->userAuth($email, $password, $permanent);
                break;
            case TRIAL_ACCOUNT_TYPE_INSTITUTION:
                $result = $this->institutionAuth($email, $password, $permanent);
                break;
            case TRIAL_ACCOUNT_TYPE_INSTITUTION_MEMBER:
                $result = $this->institutionMemberAuth($email, $password, $permanent);
                break;
            case TRIAL_ACCOUNT_TYPE_GOVERNMENT:
                $result = $this->governmentAuth($email, $password, $permanent);
                break;
        }
	return $result;
    }
    
    private function concludeAuthenticationWeb($message, $account, $type, $permanent) {
        if ($message === MESSAGE_EXIST) {
            switch ($type) {
                case TRIAL_ACCOUNT_TYPE_USER:
                    $this->makePermanentLogin([COOKIE_ID_TRIAL, COOKIE_NAME, COOKIE_EMAIL, COOKIE_PERMISSION, COOKIE_TYPE], [$account['id'], $account['name'], $account['email'], $account['permission'], $type], $permanent == 1 ? DURATION_INDEFINED : 0);
                    break;
                case TRIAL_ACCOUNT_TYPE_INSTITUTION:
                    $this->makePermanentLogin([COOKIE_TI_ID_TRIAL, COOKIE_TI_NAME, COOKIE_TI_EMAIL, COOKIE_TYPE], [$account['id'], $account['name'], $account['email'], $type], $permanent == 1 ? DURATION_INDEFINED : 0);
                    break;
                case TRIAL_ACCOUNT_TYPE_GOVERNMENT:
                    $this->makePermanentLogin([COOKIE_TG_ID_TRIAL, COOKIE_TG_NAME, COOKIE_TG_EMAIL, COOKIE_TYPE], [$account['id'], $account['name'], $account['email'], $type], $permanent == 1 ? DURATION_INDEFINED : 0);
                    break;
                case TRIAL_ACCOUNT_TYPE_INSTITUTION_MEMBER:
                    $this->makePermanentLogin([COOKIE_ID_TRIAL, COOKIE_NAME, COOKIE_PERMISSION, COOKIE_TI_ID_TRIAL, COOKIE_TI_NAME, COOKIE_TI_EMAIL, COOKIE_TYPE], [$account['member_id'], $account['member_name'], $account['member_permission'], $account['id'], $account['name'], $account['email'], $type], $permanent == 1 ? DURATION_INDEFINED : 0);
                    break;
            }
        }
    }
    
    public function updateIP() {
        $check = selectDB($this->con, TABLE_USERS, 'id', 'WHERE email = :email', [':email' => filter_input(INPUT_COOKIE, 'login')]);
        updateDB($this->con, TABLE_USERS, 'ip = :ip', 'id = :id', [':ip' => getIP(), ':id' => $check[0]['id']]);
    }
    
    private function makePermanentLogin($name_cookies, $value_cookies, $duration) {
        for ($i = 0, $total = count($name_cookies); $i < $total; $i++) {
            $domain = $_SERVER['HTTP_HOST'] !== 'localhost' ? '.trialent.com' : 'localhost';
            setcookie($name_cookies[$i], $value_cookies[$i], $duration != DURATION_INDEFINED ? $duration : strtotime('+30 days'), '/', $domain);
        }
    }
    
    public function logout() {
        $result = [];
        $i = 0;
        $host = $_SERVER['HTTP_HOST'];
        foreach ($_COOKIE as $key => $value) {
            if (strpos($key, 'TRL_') !== false) {
                if ($host !== 'localhost') {
                    if (strpos($key, 'SR') !== false) {
                        $domain = 'serginho.trialent.com';
                    } else if (strpos($key, 'CL') !== false) {
                        $domain = 'clicker.trialent.com';
                    } else {
                        $domain = '.trialent.com';
                    }
                } else {
                    $domain = $host;
                }
                setcookie($key, null, time() - 3600, '/', $domain);
                $result[$i++] = ['Key' => $key, 'Domain' => $domain, 'Status' => 'Deleted'];
            }
            $result['message'] = MESSAGE_SAVED_WITH_SUCCESS;
        }
        return $result;
    }
    
    public function changeProfileImage($id, $image) {
        move_uploaded_file($image['tmp_name'], 'images/user/profile/' . $id . '/' . $id . '.jpg');
    }
    
    public function getProfileImage($id) {
        return urlExist(IMAGE_PROFILE, 'http://www.trialent.com/images/user/profile/' . $id . '/' . $id . '.jpg');
    }
    
    public function getProfile($id) {
        $get = selectDB($this->con, TABLE_USERS, 'id, name, last_name, birthday, city, state, zip, email, permission', 'WHERE id = :id', [':id' => $id]);
        $get['message'] = $get != null ? MESSAGE_EXIST : MESSAGE_NOT_EXIST;
        return $get;
    }
    
    public function getProfiles($ids) {
        $get = selectDB($this->con, TABLE_USERS, 'id, name, last_name, birthday, city, state, zip, email, permission', 'WHERE id IN (' . $ids . ')', null);
        $get['message'] = $get != null ? MESSAGE_EXIST : MESSAGE_NOT_EXIST;
        return $get;
    }
    
    public function getProfilesByPermission($permission) {
        $get = selectDB($this->con, TABLE_USERS, 'id, name, last_name, email, permission', 'WHERE permission = :permission ORDER BY name ASC', [':permission' => $permission]);
        $get['message'] = $get != null ? MESSAGE_EXIST : MESSAGE_NOT_EXIST;
        return $get;
    }
    
    public function changeProfileData($id, $name, $last_name, $email) {
        $update = updateDB($this->con, TABLE_USERS, 'name = :name, last_name = :last_name, email = :email', 'WHERE id = :id', [':name' => $name, ':last_name' => $last_name, ':email' => $email, ':id' => $id]);
        return ['message' => $update ? MESSAGE_SAVED_WITH_SUCCESS : MESSAGE_ERROR, 'echo_message' => 'Informações de perfil atualizadas!'];
    }
    
    public function changeLocalizationData($id, $city, $state, $zip) {
        $update = updateDB($this->con, TABLE_USERS, 'city = :city, state = :state, zip = :zip', 'WHERE id = :id', [':city' => $city, ':state' => $state, ':zip' => $zip, ':id' => $id]);
        return ['message' => $update ? MESSAGE_SAVED_WITH_SUCCESS : MESSAGE_ERROR, 'echo_message' => 'Informações de localização atualizadas!'];
    }
    
    public function changePassword($id, $old_password, $new_password) {
        $update = updateDB($this->con, TABLE_USERS, 'password = :password', 'WHERE id = :id AND password = :old_password', [':password' => $new_password, ':id' => $id, ':old_password' => $old_password]);
        return ['message' => $update ? MESSAGE_SAVED_WITH_SUCCESS : MESSAGE_ERROR, 'echo_message' => 'Senha alterada!'];
    }
    
    public function recoverChangePassword($user, $new_password) {
        $update = updateDB($this->con, TABLE_USERS, 'password', 'id', [password_hash($new_password, PASSWORD_DEFAULT), $user]);
        return ['message' => $update ? MESSAGE_SAVED_WITH_SUCCESS : MESSAGE_ERROR];
    }
    
    public function activateAccount($id_account) {
        $update = updateDB($this->con, DATABASE_USERS, 'activated = :activated', 'id = :id', [':activated' => 'yes', ':id' => $id_account]);
        return ['message' => $update ? MESSAGE_SAVED_WITH_SUCCESS : MESSAGE_ERROR, 'echo_message' => 'Pronto. Conta ativada com sucesso!'];
    }
    
    public function accountIsActivated($column_activated) {
        if (!$column_activated || $column_activated === 'no') { 
            return true;
        }
        return false;
    }
    
    public function getAllAccounts($user) {
        $accounts = selectDB($this->con, DB_PREFIX . DATABASE_USERS . '.' . TABLE_USERS . ' AS trial', 'clicker.id AS clicker_id, clicker.type AS clicker_type, clicker.register_date AS clicker_register_date, clicker.register_time AS clicker_register_time', 'LEFT JOIN ' . DB_PREFIX . DATABASE_CLICKER . '.' . TABLE_USERS . ' AS clicker ON clicker.user = trial.id WHERE trial.id = :user', [':user' => $user])[0];
        $accounts['message'] = $accounts != null ? MESSAGE_EXIST : MESSAGE_NOT_EXIST;
        return $accounts;
    }

    public function getHowKnowRegisters() {
        $get = selectDB($this->con, 'how_know', 'id, how', null, null);
        $get['message'] = $get != null ?  MESSAGE_EXIST : MESSAGE_NOT_EXIST;
        return $get;
    }
    
    public function checkEmail($email) {
        $exist = selectDB($this->con, 'users', 'id', 'WHERE email = :email', [':email' => $email]);
        if ($exist != null) {
            $response['id'] = $exist[0]['id'];
        }
        $response['message'] = $exist != null ?  MESSAGE_EXIST : MESSAGE_NOT_EXIST;
        return $response;
    }
    
    public function createVerificationCode($user) {
        $raw_code = uniqid(rand(), true);
        $code = md5($raw_code);
        return [insertDB($this->con, 'verification_codes', 'user, code, register_date, register_time', [$user, $code, date('Y-m-d'), date('H:i:s')]), 'message' => MESSAGE_SAVED_WITH_SUCCESS, 'code' => $raw_code];
    }
    
    public function checkVerificationCode($id, $code) {
        $check = selectDB($this->con, 'verification_codes', 'user', 'WHERE id = :id AND code = :code', [':id' => $id, ':code' => md5($code)]);
        if ($check != null) {
            updateDB($this->con, 'verification_codes', 'verificated', 'id', [true, $id]);
        }
        $result = $check != null ? $check[0] : $check;
        $result['message'] = $check != null ? MESSAGE_EXIST : MESSAGE_NOT_EXIST;
        return $result;
    }
    
}

class DecodeInstitutionInfos {
    
    public function __construct($infos) {
    }
    
}