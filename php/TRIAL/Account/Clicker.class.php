<?php

/**
 * Description of Clicker
 *
 * @copyright (c) 2016, TRIAL
 * @author Matheus Leonardo dos Santos Martins
 * 
 * @version 1.1
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

use \PDO, NoRedo\Utils\Database, NoRedo\Utils\SQL\Query, NoRedo\Utils\SQL\Select, NoRedo\Utils\SQL\Insert, NoRedo\Utils\Message, NoRedo\TRIAL\Account\Institution;

class Clicker implements \Serializable, \JsonSerializable {
    
    const ID = 'id';
    const USER = 'user';
    const TYPE = 'user';
    const UNIQUE_CODE = 'unique_code';
    const INSTITUTIONS = 'institutions';
    const SIGNED_ON = 'register_date_time';
    
    const TEACHER = 'TEACHER';
    const STUDENT = 'STUDENT';
    
    const NO_ID = -1;
    
    private static $con;
    
    /**
     * Clicker ID
     * 
     * @var int 
     */
    private $id;
    /**
     * Clicker TRIAL Account
     * 
     * @var User 
     */
    private $user;
    /**
     * Clicker account type
     * 
     * @var string 
     */
    private $type;
    /**
     * Clicker unique code
     * 
     * @var mixed 
     */
    private $unique_code;
    /**
     * Clicker linked Institutions
     * 
     * @var array 
     */
    private $institutions;
    
    public function __construct($values = null) {
        if ($values != null && gettype($values) === 'object') {
        } else {
            $values = $this;
        }
        foreach ($values as $key => $value) {
            switch ($key) {
                case 'user':
                    $value = $value instanceof User ? $value : new User((int) $value);
                    break;
                case 'institution':
                    $institutions = json_decode($value);
                    $key .= 's';
                    $value = [];
                    foreach ($institutions as $institution) {
                        $value[] = (new Institution\Builder())->setId($institution->{'id'})->setName($institution->{'name'})->build();
                    }
                    break;
            }
            $this->{$key} = $value;
        }
    }
    
    /**
     * Gets the Database connection. This creates a new connection if hasn't estabilished yet.
     * 
     * @return PDO Estabilished connection
     * @since 1.01
     */
    private static function con() : PDO {
        if (!self::$con) {
            self::$con = Database::connect(DATABASE_CLICKER);
        }
        return self::$con;
    }
    
    /**
     * Authenticates a Clicker account
     * 
     * @return array Infos about this authentication
     * @since 1.0
     */
    public static function authenticate($id_trial) {
        $profile = self::getProfile($id_trial);
        if ($profile['message'] === Message::EXIST) {
            $domain = filter_input(INPUT_SERVER, 'HTTP_HOST') !== 'localhost' ? 'clicker.trialent.com' : 'localhost';
            setcookie(COOKIE_CLICKER_ID, serialize($profile['profile']), time() + (60 * 60 * 24 * 365), '/', $domain);
        }
        return $profile;
    }
    
    /**
     * Creates a new Clicker account
     * 
     * @return array Infos about account creation
     * @since 1.0
     */
    public static function create($user, $institution, $type) : array {
        return Query::helper((new Insert(self::con()))->table('users')->columns('user, institution, type, register_date_time, ip')->values([$user, $institution, $type, date('Y-m-d H:i:s'), getenv('REMOTE_ADDR')])->run(), function ($query) {
            $result = $query->getResult();
            $result['message'] = Message::SAVED_WITH_SUCCESS;
            return $result;
        });
    }
    
    /**
     * 
     * @return mixed Clicker profile
     * @since 1.0
     */
    public static function getProfile($user) {
        return Query::helper((new Select(self::con()))->table('users AS u')->columns('u.id, u.user, u.type, u.unique_code, CONCAT(\'[{"id": \', u.institution, \', "name": "\', i.name, \'", "address": \', CONCAT(\'{"address": "\', i.address, \'", "city": "\', i.city, \'", "state": "\', i.state, \'"}\'), \'}]\') AS institution')->leftJoin('institutions AS i ON i.id = u.institution')->where('u.user = :user')->values([':user' => $user])->fetchMode(\PDO::FETCH_CLASS, Clicker::class)->run(), function ($query) {
            return ['profile' => $query->existRows() ? $query->getResult()[0] : null, 'message' => $query->existRows() ? Message::EXIST : Message::NOT_EXIST];
        });
    }
    
    /**
     * 
     * @return int Clicker ID
     * @since 1.1
     */
    public function getId(): int {
        return $this->id ?? self::NO_ID;
    }
    
    /**
     * 
     * @return User Clicker user TRIAL Account
     * @since 1.1
     */
    public function getUser() : User {
        return $this->user;
    }
    
    /**
     * 
     * @return mixed Clicker type Account
     * @since 1.1
     */
    public function getType() {
        return $this->type;
    }
    
    /**
     * 
     * @return mixed Clicker user institution Unique Code
     * @since 1.1
     */
    public function getUniqueCode() {
        return !empty($this->unique_code) ? $this->unique_code : $this->id;
    }
    
    /**
     * Gets all institutions linked to this Clicker account
     * 
     * @return array All account Institutions
     * @since 1.1
     */
    public function getInstitutions() : array {
        return $this->institutions;
    }
    
    /**
     * Gets single institution linked to this Clicker account by index
     * 
     * @return Institution Some Clicker Institution
     */
    public function getInstitution() : Institution {
        $index = func_num_args() > 0 ? (int) func_get_arg(0) : 0;
        return $this->institutions[$index];
    }

    /**
     * 
     * @return string
     * @since 1.1
     */
    public function serialize(): string {
        return serialize([
            $this->id,
            $this->user,
            $this->type,
            $this->unique_code,
            $this->institutions
        ]);
    }

    /**
     * 
     * @since 1.1
     */
    public function unserialize($serialized) {
        list($this->id, $this->user, $this->type, $this->unique_code, $this->institutions) = unserialize($serialized);
    }

    /**
     * 
     * @since 1.1
     */
    public function jsonSerialize() {
        return [self::ID => $this->getId(), self::USER => $this->getUser(), self::TYPE => $this->getType(), self::UNIQUE_CODE => $this->getUniqueCode(), self::INSTITUTIONS => $this->getInstitutions()];
    }

}