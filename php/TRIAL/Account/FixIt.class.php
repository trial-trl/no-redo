<?php
/**
 * Description of FixItAccount
 * 
 * ConnectDB adapted to new version on 08/09/2016, ~20:47:28
 * 
 * Created on 05/09/2016, ~16:11:40
 * @copyright (c) 2016, TRIAL
 * @author Matheus Leonardo dos Santos Martins
 * 
 * @version 1.03NC
 * @package Account
 */

/*
 * Implementation of RG, CPF, Escolaridade, Ocupação principal started on 05/09/2016, ~16:32:00
 * 
 * 22/11/2016, 18:28:20 - 18:44:53 => updated all codes to new codes, fixed authenticateUser() not making login right
 * 
 * 24/12/2016, 14:51:52 => update ConnectDB code to DB code; added use Query, Select statement
 * 
 * 15/03/2017, 12:29:15 => added namespace NoRedo\TRIAL\Account;
 */

namespace NoRedo\TRIAL\Account;

use \PDO, \DateTime,
        NoRedo\TRIAL\Account as TRIALAccount, NoRedo\Utils\Database, NoRedo\Utils\SQL\Query, NoRedo\Utils\SQL\Select, NoRedo\Utils\SQL\Insert;

Database::setHost('localhost');
Database::setAuth('root', '');

class FixIt {
    
    const TABLE_CITIZENS = 'users';
    const TABLE_GOVERNMENTS = 'governments';
    
    const ID = 'id';
    const USER = 'user';
    const ACCOUNT_TYPE = 'type';
    const LEVEL = 'level';
    const XP = 'experience';
    const SIGNED_ON = 'register_date_time';
    
    private static $con;
    
    private $id = 0;
    private $user = 0;
    private $type;
    private $level = 1;
    private $experience = 0;
    private $register_date_time;
    
    private function __construct(array $copy = null) {
        $loop = $copy ?? $this;
        foreach ($loop as $key => $value) {
            if ($key === self::ACCOUNT_TYPE)
                continue;
            if ($key === self::USER && (gettype($value) === 'integer' || gettype($value) === 'string'))
                $value = $this->type === TRIALAccount::USER ? new User((int) $value) : new Government((int) $value);
            $this->{$key} = $value;
        }
        unset($this->type);
    }
    
    /**
     * Gets the Database connection. This creates a new connection if hasn't estabilished yet.
     * 
     * @return PDO Estabilished connection
     * @since 1.03
     */
    private static function con() : PDO {
        if (!self::$con) {
            self::$con = Database::connect(DATABASE_FIX_IT);
        }
        return self::$con;
    }
    
    /**
     * @return FixIt
     * @since 1.03
     */
    public static function copy(array $copy) : FixIt {
        return new self($copy);
    }
    
    /**
     * @param TRIALAccount $account
     * @return type
     * @since 1.0
     */
    public static function authenticate(int $id, string $type = TRIALAccount::USER) {
        $profile = self::get($id, $type);
        if (!$profile) {
            $profile = self::create($id, $type);
        }
        $domain = filter_input(INPUT_SERVER, 'HTTP_HOST') !== 'localhost' ? '.trialent.com' : 'localhost';
        setcookie(COOKIE_FIX_IT_ID, $profile->getId(), time() + (60 * 60 * 24 * 365), '/', $domain);
        return $profile;
    }
    
    /**
     * 
     * @param int $id
     * @param string $type
     * @return type
     * @since 1.0
     */
    public static function get(int $id, string $type = TRIALAccount::USER)  {
        return Query::helper((new Select(self::con()))->table($type === TRIALAccount::USER ? self::TABLE_CITIZENS : self::TABLE_GOVERNMENTS)->columns($type === TRIALAccount::USER ? [self::ID, self::USER, self::LEVEL, self::XP, '\'' . $type . '\' AS ' . self::ACCOUNT_TYPE]: [self::ID, self::USER])->where(self::USER . ' = :user')->values([':user' => $id])->fetchMode(PDO::FETCH_CLASS, self::class)->run(), function ($query) {
            if ($query->existRows()) {
                return $query->getResult()[0];
            }
            return null;
        });
    }
    
    /**
     * 
     * @param TRIALAccount $account
     * @return type
     * @since 1.03
     */
    public static function create(int $id, string $type = TRIALAccount::USER) {
        return Query::helper((new Insert(self::con()))->table($type === TRIALAccount::USER ? self::TABLE_CITIZENS : self::TABLE_GOVERNMENTS)->columns([self::USER, self::SIGNED_ON])->values([$id, $signed_on = date('Y-m-d H:i:s')])->run(), function ($query) use ($id, $type, $signed_on) {
            return FixIt::copy([self::ID => $query->getResult()['id'], self::USER => $id, self::ACCOUNT_TYPE => $type, self::LEVEL => 1, self::XP => 0, self::SIGNED_ON => $signed_on]);
        });
    }
    
    /**
     * @return int Account ID
     * @since 1.03
     */
    public function getId() {
        return $this->id;
    }
    
    /**
     * @return TRIALAccount Account user
     * @since 1.03
     */
    public function getUser() : TRIALAccount {
        return $this->user;
    }
    
    /**
     * @return int Citizen level
     * @since 1.03
     */
    public function getLevel() : int {
        return $this->level;
    }
    
    /**
     * @return int Citizen XP
     * @since 1.03
     */
    public function getXP() : int {
        return $this->experience;
    }
    
    /**
     * @return DateTime Citizen signed on
     * @since 1.03
     */
    public function getSignedOn() : DateTime {
        return new DateTime($this->register_date_time);
    }
    
}