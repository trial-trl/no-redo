<?php
/**
 * Description of StreetRaceAccount
 *
 * Created on 27/06/2016, 19:23:38
 * @author Matheus Leonardo dos Santos Martins
 * @copyright (c) 2016, TRIAL
 * 
 * @version 1.0NC
 * @package Account
 */

/*
 * 21/12/2016, 22:46:48 - 23:38:26 => update all code to Newer Codes, and all function are static now
 */

use SQL\Query, SQL\Select, SQL\Insert;

class StreetRaceAccount {
    
    public static function authenticateUser($id_trial) {
        $profile = self::getProfile($id_trial);
        if ($profile['message'] === Message::EXIST) {
            $domain = filter_input(INPUT_SERVER, 'HTTP_HOST') !== 'localhost' ? '.trialent.com' : 'localhost';
            setcookie(COOKIE_STREET_RACE_ID, $profile['id'], time() + (60 * 60 * 24 * 365), '/', $domain);
            setcookie(COOKIE_STREET_RACE_TYPE, $profile['type'], time() + (60 * 60 * 24 * 365), '/', $domain);
        }
        return $profile;
    }
    
    public static function createAccount($user) {
        return Query::helper((new Insert(DB::connect(DATABASE_STREET_RACE)))->table('users')->columns('user, type, can_use_app, register_date_time')->values([$user, 'STUDENT', 0, date('Y-m-d H:i:s')])->run(), function ($query) {
            $result = $query->getResult();
            $result['message'] = Message::SAVED_WITH_SUCCESS;
            return $result;
        });
    }
    
    public static function getProfile($user) {
        return Query::helper((new Select(DB::connect(DATABASE_STREET_RACE)))->table('users')->columns('id, user, type, can_use_app')->where('user = :user')->values([':user' => $user])->run(), function ($query) use ($user) {
            $result = $query->existRows() ? $query->getResult()[0] : ['user' => $user, 'type' => 'STUDENT', 'id' => self::createAccount($user)['id']];
            $result['message'] = $query->existRows() ? Message::EXIST : Message::NOT_EXIST;
            return $result;
        });
    }
    
    public static function canUseApp($user) {
        $profile = self::getProfile($user);
        return $profile['message'] === Message::EXIST && $profile['can_use_app'];
    }
    
}