<?php

/**
 * Description of Clicker
 *
 * @copyright (c) 2016, TRIAL
 * @author Matheus Leonardo dos Santos Martins
 * 
 * @version 1.11
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

use \PDO, NoRedo\Utils\Database, NoRedo\Utils\SQL\Query, NoRedo\Utils\SQL\Select, NoRedo\Utils\SQL\Insert, NoRedo\Utils\Message, NoRedo\TRIAL\Account\Institution, NoRedo\TRIAL\Account\Clicker\Institution as ClickerInstitution;

class Clicker implements \Serializable, \JsonSerializable {
    
    const TABLE_USERS = 'users';
    const TABLE_INSTITUTIONS = 'institutions';
    
    const ID = 'id';
    const USER = 'user';
    const INSTITUTION = 'institution';
    const INSTITUTIONS = 'institutions';
    const SIGNED_ON = 'register_date_time';
    
    const TEACHER = 'TEACHER';
    const STUDENT = 'STUDENT';
    
    const NO_ID = -1;
    const TYPE = 'type';
    
    const OPTION_ACCOUNT_TYPE = 'account_type';
    const OPTION_GET_BY_ID = 'get_by_id';
    const OPTION_USER_AS_ID = 'user_as_id';
    const OPTION_IGNORE_ID = 'ignore_id';
    const OPTION_FILTER = 'filter';
    const OPTION_LIMIT = 'limit';
    const OPTION_OFFSET = 'offset';
    
    private static $con;
    
    /**
     * Clicker ID
     * 
     * @var int 
     */
    private $id;
    /**
     * Clicker TRIAL Account type
     * 
     * @var User 
     */
    private $type;
    /**
     * Clicker TRIAL Account user
     * 
     * @var User 
     */
    private $user;
    /**
     * Clicker TRIAL Account
     * 
     * @var User 
     */
    private $institution;
    /**
     * Clicker linked Institutions
     * 
     * @var array 
     */
    private $institutions;
    
    private function __construct($values = []) {
        $loop = array_merge(($values[self::TYPE] ?? $this->type ?? Institution::USER) === Institution::USER ? [self::ID => $this->id, self::USER => $this->user, self::INSTITUTIONS => $this->institutions] : [self::ID => $this->id, self::INSTITUTION => $this->institution], $values);
        foreach ($loop as $key => $value) {
            switch ($key) {
                case self::USER:
                    $value = $value instanceof User ? $value : (is_string($value) && ($value[0] === '{' || $value[0] === '[') ? User::copy(json_decode($value, true)) : (is_array($value) ? User::copy($value) : User::get((int) $value)));
                    break;
                case self::INSTITUTION:
                    if (array_key_exists(self::ID, $loop)) {
                        $value = ClickerInstitution::get((int) $loop[self::ID]);
                    }
                    break;
                case self::INSTITUTIONS:
                    $institutions = (is_array($value) ? $value : json_decode($value, true)) ?? [];
                    $value = [];
                    foreach ($institutions as $institution) {
                        $value[] = ClickerInstitution::copy($institution);
                    }
                    break;
            }
            $this->{$key} = $value;
        }
        if ($this->type === Institution::USER) {
            unset($this->institution);
        } else {
            unset($this->institutions);
            unset($this->user);
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
    public static function authenticate($id_trial, string $account_type = Institution::USER) {
        $get = self::get($id_trial, [self::OPTION_ACCOUNT_TYPE => $account_type, self::OPTION_USER_AS_ID => true]);
        $profile = $get->getId() != self::NO_ID ? $get : self::create($id_trial, $account_type)['profile'];
        $domain = filter_input(INPUT_SERVER, 'HTTP_HOST') !== 'localhost' ? '.trialent.com' : 'localhost';
        setcookie(COOKIE_CLICKER_ID, $profile->getId(), time() + (60 * 60 * 24 * 365), '/', $domain);
        if (count($profile->getInstitutions()) > 0) {
            setcookie(COOKIE_CLICKER_INSTITUTION, $profile->getInstitution()->getId(), time() + (60 * 60 * 24 * 365), '/', $domain);
            setcookie(COOKIE_CLICKER_TYPE, $profile->getInstitution()->getClickerType(), time() + (60 * 60 * 24 * 365), '/', $domain);
        }
        return array_merge([self::ID => $profile->getId(), 'message' => Message::EXIST], $account_type === Institution::USER ? [self::USER => $profile->getUser(), self::INSTITUTIONS => $profile->getInstitutions()] : [self::INSTITUTION => $profile->getUser()]);
    }
    
    /**
     * Creates a new Clicker account
     * 
     * @return mixed Infos about account creation
     * @since 1.0
     */
    public static function create($user, string $account_type = Institution::USER) {
        $is_user = $account_type === Institution::USER;
        return Query::helper((new Insert(self::con()))->table($is_user ? self::TABLE_USERS : self::TABLE_INSTITUTIONS)->columns($is_user ? [self::USER, self::SIGNED_ON, 'ip'] : [self::INSTITUTION, self::SIGNED_ON])->values(array_merge([$user, date('Y-m-d H:i:s')]), $is_user ? [getenv('REMOTE_ADDR')] : [])->run(), function ($query) use ($user, $is_user, $account_type) {
            return ['profile' => self::copy(array_merge([self::ID => $query->getResult()['id'], self::TYPE => $account_type], $is_user ? [self::USER => $user, self::INSTITUTIONS => '[]'] : [self::INSTITUTION => $user])), 'message' => Message::EXIST];
        });
    }
    
    /**
     * @param mixed $constraint
     * @param array $opts
     * @return mixed Clicker profile
     * 
     * @version 1.01;
     * @since 1.0
     */
    public static function get($constraint, array $opts = []) {
        self::adaptOptions($opts);
        $is_institution = $opts[self::OPTION_ACCOUNT_TYPE] === Institution::INSTITUTION;
        $q = (new Select(self::con()))->table(DATABASE_CLICKER . '.' . ($is_institution ? self::TABLE_INSTITUTIONS . ' AS ci' : self::TABLE_USERS . ' AS u'))->columns($is_institution ? ['ci.' . self::ID, 'ci.' . self::INSTITUTION] : ['u.' . self::ID, 'u.' . self::USER, 'COUNT(ui.' . ClickerInstitution::ID .') AS count_institutions', 'IFNULL(CONCAT(\'[\', GROUP_CONCAT(CONCAT(\'{"' . ClickerInstitution::ID . '": \', ui.' . ClickerInstitution::INSTITUTION . ', \', "' . ClickerInstitution::NAME . '": "\', ui.' . ClickerInstitution::NAME . ', \'", "' . ClickerInstitution::TYPE . '": "\', ui.' . ClickerInstitution::TYPE . ', \'", "' . ClickerInstitution::UNIQUE_CODE . '": "\', ui.' . ClickerInstitution::UNIQUE_CODE .', \'", "' . ClickerInstitution::IN_CLASS . '": "\', IFNULL(ui.' . ClickerInstitution::IN_CLASS . ', \'\'), \'", "' . ClickerInstitution::LICENSED . '": "\', ui.' . ClickerInstitution::LICENSED . ', \'", "' . ClickerInstitution::ALLOWED . '": "\', ui.' . ClickerInstitution::ALLOWED . ', \'", "address": \', ui.address, \'}\')), \']\'), \'[]\') AS ' . self::INSTITUTIONS]);
        if (!$is_institution) {
            $q->leftJoin('(SELECT ui.' . ClickerInstitution::ID . ', ui.' . ClickerInstitution::USER . ', ui.' . ClickerInstitution::INSTITUTION . ', i.' . ClickerInstitution::NAME . ', ui.' . ClickerInstitution::TYPE . ', ui.' . ClickerInstitution::UNIQUE_CODE . ', ui.' . ClickerInstitution::IN_CLASS . ', ci.' . ClickerInstitution::LICENSED . ', CONCAT(\'{"address": "\', ci.address, \'", "city": "\', ci.city, \'", "state": "\', ci.state, \'"}\') AS address, ui.' . ClickerInstitution::ALLOWED . ', ui.' . ClickerInstitution::SIGNED_UP . ' FROM ' . ClickerInstitution::TABLE . ' AS ui LEFT JOIN ' . DATABASE_CLICKER . '.' . self::TABLE_INSTITUTIONS . ' AS ci ON ci.' . ClickerInstitution::ID . ' = ui.' . ClickerInstitution::INSTITUTION . ' LEFT JOIN ' . DATABASE_USERS . '.institutions AS i ON i.id = ci.institution) AS ui ON ui.' . ClickerInstitution::USER . ' = u.id')->groupBy('u.' . self::ID . ', ui.' . ClickerInstitution::INSTITUTION)->countFoundRows();
        }
        return Query::helper($q->where((!$is_institution ? ($opts[self::OPTION_GET_BY_ID] ? 'u.' . ($opts[self::OPTION_USER_AS_ID] ? self::USER : self::ID) : 'ui.' . ClickerInstitution::INSTITUTION) : 'ci.' . ($opts[self::OPTION_GET_BY_ID] ? self::ID : self::INSTITUTION)) . ' = :constraint' . (!empty($opts[self::OPTION_FILTER]) ? ' AND ui.' . self::TYPE . ' = :type' : null))->values([':constraint' => $constraint] + (!empty($opts[self::OPTION_FILTER]) ? [':type' => $opts[self::OPTION_FILTER]] : []))->limit(($opts[self::OPTION_OFFSET] * $opts[self::OPTION_LIMIT]) . ', ' . $opts[self::OPTION_LIMIT])->fetchMode(\PDO::FETCH_CLASS, self::class, [[self::TYPE => $opts[self::OPTION_ACCOUNT_TYPE]]])->run(), function ($query) use ($opts) {
            return $opts[self::OPTION_GET_BY_ID] ? $query->getResult()[0] : $query->getResult();
        });
    }
    
    /**
     * @param type $institution
     * @param string $search
     * @param string $opts
     * @return mixed
     * 
     * @version 1.0
     * @since 1.01
     */
    public static function search($institution, string $search, array $opts = []) {
        $opts[self::OPTION_IGNORE_ID] = isset($opts[self::OPTION_IGNORE_ID]) && (is_int($opts[self::OPTION_IGNORE_ID]) || is_array($opts[self::OPTION_IGNORE_ID])) ? $opts[self::OPTION_IGNORE_ID] : null;
        $opts[self::OPTION_FILTER] = isset($opts[self::OPTION_FILTER]) && is_string($opts[self::OPTION_FILTER]) ? $opts[self::OPTION_FILTER] : self::STUDENT;
        $by_unique_code = preg_match('/^(' . ($opts[self::OPTION_FILTER] === self::TEACHER ? 'CP' : 'CE') . ')[0-9]{1,}/', $search);
    	return Query::helper((new Select(Connection::get()))->table(DATABASE_CLICKER . '.' . self::TABLE_USERS . ' AS u')->columns('u.id, CONCAT(\'{"id": \', u.user, \', "first_name": "\', trial.first_name, \'", "middle_name": "\', IFNULL(trial.middle_name, \'\'), \'", "last_name": "\', trial.last_name, \'"}\') AS user, CONCAT(\'[{"id": \', ui.institution, \', "unique_code": "\', ui.unique_code, \'", "type": "STUDENT"}]\') AS institutions')->leftJoin([DATABASE_CLICKER . '.users_institutions AS ui ON ui.user = u.id AND ui.institution = :institution AND ui.type = :type', DATABASE_USERS . '.users AS trial ON trial.id = u.user'])->where((!empty($opts[self::OPTION_IGNORE_ID]) ? 'u.id NOT IN(' . implode(', ', $opts[self::OPTION_IGNORE_ID]) . ') AND ' : null) . (!$by_unique_code ? '(trial.first_name LIKE CONCAT(\'%\', :search, \'%\') OR trial.middle_name LIKE CONCAT(\'%\', :search, \'%\') OR trial.last_name LIKE CONCAT(\'%\', :search, \'%\'))' : '(ui.unique_code LIKE CONCAT(\'%\', :search, \'%\') OR ui.unique_code = :search)'))->values([':institution' => $institution, ':type' => $opts[self::OPTION_FILTER], ':search' => $search])->fetchMode(\PDO::FETCH_CLASS, ClickerAccount::class, [['type' => 'user']])->limit(5)->run(), function ($query) {
    	    $result = $query->getResult();
    	    $result['message'] = $query->existRows() ? Message::EXIST : Message::NOT_EXIST;
    	    return $result;
    	});
    }
    
    private static function adaptOptions(array &$options) {
        $options[self::OPTION_GET_BY_ID] = isset($options[self::OPTION_GET_BY_ID]) && is_bool($options[self::OPTION_GET_BY_ID]) ? $options[self::OPTION_GET_BY_ID] : true;
        $options[self::OPTION_USER_AS_ID] = isset($options[self::OPTION_USER_AS_ID]) && is_bool($options[self::OPTION_USER_AS_ID]) ? $options[self::OPTION_USER_AS_ID] : false;
        $options[self::OPTION_ACCOUNT_TYPE] = isset($options[self::OPTION_ACCOUNT_TYPE]) && is_string($options[self::OPTION_ACCOUNT_TYPE]) ? $options[self::OPTION_ACCOUNT_TYPE] : Institution::USER;
        $options[self::OPTION_FILTER] = isset($options[self::OPTION_FILTER]) && is_string($options[self::OPTION_FILTER]) ? $options[self::OPTION_FILTER] : null;
        $options[self::OPTION_LIMIT] = isset($options[self::OPTION_LIMIT]) && is_int($options[self::OPTION_LIMIT]) ? $options[self::OPTION_LIMIT] : 20;
        $options[self::OPTION_OFFSET] = isset($options[self::OPTION_OFFSET]) && is_int($options[self::OPTION_OFFSET]) ? $options[self::OPTION_OFFSET] : 0;
    }
    
    /**
     * @param array $data Array to copy
     * @return Clicker Clicker profile
     * @since 1.0
     */
    public static function copy(array $data) : Clicker {
        return new self($data);
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
     * @return string Clicker Account type
     * @since 1.1
     */
    public function getType() : string {
        return $this->type;
    }
    
    /**
     * 
     * @return User Clicker user TRIAL Account
     * @since 1.1
     */
    public function getUser() {
        return $this->user ?? $this->institution ?? null;
    }
    
    /**
     * Gets all institutions linked to this Clicker account
     * 
     * @return array All account Institutions
     * @since 1.1
     */
    public function getInstitutions() : array {
        return $this->institutions ?? [];
    }
    
    /**
     * Gets single institution linked to this Clicker account by ID
     * 
     * @param int $id Institution ID to search
     * @return Institution Some Clicker Institution
     */
    public function getInstitution(int $id = 0) {
        if ($id > 0) {
            foreach ($this->getInstitutions() as $institution) {
                if ($institution->getId() === $id) {
                    return $institution;
                }
            }
        } else {
            return $this->getInstitutions()[0] ?? null;
        }
    }

    /**
     * 
     * @return string
     * @since 1.1
     */
    public function serialize() : string {
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
        return array_filter([self::ID => $this->getId(), self::USER => $this->getUser(), self::INSTITUTIONS => $this->getInstitutions()]);
    }

}