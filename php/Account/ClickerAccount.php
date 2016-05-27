<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class ClickerAccount {
    
    private $con;
    
    public function __construct() {
        $instanceClass = new ConnectDB(DB_DATABASE, DB_USER, DB_PASSWORD, DB_PREFIX . DATABASE_CLICKER);
        $this->con = $instanceClass->connect();
    }
    
    public function authenticateUser($id_trial) {
        $profile = $this->getProfile($id_trial);
        if ($profile['message'] === MESSAGE_EXIST) {
            setcookie(COOKIE_CLICKER_ID, $profile['id'], time() + (60 * 60 * 24 * 365), '/', 'trialent.com');
            setcookie(COOKIE_CLICKER_INSTITUTION, $profile['institution'], time() + (60 * 60 * 24 * 365), '/', 'trialent.com');
            setcookie(COOKIE_CLICKER_TYPE, $profile['type'], time() + (60 * 60 * 24 * 365), '/', 'trialent.com');
        }
        return $profile;
    }
    
    public function createAccount($user, $institution, $type) {
        $result = insertDB($this->con, 'users', 'user, institution, type, register_date, register_time, ip', array($user, $institution, $type, date('Y-m-d'), date('H:i:s'), getenv('REMOTE_ADDR')));
        $result['message'] = MESSAGE_SAVED_WITH_SUCCESS;
        return $result;
    }
    
    public function getProfile($user) {
        $result = selectDB($this->con, 'users', 'id, user, institution, type', 'WHERE user = :user', array(':user' => $user));
        $result[0]['message'] = $result != null ? MESSAGE_EXIST : MESSAGE_NOT_EXIST;
        return $result[0];
    }
    
}