<?php

/**
 * Description of Clicker
 *
 * @copyright (c) 2016, TRIAL
 * @author Matheus Leonardo dos Santos Martins
 * 
 * @version 1.01
 * @package Account
 */

/*
 * 14/02/2017
 *      00:28:57 => added namespace NoRedo\TRIAL\Account;
 *      00:29:40 - 00:33:29 => updated all codes to newer repository codes
 *      00:39:10 => all function are now static, renamed authenticateUser() and createAccount() to authenticate() and create()
 * 
 * 18/02/2017, 18:42:00 => renamed ClickerAccount to Clicker only
 */
 
namespace NoRedo\TRIAL\Account;

use NoRedo\Utils\Database, NoRedo\Utils\SQL\Query, NoRedo\Utils\SQL\Select, NoRedo\Utils\SQL\Insert, NoRedo\Utils\Message;

class Clicker {
    
    private static $con;
    
    public function __construct() {}
    
    private static function con() {
        if (!self::$con) {
            self::$con = Database::connect(DATABASE_CLICKER);
        }
        return self::$con;
    }
    
    public static function authenticate($id_trial) {
        $profile = self::getProfile($id_trial);
        if ($profile['message'] === Message::EXIST) {
            $domain = filter_input(INPUT_SERVER, 'HTTP_HOST') !== 'localhost' ? '.trialent.com' : 'localhost';
            setcookie(COOKIE_CLICKER_ID, $profile['id'], time() + (60 * 60 * 24 * 365), '/', $domain);
            setcookie(COOKIE_CLICKER_INSTITUTION, $profile['institution'], time() + (60 * 60 * 24 * 365), '/', $domain);
            setcookie(COOKIE_CLICKER_TYPE, $profile['type'], time() + (60 * 60 * 24 * 365), '/', $domain);
        }
        return $profile;
    }
    
    public static function create($user, $institution, $type) {
        return Query::helper((new Insert(self::con()))->table('users')->columns('user, institution, type, register_date_time, ip')->values([$user, $institution, $type, date('Y-m-d H:i:s'), getenv('REMOTE_ADDR')])->run(), function ($query) {
            $result = $query->getResult();
            $result['message'] = Message::SAVED_WITH_SUCCESS;
            return $result;
        });
    }
    
    public static function getProfile($user) {
        return Query::helper((new Select(self::con()))->table('users')->columns('id, user, institution, type')->where('user = :user')->values([':user' => $user])->run(), function ($query) {
            if ($query->existRows()) {
                $result = $query->getResult()[0];
                $result['message'] = Message::EXIST;  
            } else {
                $result['message'] = Message::NOT_EXIST;
            }
            return $result;
        });
    }
    
}