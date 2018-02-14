<?php    

namespace NoRedo\TRIAL;

use NoRedo\Utils\Database, NoRedo\Utils\SQL\Query, NoRedo\Utils\SQL\Select, NoRedo\Utils\SQL\Insert, NoRedo\Utils\SQL\Update, NoRedo\TRIAL\Account\User, NoRedo\TRIAL\Account\Institution, NoRedo\TRIAL\Account\Government, NoRedo\TRIAL\Account\Clicker as ClickerAccount;

/**
 * Description of Account
 *
 * Created on 05/09/2016, ~20:18:19
 * @author Matheus Leonardo dos Santos Martins <mlsm@trialent.com>
 * @copyright (c) 2016, TRIAL
 * 
 * @version 1.4
 * @package TRIAL
 */
class Account implements \JsonSerializable {
    
    const USER = 'user';
    const INSTITUTION = 'institution';
    const GOVERNMENT = 'government';
    
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
    protected $id;
    protected $email;
    protected $password;
    protected $activated;
    protected $permission;
    
    protected function __construct(string $type) {
        $this->type_account = $type;
        self::$con = Database::connect(DATABASE_USERS);
    }
    
    /**
     * 
     * @param type $login
     * @param string $password
     * @param string $type_account
     * @param bool $permanent
     * @return mixed
     * 
     * @version 1.1
     */
    public static function authenticate($login, string $password, string $type_account = self::USER, bool $permanent = false) {
        if (empty($type_account) || !in_array($type_account, [self::USER, self::INSTITUTION, self::GOVERNMENT])) {
            return false;
        }
	return $type_account === self::USER ? self::authUser($login, $password, $permanent) 
               : ($type_account === self::INSTITUTION ? self::authInstitution($login, $password, $permanent) 
                  : self::authGovernment($login, $password, $permanent));
    }
    
    /**
     * 
     * @param \NoRedo\TRIAL\Account $account
     * @return int Newly created ID account
     * 
     * @version 1.1
     */
    public static function create(Account $account): int {
        if ($account instanceof User) {
            $query = (new Insert(self::$con))->table(User::TABLE)->columns('first_name, last_name, birthday, sex, email, password, ip, register_date_time')->values([$account->getFirstName(), $account->getLastName(), date_format($account->getBirthday(), 'Y-m-d'), $account->getSex(), $account->getEmail(), password_hash($account->getPassword(), PASSWORD_DEFAULT), null, date('Y-m-d H:i:s')]);
        } else if ($account instanceof Institution) {
            $query = (new Insert(self::$con))->table(Institution::TABLE)->columns('cnpj, name, infos, email, password, register_date_time')->values([$account->getRegister()->getCNPJ(), $account->getName(), base64_encode(json_encode($account->getRegister())), $account->getEmail(), password_hash($account->getPassword(), PASSWORD_DEFAULT), date('Y-m-d H:i:s')]);
        } else if ($account instanceof Government) {
            $query = (new Insert(self::$con))->table(Government::TABLE)->columns('name, email, password, register_date_time')->values([$account->getName(), $account->getEmail(), password_hash($account->getPassword(), PASSWORD_DEFAULT), date('Y-m-d H:i:s')]);
        }
        return Query::helper($query->run(), function ($query) {
            return $query->getResult()['id'];
        });
    }
    
    /**
     * 
     * @param mixed $login
     * @param string $password
     * @param bool $permanent
     * @return boolean
     * 
     * @version 1.1
     */
    private static function authUser(&$login, string &$password, bool &$permanent) {
        $user = User::get($login, [User::ID, User::FIRST_NAME, User::LAST_NAME, User::EMAIL, User::PASSWORD, User::ACTIVATED, User::PERMISSION]);
        if ($user != null) {
            if ($user->checkPassword($password)) {
                self::storeAccount($user);
                self::setAccountLogged($user, $permanent);
                return [self::ID => $user->getId(), 'name' => $user->getFirstName(), User::LAST_NAME => $user->getLastName(), self::PHOTO_URL => $user->getPhotoUrl(), self::PERMISSION => $user->getPermission(), 'account' => self::USER];
            }
            return false;
        }
        return null;
    }
    
    /**
     * 
     * @param type $login
     * @param string $password
     * @param bool $permanent
     * @return mixed
     * 
     * @version 1.1
     */
    private static function authInstitution(&$login, string &$password, bool &$permanent) {
        $institution = Institution::get($login, [Institution::ID, Institution::CNPJ, Institution::REGISTER, Institution::NAME, self::EMAIL, self::PASSWORD, self::ACTIVATED]);
        if ($institution != null) {
            if ($institution->checkPassword($password)) {
                self::storeAccount($institution);
                self::setAccountLogged($institution, $permanent);
                return [Institution::ID => $institution->getId(), Institution::REGISTER => $institution->getRegister(), 'name' => $institution->getName(), Institution::PHOTO_URL => $institution->getPhotoUrl(), 'account' => self::INSTITUTION];
            }
            return false;
        }
        return null;
    }
    
    /**
     * 
     * @param type $login
     * @param string $password
     * @param bool $permanent
     * @return mixed
     * 
     * @version 1.1
     */
    private static function authGovernment(&$login, string &$password, bool &$permanent) {
        $gov = Government::get($login, [Government::ID, Government::CNPJ, Government::REGISTER, Government::NAME, self::EMAIL, self::PASSWORD, self::ACTIVATED]);
        if ($gov != null) {
            if ($gov->checkPassword($password)) {
                self::storeAccount($gov);
                self::setAccountLogged($gov, $permanent);
                return [Government::ID => $gov->getId(), Government::REGISTER => $gov->getRegister(), 'name' => $gov->getName(), Government::PERMISSION => $gov->getPermission(), Government::PHOTO_URL => $gov->getPhotoUrl(), 'account' => self::GOVERNMENT];
            }
            return false;
        }
        return null;
    }
    
    /**
     * 
     * @param type $account
     * 
     * @version 1.1
     */
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
    
    /**
     * 
     * @param \NoRedo\TRIAL\Account $account
     * @param bool $permanent
     * 
     * @version 1.1
     */
    private static function setAccountLogged(Account $account, bool $permanent = false) {
        $domain = $_SERVER['HTTP_HOST'] !== 'localhost' ? '.trialent.com' : 'localhost';
        setrawcookie('trl_logged', base64_encode($account->getTypeAccount() . ':' . $account->getId()), !$permanent ? 0 : strtotime('+30 days'), '/', $domain);
    }
    
    /**
     * Logout from TRIAL account
     * 
     * @return bool <b>TRUE</b> if logout succeed, <b>FALSE</b> if not.
     * 
     * @version 1.1
     */
    public static function logout(): bool {
        $host = $_SERVER['HTTP_HOST'];
        $trl_cookies = array_filter(filter_input_array(INPUT_COOKIE), function ($k) {
            return $k !== 'trl_accounts' && substr($k, 0, 4) === 'trl_';
        }, ARRAY_FILTER_USE_KEY);
        foreach ($trl_cookies as $k => $v) {
            setcookie($k, null, time() - 3600, '/', $host !== 'localhost' ? (strpos($k, '_cl') !== false ? 'clicker' : (strpos($k, '_on') !== false ? 'oportunidadeja' : '')) . '.trialent.com' : $host);
        }
        return true;
    }
    
    /**
     * 
     * @return mixed
     * 
     * @version 1.1
     */
    public static function edit(Account $account, array $field) {
        if (!isset($field['tmp_name'])) {
            return Query::helper((new Update(self::$con))->table($account::TABLE)->columns(implode(', ', array_keys($field)))->where(self::ID . ' = :id')->values(array_values($field))->valuesWhere([':id' => $account->getId()])->run(), function () {
                return true;
            });
        }
        if (is_uploaded_file($field['tmp_name'])) {
            $url = 'https://trialent.com/images/' . $account->getTypeAccount() . '/profile/' . $account->getId() . '/' . $account->getId() . '.jpg';
            return move_uploaded_file($field['tmp_name'], $_SERVER['DOCUMENT_ROOT'] . $url) ? $url : false;
        }
        return false;
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
        return 'https://trialent.com/img/' . $this->type_account . '/profile/' . $this->id . '/' . $this->id . '.jpg';
    }
    
    // added on 16:22:39
    public function getPermission() {
        return $this->permission;
    }
    
    /**
     * 
     * @return mixed
     * 
     * @version 1.1
     */
    public function getSubAccounts() {
        return Query::helper((new Select(self::$con))->table(DATABASE_USERS . '.' . ($this->type_account === self::USER ? User::TABLE : ($this->type_account === self::INSTITUTION ? Institution::TABLE : Government::TABLE)) . ' AS trial')->columns('CONCAT(\'{"' . ClickerAccount::ID . '": \', clicker.' . ClickerAccount::ID . ', \', "' . ClickerAccount::INSTITUTIONS . '": \', CONCAT(\'[\', GROUP_CONCAT(clicker_i.institution), \']\'), \', "' . ClickerAccount::SIGNED_ON . '": "\', clicker.' . ClickerAccount::SIGNED_ON . ', \'"}\') AS clicker')->leftJoin([DATABASE_CLICKER . '.' . TABLE_USERS . ' AS clicker ON clicker.' . ClickerAccount::USER . ' = trial.' . self::ID, '(SELECT user, CONCAT(\'{"id": \', ui.institution, \', "name": "\', i.name, \'", "type": "\', ui.type, \'", "unique_code": "\', ui.unique_code, \'"}\') AS institution FROM ' . DATABASE_CLICKER . '.users_institutions AS ui LEFT JOIN ' . DATABASE_CLICKER . '.institutions AS i ON ui.institution = i.id) AS clicker_i ON clicker_i.user = clicker.' . ClickerAccount::ID])->where('trial.' . self::ID . ' = :user')->values([':user' => $this->getId()])->run(), function ($accounts) {
            return $accounts->existRows() ? json_decode(json_encode($accounts->getResult()[0]), true) : [];
        });
    }

    /**
     * 
     * @param string $email
     * @param string $type
     * @return mixed
     * 
     * @version 1.2
     */
    public static function checkEmail(string $email, string $type = self::USER) {
          $account = $type === self::USER ? User::get($email) 
                     : ($type === self::INSTITUTION ? Institution::get($email) 
                        : Government::get($email));
        return $account != null && $account->getId() != null ? $account : false;
    }
    
    /**
     * 
     * @param \NoRedo\TRIAL\Account $account
     * @return mixed
     * 
     * @version 1.2
     * @since 1.1
     */
    public static function createResetPasswordToken(Account $account) {
        $token = self::random_text();
        $table = $account instanceof User ? User::TABLE 
                 : ($account instanceof Institution ? Institution::TABLE 
                    : Government::TABLE);
        return Query::helper((new Update(self::$con))->table($table)->columns('reset_password_token, reset_password_datetime')->where('id = :id')->values([$token, date('Y-m-d H:i:s')])->valuesWhere([':id' => $account->getId()])->run(), function () use ($token) {
            return $token;
        });
    }
    
    /**
     * @author Rokas Šleinius <raveren+gh@gmail.com>
     * @link https://gist.github.com/raveren/5555297 Original GitHub Gist
     */
    private static function random_text( $type = 'alnum', $length = 8 ) {
        switch ( $type ) {
            case 'alnum':
                $pool = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
                break;
            case 'alpha':
                $pool = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
                break;
            case 'hexdec':
                $pool = '0123456789abcdef';
                break;
            case 'numeric':
                $pool = '0123456789';
                break;
            case 'nozero':
                $pool = '123456789';
                break;
            case 'distinct':
                $pool = '2345679ACDEFHJKLMNPRSTUVWXYZ';
                break;
            default:
                $pool = (string) $type;
                break;
        }
        $crypto_rand_secure = function ( $min, $max ) {
            $range = $max - $min;
            if ( $range < 0 ) return $min; // not so random...
            $log    = log( $range, 2 );
            $bytes  = (int) ( $log / 8 ) + 1; // length in bytes
            $bits   = (int) $log + 1; // length in bits
            $filter = (int) ( 1 << $bits ) - 1; // set all lower bits to 1
            do {
                $rnd = hexdec( bin2hex( openssl_random_pseudo_bytes( $bytes ) ) );
                $rnd = $rnd & $filter; // discard irrelevant bits
            } while ( $rnd >= $range );
            return $min + $rnd;
        };
        $token = "";
        $max   = strlen( $pool );
        for ( $i = 0; $i < $length; $i++ ) {
            $token .= $pool[$crypto_rand_secure( 0, $max )];
        }
        return $token;
    }
    
    /**
     * 
     * @param \NoRedo\TRIAL\Account $account
     * @param string $token
     * @return bool
     * 
     * @version 1.2
     * @since 1.1
     */
    public static function checkResetPasswordToken(Account $account, string $token) : bool {
        $table = $account instanceof User ? User::TABLE 
                 : ($account instanceof Institution ? Institution::TABLE 
                    : Government::TABLE);
        return Query::helper((new Select(self::$con))->table($table)->columns('reset_password_token, reset_password_datetime')->where('id = :id AND reset_password_token = :reset_password_token')->values([':id' => $account->getId(), ':reset_password_token' => $token])->run(), function ($query) use ($account, $table) {
            if ($query->existRows()) {
                (new Update(self::$con))->table($table)->columns('reset_password_token')->where('id = :id')->values([null])->valuesWhere([':id' => $account->getId()])->run();
                return true;
            }
            return false;
        });
    }
    
    /**
     * 
     * @return string
     * 
     * @version 1.1
     */
    public function getTypeAccount() : string {
        return $this->type_account;
    }
    
    /**
     * 
     * @return array
     * 
     * @version 1.1
     */
    public static function getStoredAccounts() : array {
        return json_decode(base64_decode(filter_input(INPUT_COOKIE, 'trl_accounts')), true) ?? [];
    }
    
    /**
     * 
     * @return array
     * 
     * @version 1.1
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
     * @return mixed
     * 
     * @version 1.1
     */
    public function jsonSerialize() {
        return [self::ID => $this->getId(), self::EMAIL => $this->getEmail(), self::PHOTO_URL => $this->getPhotoUrl(), self::PERMISSION => $this->getPermission(), self::ACTIVATED => $this->isActivated(), 'type' => $this->getTypeAccount()];
    }

}