<?php

/**
 * Description of ClickerAccount
 *
 * @copyright (c) 2016, TRIAL
 * @author Matheus Leonardo dos Santos Martins
 * 
 * @version 1.0
 * @package Account
 */
class TRIALGamesAccount {
    
    private $con;
    
    public function __construct() {
        $instanceClass = new ConnectDB(DB_DATABASE, DB_USER, DB_PASSWORD, DB_PREFIX.DATABASE_GAMES);
        $this->con = $instanceClass->connect();
    }
    
    public function createTRIALGamesAccount($trial_id, $nick) {
        $check = selectDB($this->con, TABLE_USERS, 'trial_id', 'WHERE trial_id = :trial_id', array(':trial_id' => $trial_id));
        if ($check != null) { 
            $result['message'] = MESSAGE_EXIST;
        } else {
            $result = insertDB($this->con, TABLE_USERS, 'trial_id, nick, register_date, register_time', array($trial_id, $nick, date('Y-m-d'), date('H:i:s')));
            $result['message'] = MESSAGE_SAVED_WITH_SUCCESS;
        }
        return $result;
    }
    
    public function getAccount($val_account) {
        $is_nick = gettype($val_account) === 'string';
        if ($is_nick) {
            $account = selectDB($this->con, TABLE_USERS, 'id AS games_account_id, trial_id, trial_tokens', 'WHERE nick = :nick', array(':nick' => $val_account))[0];
        } else {
            $account = selectDB($this->con, TABLE_USERS, 'id AS games_account_id, nick, trial_tokens', 'WHERE trial_id = :trial_id', array(':trial_id' => $val_account))[0];
        }
        $account['message'] = $account != null ? MESSAGE_EXIST : MESSAGE_NOT_EXIST;
	return $account;
    }
    
    public function getGameProfile($trial_id) {
        $profile = selectDB($this->con, TABLE_USERS, 'id AS games_account_id, nick, trial_tokens', 'WHERE trial_id = :trial_id', array(':trial_id' => $trial_id))[0];
        $result['message'] = $profile != null ? MESSAGE_EXIST : MESSAGE_NOT_EXIST;
        return $result;
    }
    
    public function checkNickAvailability($nick) {
        $profile = selectDB($this->con, TABLE_USERS, 'nick', 'WHERE nick = :nick', array(':nick' => $nick));
        $result = $profile != null ? MESSAGE_EXIST : MESSAGE_NOT_EXIST;
        return $result;
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
    
    public function changeNick($id, $nick) {
        updateDB($this->con, TABLE_USERS, 'nick', 'id', array($nick, $id));
        $result['message'] = MESSAGE_SAVED_WITH_SUCCESS;
        return $result;
    }
    
}