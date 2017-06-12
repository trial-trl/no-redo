<?php
/**
 * Description of OpportunityNowAccount
 * 
 * ConnectDB adapted to new version on 08/09/2016, ~20:47:28
 * 
 * Created on 21/10/2016, 17:40:52
 * @author Matheus Leonardo dos Santos Martins
 * @copyright (c) 2016, TRIAL
 * 
 * @version 1.01NC
 * @package Account
 */
 
namespace NoRedo\TRIAL\Account;

use \PDO, NoRedo\Utils\Database, NoRedo\Utils\SQL\Query, NoRedo\Utils\SQL\Select, NoRedo\Utils\SQL\Insert, NoRedo\Utils\Message;

Database::setHost('localhost');
Database::setAuth('root', '');

/*
 * 22/05/2017 (v1.01NC)
 *      01:47:04:
 *          added namespace NoRedo\TRIAL\Account
 *          added con() method
 *      01:48:44:
 *          removed Account from OpportunityNowAccount class name
 *          all methods changed to static
 *          renamed
 *              authenticateUser => authenticate
 *              createAccount => create
 *              getProfile => get
 *      01:50:02
 *          private $con => private static $con
 *      01:53:48
 *          added TABLE, ID, USER, FREELANCER, and SIGNED_ON constants
 */

class OpportunityNow {
    
    const TABLE = 'users';
    
    const ID = 'id';
    const USER = 'user';
    const FREELANCER = 'freelancer';
    const SIGNED_ON = 'register_date_time';
    
    private static $con;
    
    /**
     * Gets the Database connection. This creates a new connection if hasn't estabilished yet.
     * 
     * @return PDO Estabilished connection
     * @since 1.01NC
     */
    private static function con() : PDO {
        if (!self::$con) {
            self::$con = Database::connect(DATABASE_JOBS_NOW);
        }
        return self::$con;
    }
    
    public function authenticate(int $id_trial) : array {
        $profile = self::get($id_trial);
        if ($profile['message'] === Message::EXIST) {
            $domain = filter_input(INPUT_SERVER, 'HTTP_HOST') !== 'localhost' ? '.trialent.com' : 'localhost';
            setcookie(COOKIE_OPPORTUNITY_NOW_ID, $profile['id'], time() + (60 * 60 * 24 * 365), '/', $domain);
            setcookie(COOKIE_OPPORTUNITY_NOW_FREELANCER, $profile['freelancer'], time() + (60 * 60 * 24 * 365), '/', $domain);
        }
        return $profile;
    }
    
    public static function create($user) : array {
        return Query::helper((new Insert(self::con()))->table(self::TABLE)->columns([self::USER, self::SIGNED_ON])->values([$user, date('Y-m-d H:i:s')])->run(), function ($query) {
            $result = $query->getResult();
            $result['message'] = Message::SAVED_WITH_SUCCESS;
            return $result;
        });
    }
    
    public static function get(int $user) : array {
        return Query::helper((new Select(self::con()))->table(self::TABLE)->columns([self::ID, self::USER, self::FREELANCER])->where('user = :user')->values([':user' => $user])->run(), function ($query) use ($user) {
            $result = $query->existRows() ? $query->getResult()[0] : [self::ID => self::create($user)['id'], self::FREELANCER => false];
            $result['message'] = $query->existRows() ? Message::EXIST : Message::NOT_EXIST;
            return $result;
        });
    }
    
}
