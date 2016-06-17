<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class NoRedoAccount {
    
    private $con;
    
    public function __construct() {
        $instanceClass = new ConnectDB(DB_DATABASE, DB_USER, DB_PASSWORD, DB_PREFIX . DATABASE_NO_REDO);
        $this->con = $instanceClass->connect();
    }
    
    public function authenticateUser($id_trial) {
        $profile = $this->getProfile($id_trial);
        if ($profile['message'] === MESSAGE_EXIST) {
            setcookie(COOKIE_NO_REDO_MEMBER_COMPANIES, $profile['companies'], time() + (60 * 60 * 24 * 365), '/', 'localhost');
            setcookie(COOKIE_NO_REDO_COMPANY, $profile['current_institution'], time() + (60 * 60 * 24 * 365), '/', 'localhost');
            setcookie(COOKIE_NO_REDO_COMPANY . '_id', $profile['institution_id'], time() + (60 * 60 * 24 * 365), '/', 'localhost');
            setcookie(COOKIE_NO_REDO_ID_MEMBER, $profile['id'], time() + (60 * 60 * 24 * 365), '/', 'localhost');
        }
        return $profile;
    }
    
    public function createAccount($user, $institution, $type) {
        $result = insertDB($this->con, 'users', 'user, institution, type, register_date, register_time, ip', array($user, $institution, $type, date('Y-m-d'), date('H:i:s'), getenv('REMOTE_ADDR')));
        $result['message'] = MESSAGE_SAVED_WITH_SUCCESS;
        return $result;
    }
    
    public function getProfile($user) {
        $get = selectDB($this->con, 'users AS u', 'u.id, u.current_institution, cui.institution AS institution_id, GROUP_CONCAT(ui.institution SEPARATOR \',\') AS companies', 'LEFT JOIN user_institutions AS cui ON cui.id = u.current_institution LEFT JOIN user_institutions AS ui ON u.id = ui.user WHERE u.user = :user', [':user' => $user])[0];
        $get['message'] = $get != null ? MESSAGE_EXIST : MESSAGE_NOT_EXIST;
        return $get;
    }
    
}