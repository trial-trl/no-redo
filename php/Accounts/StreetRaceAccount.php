<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class StreetRaceAccount {
    
    private $con;
    
    public function __construct() {
        $this->con = (new ConnectDB(DB_PREFIX . DATABASE_STREET_RACE))->connect();
    }
    
    public function authenticateUser($id_trial) {
        $profile = $this->getProfile($id_trial);
        if ($profile['message'] === MESSAGE_EXIST) {
            $domain = $_SERVER['HTTP_HOST'] !== 'localhost' ? '.trialent.com' : 'localhost';
            setcookie(COOKIE_STREET_RACE_ID, $profile['id'], time() + (60 * 60 * 24 * 365), '/', $domain);
            setcookie(COOKIE_STREET_RACE_TYPE, $profile['type'], time() + (60 * 60 * 24 * 365), '/', $domain);
        }
        return $profile;
    }
    
    public function createAccount($user, $institution, $type) {
        $result = insertDB($this->con, 'users', 'user, institution, type, register_date, register_time, ip', array($user, $institution, $type, date('Y-m-d'), date('H:i:s'), getenv('REMOTE_ADDR')));
        $result['message'] = MESSAGE_SAVED_WITH_SUCCESS;
        return $result;
    }
    
    public function getProfile($user) {
        $result = selectDB($this->con, 'users', 'id, user, type, can_use_app', 'WHERE user = :user', [':user' => $user]);
        if ($result != null) {
            $result = $result[0];
        } else {
            $result['user'] = $user;
            $result['type'] = 'STUDENT';
            $result['id'] = insertDB($this->con, 'users', 'user, type, can_use_app, register_date, register_time', [$result['user'], $result['type'], 0, date('Y-m-d'), date('H:i:s')])['id'];
        }
        $result['message'] = $result != null ? MESSAGE_EXIST : MESSAGE_NOT_EXIST;
        return $result;
    }
    
    public function canUseApp($user) {
        $profile = $this->getProfile($user);
        return $profile['message'] === MESSAGE_EXIST && $profile['can_use_app'];
    }
    
}