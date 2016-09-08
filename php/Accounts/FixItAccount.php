<?php
/**
 * Description of FixItAccount
 * 
 * Created on 05/09/2016, ~16:11:40
 * @author Matheus Leonardo dos Santos Martins
 * @copyright (c) 2016, TRIAL
 * 
 * @package Account
 */

/*
 * Implementation of RG, CPF, Escolaridade, Ocupação principal started on 05/09/2016, ~16:32:00
 */
class FixItAccount {
    
    private $con;
    
    public function __construct() {
        $instanceClass = new ConnectDB(DB_DATABASE, DB_USER, DB_PASSWORD, DB_PREFIX . DATABASE_STREET_RACE);
        $this->con = $instanceClass->connect();
    }
    
    public function authenticateUser($id_trial) {
        $profile = $this->getProfile($id_trial);
        if ($profile['message'] === MESSAGE_EXIST) {
            $domain = $_SERVER['HTTP_HOST'] !== 'localhost' ? '.trialent.com' : 'localhost';
            setcookie(COOKIE_FIX_IT_ID, $profile['id'], time() + (60 * 60 * 24 * 365), '/', $domain);
        }
        return $profile;
    }
    
    public function getProfile($user) {
        $result = selectDB($this->con, 'users', 'id, user', 'WHERE user = :user', [':user' => $user]);
        if ($result != null) {
            $result = $result[0];
        } else {
            $result = ['user' => $user, 'type' => 'STUDENT', 'id' => insertDB($this->con, 'users', 'user, type, can_use_app, register_date, register_time', [$result['user'], $result['type'], 0, date('Y-m-d'), date('H:i:s')])['id']];
        }
        $result['message'] = $result != null ? MESSAGE_EXIST : MESSAGE_NOT_EXIST;
        return $result;
    }
    
    public function canUseApp($user) {
        $profile = $this->getProfile($user);
        return $profile['message'] === MESSAGE_EXIST && $profile['can_use_app'];
    }
    
}