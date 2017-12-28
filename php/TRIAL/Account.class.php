<?php
/**
 * Description of Account
 *
 * Created on 05/09/2016, ~20:18:19
 * @author Matheus Leonardo dos Santos Martins
 * @copyright (c) 2016, TRIAL
 * 
 * @version 1.31
 * @package TRIAL
 */

namespace NoRedo\TRIAL;

use \PDO, NoRedo\Utils\Request, NoRedo\Utils\Database, NoRedo\Utils\SQL\Query, NoRedo\Utils\SQL\Select, NoRedo\Utils\SQL\Insert, NoRedo\Utils\SQL\Update, NoRedo\Utils\SQL\Delete, NoRedo\Utils\Message, NoRedo\TRIAL\Account\User, NoRedo\TRIAL\Account\Institution, NoRedo\TRIAL\Account\Government, NoRedo\TRIAL\Account\Department, NoRedo\TRIAL\Account\Clicker as ClickerAccount;

class Account implements \JsonSerializable {
    
    const USER = 'user';
    const INSTITUTION = 'institution';
    const INSTITUTION_MEMBER = 'institution_member';
    const GOVERNMENT = 'government';
    const GOVERNMENTAL_DEPARTMENT = 'governmental_department';
    
    const ID = 'id';
    const PHOTO_URL = 'photo_url';
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
            default:
                $result = [
                    'message' => Message::ERROR,
                    'error' => '\'type_account\' is missing or unknown. Acceptable values are: Account::USER | Account::INSTITUTION | Account::INSTITUTION_MEMBER | Account::GOVERNMENT | Account::GOVERNMENTAL_DEPARTMENT'
                ];
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
        $id_login = is_numeric($login);
        return Query::helper((new Select(self::con()))->table(TABLE_USERS)->columns([User::ID, User::FIRST_NAME, User::LAST_NAME, User::EMAIL, User::PASSWORD, User::ACTIVATED, User::PERMISSION])->where($id_login ? User::ID . ' = :id' : User::EMAIL . ' = :email')->values($id_login ? [':id' => $login] : [':email' => $login])->fetchMode(PDO::FETCH_CLASS, User::class)->run(), function ($user) use ($password, $permanent) {
            if ($user->existRows()) {
                $account = $user->getResult()[0];
                if ($account->checkPassword($password)) {
                    self::storeAccount($account);
                    self::setAccountLogged($account, $permanent);
                    $result = [self::ID => $account->getId(), 'name' => $account->getFirstName(), User::LAST_NAME => $account->getLastName(), self::PHOTO_URL => $account->getPhotoUrl(), self::PERMISSION => $account->getPermission(), 'account' => self::USER, 'message' => $account->isActivated() ? Message::EXIST : Message::NOT_ACTIVATED];
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
        $id_login = is_numeric($login);
        return Query::helper((new Select(self::con()))->table(Institution::TABLE)->columns([Institution::ID, Institution::CNPJ, Institution::REGISTER, Institution::NAME, self::EMAIL, self::PASSWORD, self::ACTIVATED])->where($id_login ? Institution::ID . ' = :id' : Institution::EMAIL . ' = :email')->values($id_login ? [':id' => $login] : [':email' => $login])->fetchMode(PDO::FETCH_CLASS, Institution::class)->run(), function ($account) use ($password, $permanent) {
            if ($account->existRows()) {
                $account = $account->getResult()[0];
                if ($account->checkPassword($password)) {
                    $result = [Institution::ID => $account->getId(), Institution::REGISTER => $account->getRegister(), 'name' => $account->getName(), Institution::PHOTO_URL => $account->getPhotoUrl(), 'account' => self::INSTITUTION];
                    self::storeAccount($account);
                    self::setAccountLogged($account, $permanent);
                    $result += ['message' => $account->isActivated() ? Message::EXIST : Message::NOT_ACTIVATED];
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
	        self::concludeAuthenticationWeb($account, $permanent);
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
        $id_login = is_numeric($login);
        return Query::helper((new Select(self::con()))->table(Government::TABLE)->columns([Government::ID, Government::CNPJ, Government::REGISTER, Government::NAME, self::EMAIL, self::PASSWORD, self::ACTIVATED])->where($id_login ? Government::ID . ' = :id' : self::EMAIL . ' = :email')->values($id_login ? [':id' => $login] : [':email' => $login])->fetchMode(PDO::FETCH_CLASS, Government::class)->run(), function ($account) use ($login, $password, $permanent) {
            if ($account->existRows()) {
                $account = $account->getResult()[0];
                if ($account->checkPassword($password)) {
                    self::storeAccount($account);
                    self::setAccountLogged($account, $permanent);
                    $result = [Government::ID => $account->getId(), Government::REGISTER => $account->getRegister(), 'name' => $account->getName(), Government::PERMISSION => $account->getPermission(), Government::PHOTO_URL => $account->getPhotoUrl(), 'account' => self::GOVERNMENT, 'message' => $account->isActivated() ? Message::EXIST : Message::NOT_ACTIVATED];
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
        $id_login = is_numeric($login);
        return Query::helper((new Select(self::con()))->table('governmental_departments')->columns('id, government, name, login, password, activated')->where($id_login ? 'id = :id' : 'login = :login')->values($id_login ? [':id' => $login] : [':login' => $login])->fetchMode(PDO::FETCH_CLASS, Department::class)->run(), function ($account) use ($login, $password, $permanent) {
            if ($account->existRows()) {
                $account = $account->getResult()[0];
                if ($account->checkPassword($password)) {
                    self::storeAccount($account);
                    self::setAccountLogged($account, $permanent);
                    $result = ['id' => $account->getGovernmentId(), 'department_id' => $account->getId(), 'name' => $account->getName(), 'permission' => $account->getPermission(), 'photo_url' => $account->getPhotoUrl(), 'account' => self::GOVERNMENTAL_DEPARTMENT, 'message' => $account->isActivated() ? Message::EXIST : Message::NOT_ACTIVATED];
                } else {
                    $result['message'] = Message::ERROR_PASSWORD_INCORRECT;
                }
            }  else {
                $result['message'] = Message::NOT_EXIST;
            }
            return $result;
        });
    }
    
    // 20/06/2017, 23:41:53 => renamed createCookies() to storeAccount()
    private static function storeAccount($account) {
        $domain = $_SERVER['HTTP_HOST'] !== 'localhost' ? '.trialent.com' : 'localhost';
        $trl_accounts = filter_has_var(INPUT_COOKIE, 'trl_accounts') ? json_decode(base64_decode(filter_input(INPUT_COOKIE, 'trl_accounts')), true) : [];
        if (!isset($trl_accounts[$account->getTypeAccount()])) {
            $trl_accounts[$account->getTypeAccount()] = [];
        }
        if (!in_array($account->getId(), $trl_accounts[$account->getTypeAccount()])) {
            $jd_account = json_decode(json_encode($account), true);
            unset($jd_account['permission']);
            unset($jd_account['activated']);
            unset($jd_account['type']);
            $trl_accounts[$account->getTypeAccount()][$account->getId()] = $jd_account;
        }
        setrawcookie('trl_accounts', base64_encode(json_encode($trl_accounts)), strtotime('+30 days'), '/', $domain);
    }
    
    // created on 21/06/2017, 00:07:51
    private static function setAccountLogged($account, bool $permanent = false) {
        $domain = $_SERVER['HTTP_HOST'] !== 'localhost' ? '.trialent.com' : 'localhost';
        setrawcookie('trl_logged', base64_encode($account->getTypeAccount() . ':' . $account->getId()), !$permanent ? 0 : strtotime('+30 days'), '/', $domain);
    }
    
    public static function logout() : array {
        $host = $_SERVER['HTTP_HOST'];
        foreach ($_COOKIE as $k => $v) {
            if ($k !== 'trl_accounts' && substr($k, 0, 4) === 'trl_') {
                if ($host !== 'localhost') {
                    $domain = (strpos($k, '_sr') !== false ? 'serginhocorridaderua' : (strpos($k, '_cl') !== false ? 'clicker' : (strpos($k, '_on') !== false ? 'oportunidadeja' : ''))) . '.trialent.com';
                } else {
                    $domain = $host;
                }
                setcookie($k, null, time() - 3600, '/', $domain);
            }
        }
        return ['message' => Message::SAVED_WITH_SUCCESS];
    }
    
    public static function edit() : array {
        $account = func_get_arg(0); $field = func_get_arg(1);
        if (!($account instanceof Account)) {
            throw new \InvalidArgumentException;
        }
        if (!is_array($field)) {
            throw new \InvalidArgumentException;
        }
        if (isset($field['tmp_name'])) {
            if (is_uploaded_file($field['tmp_name'])) {
                $divided_namespace = explode('\\', get_class($account));
                $url = 'https://trialent.com/images/' . strtolower(array_pop($divided_namespace)) . '/profile/' . $account->getId() . '/' . $account->getId() . '.jpg';
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
        return 'https://trialent.com/images/' . $this->type_account . '/profile/' . $this->id . '/' . $this->id . '.jpg';
    }
    
    // added on 16:22:39
    public function getPermission() {
        return $this->permission;
    }
    
    public function getAllAccounts() : array {
        return Query::helper((new Select(self::con()))->table(DATABASE_USERS . '.' . ($this->type_account === self::USER ? User::TABLE : ($this->type_account === self::INSTITUTION ? Institution::TABLE : Government::TABLE)) . ' AS trial')->columns('CONCAT(\'{"' . ClickerAccount::ID . '": \', clicker.' . ClickerAccount::ID . ', \', "' . ClickerAccount::INSTITUTIONS . '": \', CONCAT(\'[\', GROUP_CONCAT(clicker_i.institution), \']\'), \', "' . ClickerAccount::SIGNED_ON . '": "\', clicker.' . ClickerAccount::SIGNED_ON . ', \'"}\') AS clicker')->leftJoin([DATABASE_CLICKER . '.' . TABLE_USERS . ' AS clicker ON clicker.' . ClickerAccount::USER . ' = trial.' . self::ID, '(SELECT user, CONCAT(\'{"id": \', ui.institution, \', "name": "\', i.name, \'", "type": "\', ui.type, \'", "unique_code": "\', ui.unique_code, \'"}\') AS institution FROM ' . DATABASE_CLICKER . '.users_institutions AS ui LEFT JOIN ' . DATABASE_CLICKER . '.institutions AS i ON ui.institution = i.id) AS clicker_i ON clicker_i.user = clicker.' . ClickerAccount::ID])->where('trial.' . self::ID . ' = :user')->values([':user' => $this->getId()])->run(), function ($accounts) {
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
        $user = User::get($email);
        if ($user != null && $user->getId() != null) {
            $response['user'] = $user;
        }
        $response['message'] = $user != null && $user->getId() != null ?  Message::EXIST : Message::NOT_EXIST;
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
    
    /**
     * @copyright (c) 2017, TRIAL<Matheus Leonardo dos Santos Martins>
     * Created on 21/06/2017, 00:38:21
     */
    public function getTypeAccount() {
        return $this->type_account;
    }
    
    /**
     * Created on 21/06/2017, 16:10:06
     */
    public static function getStoredAccounts() : array {
        return json_decode(base64_decode(filter_input(INPUT_COOKIE, 'trl_accounts')), true) ?? [];
    }
    
    /**
     * Created on 21/06/2017, 16:10:52
     */
    public static function getLoggedAccount() : array {
        if (filter_has_var(INPUT_COOKIE, 'trl_logged')) {
            $trl_logged = explode(':', base64_decode(filter_input(INPUT_COOKIE, 'trl_logged')));
            $trl_accounts = self::getStoredAccounts();
            if (!empty($trl_accounts) && isset($trl_accounts[$trl_logged[0]])) {
                $trl_accounts[$trl_logged[0]][$trl_logged[1]]['type'] = $trl_logged[0];
            }
            return $trl_accounts[$trl_logged[0]][$trl_logged[1]] ?? [];
        }
        return [];
    }

    /**
     * @return type
     * 
     * added on 14/04/2017, 00:27:30
     */
    public function jsonSerialize() {
        return [self::ID => $this->getId(), self::EMAIL => $this->getEmail(), self::PHOTO_URL => $this->getPhotoUrl(), self::PERMISSION => $this->getPermission(), self::ACTIVATED => $this->isActivated(), 'type' => $this->type_account];
    }

}