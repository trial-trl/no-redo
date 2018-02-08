<?php
/**
 * Description of VHC
 *
 * Created on 02/10/2017, 23:55:56
 * @author Matheus Leonardo dos Santos Martins
 * @copyright (c) 2017, TRIAL
 * 
 * @version 1.0
 * @package Account
 */
 
namespace NoRedo\TRIAL\Account;

use NoRedo\Utils\Database, NoRedo\Utils\SQL\Query, NoRedo\Utils\SQL\Select, NoRedo\Utils\SQL\Insert, NoRedo\Utils\Message;

class VHC {

    const DATABASE = DB_PREFIX . 'virtual_heritage_control';
    
    public static function authenticate($id_trial) {
        $profile = self::getProfile($id_trial);
        if ($profile['message'] === Message::EXIST) {
            $domain = $_SERVER['HTTP_HOST'] !== 'localhost' ? 'cvp.trialent.com' : 'localhost';
            setcookie('trl_vhc', base64_encode(json_encode(['id' => $profile['id']])), time() + (60 * 60 * 24 * 365), '/', $domain);
        }
        return $profile;
    }
    
    public static function createAccount($institution) {
        return Query::helper((new Insert(Database::connect(self::DATABASE)))->table('institutions')->columns('institution, licensed, register_date_time')->values([$institution, 0, date('Y-m-d H:i:s')])->run(), function ($query) {
            return $query->getResult() + ['message' => Message::SAVED_WITH_SUCCESS];
        });
    }
    
    public static function getProfile($institution) {
        return Query::helper((new Select(Database::connect(self::DATABASE)))->table('institutions')->columns('id, institution, licensed')->where('institution = :institution')->values([':institution' => $institution])->run(), function ($query) use ($institution) {
            $result = $query->existRows() ? $query->getResult()[0] : ['licensed' => false, 'institution' => $institution, 'id' => self::createAccount($institution)['id']];
            $result['message'] = $query->existRows() ? Message::EXIST : Message::NOT_EXIST;
            return $result;
        });
    }
    
    public static function isLicensed($institution) {
        $profile = self::getProfile($institution);
        return $profile['message'] === Message::EXIST && $profile['licensed'] === true;
    }
    
}