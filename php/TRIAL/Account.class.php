<?php
/**
 * Description of Account
 *
 * Created on 05/09/2016, ~20:18:19
 * @author Matheus Leonardo dos Santos Martins
 * @copyright (c) 2016, TRIAL
 * 
 * @version 1.3
 * @package TRIAL
 */

namespace NoRedo\TRIAL;

use \PDO, NoRedo\Utils\Request, NoRedo\Utils\Database, NoRedo\Utils\SQL\Query, NoRedo\Utils\SQL\Select, NoRedo\Utils\SQL\Insert, NoRedo\Utils\SQL\Update, NoRedo\Utils\SQL\Delete, NoRedo\Utils\Message, NoRedo\TRIAL\Account\User, NoRedo\TRIAL\Account\Institution, NoRedo\TRIAL\Account\Government, NoRedo\TRIAL\Account\Department;

class Account {
    
    const USER = 'user';
    const INSTITUTION = 'institution';
    const INSTITUTION_MEMBER = 'institution_member';
    const GOVERNMENT = 'government';
    const GOVERNMENTAL_DEPARTMENT = 'governmental_department';
    
    const ID = 'id';
    const PHOTO = 'photo';
    const LOGIN = 'login';
    const EMAIL = 'email';
    const PASSWORD = 'password';
    const PERMISSION = 'permission';
    const ACTIVATED = 'activated';
    const SIGNED_UP = 'register_date_time';
    
    private static $con;
    
    private $type_account;
    private $account;    
    protected $id;
    protected $email;
    protected $password;
    protected $activated;
    // added on 16/12/2016, 16:22:14
    protected $permission;
    
    protected function __construct(string $type, Account $account = null) {
        $this->type_account = $type;
        if ($account != null) {
            $this->account = $account;
            if (!($account instanceof User) && !($account instanceof Institution) && !($account instanceof Government) && !($account instanceof Department)) {
                throw new InvalidArgumentException("account argument isn't a instance of User, Institution, Government, or Department class");
            }
        }
        
    }
    
    /*
     * Added on 12/02/2017 23:08:53
     */
    private static function con() {
        if (!self::$con) {
            self::$con = Database::connect(DATABASE_USERS);
        }
        return self::$con;
    }
    
    public static function authenticate($login, string $password, $type_account = self::USER, bool $permanent = false) : array {
        switch ($type_account) {
            case self::USER:
                $result = self::userAuth($login, $password, $permanent);
                break;
            case self::INSTITUTION:
                $result = self::institutionAuth($login, $password, $permanent);
                break;
            case self::INSTITUTION_MEMBER:
                $result = self::institutionMemberAuth($login, $password, $permanent);
                break;
            case self::GOVERNMENT:
                $result = self::governmentAuth($login, $password, $permanent);
                break;
            case self::GOVERNMENTAL_DEPARTMENT:
                $result = self::governmentalDepartmentAuth($login, $password, $permanent);
                break;
        }
	return $result;
    }
    
    public static function create(Account $account) : array {
        $is_user = $account instanceof User;
        $is_institution = $account instanceof Institution;
        $is_government = $account instanceof Government;
        $is_governmental_department = $account instanceof Department;
        if (!$is_user && !$is_institution && !$is_government && !$is_governmental_department) {
            throw new InvalidArgumentException("Account isn't a instance of User, Institution, Government, or Department class");
        } else {
            if ($is_user) {
                $query = (new Insert(self::con()))->table(User::TABLE)->columns('first_name, last_name, birthday, sex, email, postal_code, password, ip, register_date_time')->values([$account->getFirstName(), $account->getLastName(), date_format($account->getBirthday(), 'Y-m-d'), $account->getSex(), $account->getEmail(), $account->getAddress()->getPostalCode(), password_hash($account->getPassword(), PASSWORD_DEFAULT), null, date('Y-m-d H:i:s')]);
            } else if ($is_institution) {
                $query = (new Insert(self::con()))->table(TABLE_INSTITUTIONS)->columns('cnpj, name, infos, email, password, register_date_time')->values([$account->getCNPJ(), $account->getName(), $account->getInfos(), $account->getEmail(), password_hash($account->getPassword(), PASSWORD_DEFAULT), date('Y-m-d H:i:s')]);
            } else if ($is_government) {
                $query = (new Insert(self::con()))->table(TABLE_GOVERNMENTALS)->columns('name, email, password, register_date_time')->values([$account->getName(), $account->getEmail(), password_hash($account->getPassword(), PASSWORD_DEFAULT), date('Y-m-d H:i:s')]);
            } else if ($is_governmental_department) {
                $query = (new Insert(self::con()))->table('governamental_departments')->columns('name, login, password, register_date_time')->values([$account->getName(), $account->getLogin(), password_hash($account->getPassword(), PASSWORD_DEFAULT), date('Y-m-d H:i:s')]);
            }
            return Query::helper($query->run(), function ($query) {
                $result = $query->getResult();
                $result['message'] = Message::SAVED_WITH_SUCCESS;
                return $result;
            });
        }
    }
    
    private static function userAuth(&$login, string &$password, bool &$permanent) : array {
        $id_login = gettype($login) === 'integer';
        return Query::helper((new Select(self::con()))->table(TABLE_USERS)->columns(User::ID . ', ' . User::FIRST_NAME . ', ' . User::LAST_NAME . ', ' . User::EMAIL . ', ' . User::PASSWORD . ', ' . User::ACTIVATED . ', ' . User::PERMISSION)->where($id_login ? User::ID . ' = :id' : User::EMAIL . ' = :email')->values($id_login ? [':id' => $login] : [':email' => $login])->fetchMode(PDO::FETCH_CLASS, User::class)->run(), function ($user) use ($login, $password, $permanent) {
            if ($user->existRows()) {
                $account = $user->getResult()[0];
                if ($account->checkPassword($password)) {
                    $result = ['message' => $account->isActivated() ? Message::EXIST : Message::NOT_ACTIVATED, 'id' => $account->getId(), 'name' => $account->getFirstName(), 'last_name' => $account->getLastName(), 'photo_url' => $account->getPhotoUrl(), 'permission' => $account->getPermission(), 'account' => self::USER];
                    self::createCookies([COOKIE_ID_TRIAL => $account->getId(), COOKIE_NAME => $account->getFirstName(), COOKIE_EMAIL => $account->getEmail(), COOKIE_PERMISSION => $account->getPermission(), COOKIE_TYPE => self::USER], $permanent);
                } else {
                    $result['message'] = Message::ERROR_PASSWORD_INCORRECT;
                }
            } else {
            	$result['message'] = Message::NOT_EXIST;
            }
            return $result;
        });
    }
    
    private static function institutionAuth(&$login, string &$password, bool &$permanent) : array {
        $id_login = gettype($login) === 'integer';
        return Query::helper((new Select(self::con()))->table(TABLE_INSTITUTIONS)->columns('id, name, email, password, activated')->where($id_login ? 'id = :id' : 'email = :email')->values($id_login ? [':id' => $login] : [':email' => $login])->fetchMode(PDO::FETCH_CLASS, Institution::class)->run(), function ($account) use ($login, $password, $permanent) {
            if ($account->existRows()) {
                $account = $account->getResult()[0];
                if ($account->checkPassword($password)) {
                    $result = ['message' => $account->isActivated() ? Message::EXIST : Message::NOT_ACTIVATED, 'id' => $account->getId(), 'name' => $account->getName(), 'photo_url' => $account->getPhotoUrl(), 'account' => self::INSTITUTION];
                    self::createCookies([COOKIE_ID_TRIAL => $account->getId('id'), COOKIE_TI_NAME => $account->getName(), COOKIE_TI_EMAIL => $account->getEmail(), COOKIE_TYPE => self::INSTITUTION], $permanent);
                } else {
                    $result['message'] = Message::ERROR_PASSWORD_INCORRECT;
                }
            }  else {
                $result['message'] = Message::NOT_EXIST;
            }
            return $result;
        });
    }
    
    private static function institutionMemberAuth(&$login, string &$password, bool &$permanent) : array {
        $account = selectDB(self::$con, 'users AS u', 'u.id AS member_id, u.name AS member_name, u.password, u.permission AS member_permission, IF(COUNT(i.id) > 0, true, false) AS have_institution, COUNT(i.id) AS total_institutions, GROUP_CONCAT(i.id SEPARATOR \', \') AS id, GROUP_CONCAT(i.name SEPARATOR \', \') AS name, GROUP_CONCAT(i.email SEPARATOR \', \') AS email, GROUP_CONCAT(i.activated SEPARATOR \', \') AS activated', 'LEFT JOIN institutions_members AS im ON im.user = u.id LEFT JOIN institutions AS i ON i.id = im.institution WHERE u.email = :email', [':email' => $login]);
        if ($account != null) {
            $account = $account[0];
            if (password_verify($password, $account['password']) ? true : $password === $account['password']) {
	        $result = ['message' => $account['have_institution'] ? Message::EXIST : Message::MEMBER_WITHOUT_INSTITUTION, 'institutions' => ['have_institutions' => $account['have_institution']], 'member' => ['id' => $account['member_id'], 'name' => $account['member_name'], 'permission' => $account['member_permission']]];
	        self::$concludeAuthenticationWeb($account, $permanent);
                if ($account['have_institution']) {
                    $result['institutions']['total_institutions'] = $account['total_institutions'];
                    $account = ['id' => explode(', ', $account['id']), 'name' => explode(', ', $account['name']), 'email' => explode(', ', $account['email'])];
                    foreach ($account['id'] as $key => $id) {
                        $result['institutions'][$key] = ['id' => $id, 'name' => $account['name'][$key], 'email' => $account['email'][$key]];
                    }
                }
            } else {
            	$result['message'] = Message::ERROR_PASSWORD_INCORRECT;
            }
        } else {
            $result['message'] = Message::NOT_EXIST;
        }
        return $result;
    }
    
    private static function governmentAuth(&$login, string &$password, bool &$permanent) : array {
        $id_login = gettype($login) === 'integer';
        return Query::helper((new Select(self::con()))->table(TABLE_GOVERNMENTS)->columns('id, name, email, password, activated')->where($id_login ? 'id = :id' : 'email = :email')->values($id_login ? [':id' => $login] : [':email' => $login])->fetchMode(PDO::FETCH_CLASS, Government::class)->run(), function ($account) use ($login, $password, $permanent) {
            if ($account->existRows()) {
                $account = $account->getResult()[0];
                if ($account->checkPassword($password)) {
                    $result = ['message' => $account->isActivated() ? Message::EXIST : Message::NOT_ACTIVATED, 'id' => $account->getId(), 'name' => $account->getName(), 'permission' => $account->getPermission(), 'photo_url' => $account->getPhotoUrl(), 'account' => self::GOVERNMENT];
                    self::createCookies([COOKIE_ID_TRIAL => $account->getId('id'), COOKIE_TG_NAME => $account->getName(), COOKIE_TG_EMAIL => $account->getEmail(), COOKIE_TYPE => self::GOVERNMENT], $permanent);
                } else {
                    $result['message'] = Message::ERROR_PASSWORD_INCORRECT;
                }
            }  else {
                $result['message'] = Message::NOT_EXIST;
            }
            return $result;
        });
    }
    
    private static function governmentalDepartmentAuth(&$login, string &$password, bool &$permanent) : array {
        $id_login = gettype($login) === 'integer';
        return Query::helper((new Select(self::con()))->table('governmental_departments')->columns('id, government, name, login, password, activated')->where($id_login ? 'id = :id' : 'login = :login')->values($id_login ? [':id' => $login] : [':login' => $login])->fetchMode(PDO::FETCH_CLASS, Department::class)->run(), function ($account) use ($login, $password, $permanent) {
            if ($account->existRows()) {
                $account = $account->getResult()[0];
                if ($account->checkPassword($password)) {
                    $result = ['message' => $account->isActivated() ? Message::EXIST : Message::NOT_ACTIVATED, 'id' => $account->getGovernmentId(), 'department_id' => $account->getId(), 'name' => $account->getName(), 'permission' => $account->getPermission(), 'photo_url' => $account->getPhotoUrl(), 'account' => self::GOVERNMENTAL_DEPARTMENT];
                    self::createCookies([COOKIE_ID_TRIAL => $account->getId(), COOKIE_TGD_NAME => $account->getName(), COOKIE_PERMISSION => $account->getPermission(), COOKIE_TG_ID_TRIAL => $account->getGovernmentId(), COOKIE_TYPE => self::GOVERNMENTAL_DEPARTMENT], $permanent);
                } else {
                    $result['message'] = Message::ERROR_PASSWORD_INCORRECT;
                }
            }  else {
                $result['message'] = Message::NOT_EXIST;
            }
            return $result;
        });
    }
    
    private static function createCookies(array $cookies, bool &$permanent) {
        $domain = $_SERVER['HTTP_HOST'] !== 'localhost' ? '.trialent.com' : 'localhost';
        foreach ($cookies as $key => $value) {
            setcookie($key, $value, !$permanent ? 0 : strtotime('+30 days'), '/', $domain);
        }
    }
    
    public static function logout() : array {
        $host = $_SERVER['HTTP_HOST'];
        foreach ($_COOKIE as $key) {
            if (strpos($key, 'TRL_') !== false) {
                if ($host !== 'localhost') {
                    $domain = (strpos($key, 'SR') !== false ? 'serginho' : strpos($key, 'CL') !== false ? 'clicker' : strpos($key, 'ON') !== false ? 'oportunidadeja' : '') . '.trialent.com';
                } else {
                    $domain = $host;
                }
                setcookie($key, null, time() - 3600, '/', $domain);
            }
        }
        return ['message' => Message::SAVED_WITH_SUCCESS];
    }
    
    public static function edit(Account $account, array $field) : array {
        if (isset($field['tmp_name'])) {
            if (is_uploaded_file($field['tmp_name'])) {
                $divided_namespace = explode('\\', get_class($account));
                $url = '/TRIAL/images/' . strtolower(array_pop($divided_namespace)) . '/profile/' . $account->getId() . '/' . $account->getId() . '.jpg';
                if (move_uploaded_file($field['tmp_name'], $_SERVER['DOCUMENT_ROOT'] . $url)) {
                    return ['message' => Message::SAVED_WITH_SUCCESS, 'url' => $url];
                }
            }
            return ['message' => Message::ERROR, 'error' => ['message' => 'File can\'t be uploaded']];
        } else {
            return Query::helper((new Update(self::con()))->table($account::TABLE)->columns(implode(', ', array_keys($field)))->where(self::ID . ' = :id')->values(array_values($field))->valuesWhere([':id' => $account->getId()])->run(), function ($query) {
                return ['message' => Message::SAVED_WITH_SUCCESS];
            });
        }
    }
    
    public static function delete($object) : CRUD {
        
    }
    
    public function getId() {
        return $this->id;
    }
    
    public function getEmail() {
        return $this->email;
    }
    
    public function getPassword() {
        return $this->password;
    }
    
    public function checkPassword($password) {
        return password_verify($password, $this->password) || $this->password === $password;
    }
    
    public function isActivated() {
        return !$this->activated;
    }
    
    public function getPhotoUrl() {
        $url = 'http://trialent.com/images/' . $this->type_account . '/profile/' . $this->id . '/' . $this->id . '.jpg';
        $response = Request::make($url);
        return  $response->success() && $response->getResult()['http_code'] === 200 ? $url : '/no-redo/images/TRIAL/logo/icon/social/min/T_icon_social_invert.png';
    }
    
    // added on 16:22:39
    public function getPermission() {
        return $this->permission;
    }
    
    public function getAllAccounts() : array {
        return Query::helper((new Select(self::con()))->table(DATABASE_USERS . '.' . TABLE_USERS . ' AS trial')->columns('CONCAT(\'{"id": \', clicker.id, \', "type": "\', clicker.type, \'", "register_date_time": "\', clicker.register_date, \' \', clicker.register_time, \'"}\') AS clicker')->leftJoin(DATABASE_CLICKER . '.' . TABLE_USERS . ' AS clicker ON clicker.user = trial.id')->where('trial.id = :user')->values([':user' => $this->getId()])->run(), function ($accounts) {
            if ($accounts->existRows()) {
                $result = $accounts->getResult()[0];
                $result['message'] = Message::EXIST;
            } else {
                $result = ['message' => Message::NOT_EXIST];
            }
            return $result;
        });
    }

    public static function getHowKnowRegisters() : array {
        return Query::helper((new Select(self::con()))->table('how_know')->columns('id, how')->run(), function ($query) {
            $result = $query->getResult();
            $result['message'] = $query->existRows() ?  Message::EXIST : Message::NOT_EXIST;
            return $result;
        });
    }
    
    public static function checkEmail(string $email) : array {
        $user = new User($email);
        if ($user->getId() != null) {
            $response['user'] = $user;
        }
        $response['message'] = $user->getId() != null ?  Message::EXIST : Message::NOT_EXIST;
        return $response;
    }
    
    /* 
     * 12/02/2017
     *      18:56:40:
     *          renamed: createVerificationCode() => createResetPasswordToken()
     */
    public static function createResetPasswordToken(Account $account) : array {
        $raw_token = uniqid(rand(), true);
        $token = md5($raw_token);
        if ($account instanceof User) {
            $table = 'users';
        } else if ($account instanceof Institution) {
            $table = 'institutions';
        } else if ($account instanceof Government) {
            $table = 'governments';
        } else {
            $table = 'governmental_departments';
        }
        return Query::helper((new Update(self::con()))->table($table)->columns('reset_password_token, reset_password_datetime')->where('id = :id')->values([$token, date('Y-m-d H:i:s')])->valuesWhere([':id' => $account->getId()])->run(), function ($query) use ($raw_token) {
            return ['token' => $raw_token, 'message' => Message::SAVED_WITH_SUCCESS];
        });
    }
    
    /* 
     * 12/02/2017
     *      22:59:50:
     *          renamed: checkVerificationCode() => checkResetPasswordToken()
     */
    public static function checkResetPasswordToken(Account $account, $token) : array {
        if ($account instanceof User) {
            $table = 'users';
        } else if ($account instanceof Institution) {
            $table = 'institutions';
        } else if ($account instanceof Government) {
            $table = 'governments';
        } else {
            $table = 'governmental_departments';
        }
        return Query::helper((new Select(self::con()))->table($table)->columns('reset_password_token, reset_password_datetime')->where('id = :id')->values([':id' => $account->getId()])->run(), function ($query) use ($account, $table, $token) {
            if ($query->existRows()) {
                $result = $query->getResult()[0];
                if ($result['reset_password_token'] === md5($token)) {
                    (new Update(self::con()))->table($table)->columns('reset_password_token')->where('id = :id')->values([null])->valuesWhere([':id' => $account->getId()])->run();
                    $response['message'] = Message::EXIST;
                } else {
                    $response['message'] = Message::NOT_EXIST;
                }
            } else {
                $response['message'] = Message::ERROR;
            }
            return $response;
        });
    }
    
}