<?php
/**
 * Description of TRIALAccount
 * 
 * Last edition: 07/09/2016, 18:30:46
 *
 * Created on 05/09/2016, ~20:18:19
 * @author Matheus Leonardo dos Santos Martins
 * @copyright (c) 2016, TRIAL
 * 
 * @package Accounts
 */

/*
 * 06/12/2016, 23:37:02:
 *      added namespace TRIAL
 *      renamed TRIALAccount to Account
 */

namespace TRIAL;

use ConnectDB, SQL\Query, SQL\Select, SQL\Insert, SQL\Update, SQL\Delete;
use User, Institution, Government;
use Message, InvalidArgumentException;

require_once 'Profile/User/User.php';
require_once 'Profile/User/Builder.php';
require_once 'Profile/Institution.php';
require_once 'Profile/Government.php';

class Account {
    
    // Added on 10/09/2016, 15:32:01
    const USER = 'user';
    
    // Added on 10/09/2016, 15:31:26
    const INSTITUTION = 'institution';
    
    // Added on 10/09/2016, 15:58:25
    const INSTITUTION_MEMBER = 'institution_member';
    
    // Added on 10/09/2016, 15:32:26
    const GOVERNMENT = 'government';
    
    private $con;
    
    /* 18/10/2016
     *      01:35:11 => added return of photo_url in userAuth()
     *      01:36:08 => added return of photo_url in institutionAuth()
     * 
     * 19/10/2016:
     *      16:58:07
     *          __construct: now have account argument that takes an Account instance of type User or Institution to manage it;
     *          added $account variable 
     *      19:25:25
     *          getAllAccounts() now uses private $account instead of $user argument
     * 
     * 20/10/2016:
     *      00:22:11 => getAllAccounts() clicker_ columns transformed to JSON
     *      01:46:33 - 02:06:25
     *          authenticateUser():
     *              arguments now have defined types;
     *              argument $email changed to $login, allowing the authentication by id or email;
     *              userAuth(), institutionAuth(), institutionMemberAuth(), and governmentAuth() now references his arguments and they assign to private $account their authentication results.
     *          renamed:
     *              makePermanentLogin() => createCookies()
     *      11:34:58 => removed getProfile()
     *      14:45:54
     *          createTRIALAccount() => now takes only the $account argument, that must be an instance of User, Institution, or Government class. With this change, all data stay inside one of these classes, and allows createTRIALAccount() to create all TRIAL Account types
     *          removed:
     *              createInstitutionalTRIALAccount()
     *              createGovernmentalTRIALAccount()
     *      15:13:15
     *          removed => accountIsActivated($id_account)
     *      15:27:07
     *          all methods, except concludeAuthenticationWeb() and createCookies(), now have array return type
     *          added:
     *              buildResponse($query, $callback) => helps the construction of return data
     *      15:38:34
     *          all methods that make requests to DB now have the implementation of buildRequest().
     *      17:43:14
     *          class NewUser was deleted for being useless to TRIAL Account structure
     *      18:08:24
     *          changeProfileData(), changeLocalizationData(), changePassword(), recoverChangePassword() => removed $id/$user argument and now takes this argument from $account
     *      18:12:49
     *          checkEmail() => now returns the entire User class instead id only
     * 
     * 22/11/2016, 20:11:48 => removed buildResponse() and replaced for Query::helper()
     * 
     * 24/11/2016, 23:51:28 => now all authenticateUser() sub-methods returns account type. It's given by 'account' alias.
     * 
     * 06/12/2016
     *      21:14:24 => added changePhoto($photo)
     *      21:17:20 => added Government account instance verification in __construct($account = null) and createTRIALAccount($account)
     *      22:29:28 => createTRIALAccount():
     *          renamed to createAccount()
     *          turned to static
     */
    
    private $account;
    
    public function __construct($account = null) {
        $this->con = (new ConnectDB(DB_PREFIX . DATABASE_USERS))->connect();
        if ($account != null) {
            $this->account = $account;
            if (!($account instanceof User) && !($account instanceof Institution) && !($account instanceof Government)) {
                throw new InvalidArgumentException("account argument isn't a instance of User, Institution, or Government class");
            }
        }
    }
    
    public static function createAccount($account) : array {
        $is_user = $account instanceof User;
        $is_institution = $account instanceof Institution;
        $is_government = $account instanceof Government;
        if (!$is_user && !$is_institution && !$is_government) {
            throw new InvalidArgumentException("Account isn't a instance of User, Institution, or Government class");
        } else {
            if ($is_user) {
                $query = (new Insert($this->con))->table(TABLE_USERS)->columns('first_name, last_name, birthday, sex, email, postal_code, password, ip, register_date_time')->values([$account->getName(), $account->getLastName(), $account->getBirthday(), $account->getSex(), $account->getEmail(), $account->getPostalCode(), $account->getPassword(), null, date('Y-m-d H:i:s')]);
            } else if ($is_institution) {
                $query = (new Insert($this->con))->table(TABLE_INSTITUTIONS)->columns('cnpj, name, infos, email, password, register_date_time')->values([$account->getCNPJ(), $account->getName(), $account->getInfos(), $account->getEmail(), password_hash($account->getPassword(), PASSWORD_DEFAULT), date('Y-m-d H:i:s')]);
            } else if ($is_government) {
                $query = (new Insert($this->con))->table(TABLE_GOVERNMENTALS)->columns('name, email, password, register_date_time')->values([$account->getName(), $account->getEmail(), password_hash($account->getPassword(), PASSWORD_DEFAULT), date('Y-m-d H:i:s')]);
            }
            return Query::helper($query->run(), function ($query) {
                $result = $query->getResult();
                $result['message'] = Message::SAVED_WITH_SUCCESS;
                return $result;
            });
        }
    }
    
    private function userAuth(&$login, string &$password, bool &$permanent) : array {
        $id_login = gettype($login) === 'integer';
        return Query::helper((new Select($this->con))->table(TABLE_USERS)->columns('id, first_name, last_name, email, password, activated, permission')->where($id_login ? 'id = :id' : 'email = :email')->values($id_login ? [':id' => $login] : [':email' => $login])->fetchMode(PDO::FETCH_CLASS, 'User')->run(), function ($user) use ($login, $password, $permanent) {
            if ($user->existRows()) {
                $this->account = $user->getResult()[0];
                if ($this->account->checkPassword($password)) {
                    $result = ['message' => $this->account->isActivated() ? Message::EXIST : Message::NOT_ACTIVATED, 'id' => $this->account->getId(), 'name' => $this->account->getName(), 'last_name' => $this->account->getLastName(), 'photo_url' => $this->account->getPhotoUrl(), 'permission' => $this->account->getPermission(), 'account' => TRIALAccount::USER];
                    $this->concludeAuthenticationWeb($permanent);
                } else {
                    $result['message'] = Message::ERROR_PASSWORD_INCORRECT;
                }
            } else {
            	$result['message'] = Message::NOT_EXIST;
            }
            return $result;
        });
    }
    
    private function institutionAuth(&$login, string &$password, bool &$permanent) : array {
        $id_login = gettype($login) === 'integer';
        return Query::helper((new Select($this->con))->table(TABLE_INSTITUTIONS)->columns('id, name, email, password, activated')->where($id_login ? 'id = :id' : 'email = :email')->values($id_login ? [':id' => $login] : [':email' => $login])->fetchMode(PDO::FETCH_CLASS, 'Institution')->run(), function ($account) use ($login, $password, $permanent) {
            if ($account->existRows()) {
                $this->account = $account->getResult()[0];
                if ($this->account->checkPassword($password)) {
                    $result = ['message' => $this->account->isActivated() ? Message::EXIST : Message::NOT_ACTIVATED, 'id' => $this->account->getId(), 'name' => $this->account->getName(), 'photo_url' => $this->account->getPhotoUrl(), 'account' => TRIALAccount::INSTITUTION];
                    $this->concludeAuthenticationWeb($permanent);
                } else {
                    $result['message'] = Message::ERROR_PASSWORD_INCORRECT;
                }
            }  else {
                $result['message'] = Message::NOT_EXIST;
            }
            return $result;
        });
    }
    
    private function institutionMemberAuth(&$login, string &$password, bool &$permanent) : array {
        $account = selectDB($this->con, 'users AS u', 'u.id AS member_id, u.name AS member_name, u.password, u.permission AS member_permission, IF(COUNT(i.id) > 0, true, false) AS have_institution, COUNT(i.id) AS total_institutions, GROUP_CONCAT(i.id SEPARATOR \', \') AS id, GROUP_CONCAT(i.name SEPARATOR \', \') AS name, GROUP_CONCAT(i.email SEPARATOR \', \') AS email, GROUP_CONCAT(i.activated SEPARATOR \', \') AS activated', 'LEFT JOIN institutions_members AS im ON im.user = u.id LEFT JOIN institutions AS i ON i.id = im.institution WHERE u.email = :email', [':email' => $login]);
        if ($account != null) {
            $account = $account[0];
            if (password_verify($password, $account['password']) ? true : $password === $account['password']) {
	        $result = ['message' => $account['have_institution'] ? Message::EXIST : Message::MEMBER_WITHOUT_INSTITUTION, 'institutions' => ['have_institutions' => $account['have_institution']], 'member' => ['id' => $account['member_id'], 'name' => $account['member_name'], 'permission' => $account['member_permission']]];
	        $this->concludeAuthenticationWeb($permanent);
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
    
    private function governmentAuth(&$login, string &$password, bool &$permanent) : array {
        $id_login = gettype($login) === 'integer';
        return Query::helper((new Select($this->con))->table(TABLE_GOVERNMENTS)->columns('id, name, email, password, activated')->where($id_login ? 'id = :id' : 'email = :email')->values($id_login ? [':id' => $login] : [':email' => $login])->fetchMode(PDO::FETCH_CLASS, 'Government')->run(), function ($account) use ($login, $password, $permanent) {
            if ($account->existRows()) {
                $this->account = $account->getResult()[0];
                if ($this->account->checkPassword($password)) {
                    $result = ['message' => $this->account->isActivated() ? Message::EXIST : Message::NOT_ACTIVATED, 'id' => $this->account->getId(), 'name' => $this->account->getName(), 'permission' => $this->account->getPermission(), 'photo_url' => $this->account->getPhotoUrl(), 'account' => TRIALAccount::GOVERNMENT];
                    $this->concludeAuthenticationWeb($permanent);
                } else {
                    $result['message'] = Message::ERROR_PASSWORD_INCORRECT;
                }
            }  else {
                $result['message'] = Message::NOT_EXIST;
            }
            return $result;
        });
    }
    
    public function authenticateUser($login, string $password, $type_account = self::USER, bool $permanent = false) : array {
        switch ($type_account) {
            case self::USER:
                $result = $this->userAuth($login, $password, $permanent);
                break;
            case self::INSTITUTION:
                $result = $this->institutionAuth($login, $password, $permanent);
                break;
            case self::INSTITUTION_MEMBER:
                $result = $this->institutionMemberAuth($login, $password, $permanent);
                break;
            case self::GOVERNMENT:
                $result = $this->governmentAuth($login, $password, $permanent);
                break;
        }
	return $result;
    }
    
    private function concludeAuthenticationWeb(bool &$permanent) {
        if ($this->account instanceof User) {
            $this->createCookies([COOKIE_ID_TRIAL, COOKIE_NAME, COOKIE_EMAIL, COOKIE_PERMISSION, COOKIE_TYPE], [$this->account->getId(), $this->account->getName(), $this->account->getEmail(), $this->account->getPermission(), self::USER], $permanent);
        } else if ($this->account instanceof Institution) {
            $this->createCookies([COOKIE_TI_ID_TRIAL, COOKIE_TI_NAME, COOKIE_TI_EMAIL, COOKIE_TYPE], [$this->account->getId('id'), $this->account->getName(), $this->account->getEmail(), self::INSTITUTION], $permanent);
        } else if ($this->account instanceof Government) {
            $this->createCookies([COOKIE_TG_ID_TRIAL, COOKIE_TG_NAME, COOKIE_TG_EMAIL, COOKIE_TYPE], [$this->account->getId('id'), $this->account->getName(), $this->account->getEmail(), self::GOVERNMENT], $permanent);
        } else if ($this->account instanceof TRIALAccount) {
            $this->createCookies([COOKIE_ID_TRIAL, COOKIE_NAME, COOKIE_PERMISSION, COOKIE_TI_ID_TRIAL, COOKIE_TI_NAME, COOKIE_TI_EMAIL, COOKIE_TYPE], [$account['member_id'], $account['member_name'], $account['member_permission'], $account['id'], $account['name'], $account['email'], self::INSTITUTION_MEMBER], $permanent);
        }
    }
    
    private function createCookies(array $name_cookies, array $value_cookies, bool &$permanent) {
        foreach ($name_cookies as $i => $cookie) {
            $domain = $_SERVER['HTTP_HOST'] !== 'localhost' ? '.trialent.com' : 'localhost';
            setcookie($cookie, $value_cookies[$i], !$permanent ? 0 : strtotime('+30 days'), '/', $domain);
        }
    }
    
    public function logout() : array {
        $result = [];
        $i = 0;
        $host = $_SERVER['HTTP_HOST'];
        foreach ($_COOKIE as $key) {
            if (strpos($key, 'TRL_') !== false) {
                if ($host !== 'localhost') {
                    if (strpos($key, 'SR') !== false) {
                        $domain = 'serginho.trialent.com';
                    } else if (strpos($key, 'CL') !== false) {
                        $domain = 'clicker.trialent.com';
                    } else if (strpos($key, 'ON') !== false) {
                        $domain = 'oportunidadeja.trialent.com';
                    } else {
                        $domain = '.trialent.com';
                    }
                } else {
                    $domain = $host;
                }
                setcookie($key, null, time() - 3600, '/', $domain);
                $result[$i++] = ['Key' => $key, 'Domain' => $domain, 'Status' => 'Deleted'];
            }
            $result['message'] = Message::SAVED_WITH_SUCCESS;
        }
        return $result;
    }
    
    public function getProfiles($ids) : array {
        $get = selectDB($this->con, TABLE_USERS, 'id, name, last_name, birthday, city, state, zip, email, permission', 'WHERE id IN (' . $ids . ')', null);
        $get['message'] = $get != null ? Message::EXIST : Message::NOT_EXIST;
        return $get;
    }
    
    public function getProfilesByPermission($permission) : array {
        $get = selectDB($this->con, TABLE_USERS, 'id, name, last_name, email, permission', 'WHERE permission = :permission ORDER BY name ASC', [':permission' => $permission]);
        $get['message'] = $get != null ? Message::EXIST : Message::NOT_EXIST;
        return $get;
    }
    
    public function editData($field, $new_value) : array {
        return Query::helper((new Update($this->con))->table(TABLE_USERS)->columns($field)->where('id = :id')->values([$new_value == '' ? null : $new_value])->valuesWhere([':id' => $this->account->getId()])->run(), function ($query) {
            return ['message' => Message::SAVED_WITH_SUCCESS];
        });
    }
    
    public function changeProfileData($name, $last_name, $email) : array {
        $update = updateDB($this->con, TABLE_USERS, 'name = :name, last_name = :last_name, email = :email', 'WHERE id = :id', [':name' => $name, ':last_name' => $last_name, ':email' => $email, ':id' => $id]);
        return ['message' => $update ? Message::SAVED_WITH_SUCCESS : Message::ERROR, 'echo_message' => 'Informações de perfil atualizadas!'];
    }
    
    public function changeLocalizationData($city, $state, $zip) : array {
        $update = updateDB($this->con, TABLE_USERS, 'city = :city, state = :state, zip = :zip', 'WHERE id = :id', [':city' => $city, ':state' => $state, ':zip' => $zip, ':id' => $id]);
        return ['message' => $update ? Message::SAVED_WITH_SUCCESS : Message::ERROR, 'echo_message' => 'Informações de localização atualizadas!'];
    }
    
    public function changePassword($old_password, $new_password) : array {
        $update = updateDB($this->con, TABLE_USERS, 'password = :password', 'WHERE id = :id AND password = :old_password', [':password' => $new_password, ':id' => $id, ':old_password' => $old_password]);
        return ['message' => $update ? Message::SAVED_WITH_SUCCESS : Message::ERROR, 'echo_message' => 'Senha alterada!'];
    }
    
    public function recoverChangePassword($new_password) : array {
        $update = updateDB($this->con, TABLE_USERS, 'password', 'id', [password_hash($new_password, PASSWORD_DEFAULT), $user]);
        return ['message' => $update ? Message::SAVED_WITH_SUCCESS : Message::ERROR];
    }
    
    public function activateAccount(bool $activate) : array {
        return Query::helper((new Update($GLOBALS['con']))->table(TABLE_USERS)->columns('activated')->where('id = :id')->values([$activate])->valuesWhere([':id' => $this->account->getId()])->run(), function ($query) {
            return ['message' => Message::SAVED_WITH_SUCCESS];
        });
    }
    
    public function getAllAccounts() : array {
        return Query::helper((new Select($this->con))->table(DB_PREFIX . DATABASE_USERS . '.' . TABLE_USERS . ' AS trial')->columns('CONCAT(\'{"id": \', clicker.id, \', "type": "\', clicker.type, \'", "register_date_time": "\', clicker.register_date, \' \', clicker.register_time, \'"}\') AS clicker')->leftJoin(DB_PREFIX . DATABASE_CLICKER . '.' . TABLE_USERS . ' AS clicker ON clicker.user = trial.id')->where('trial.id = :user')->values([':user' => $this->account->getId()])->run(), function ($accounts) {
            if ($accounts->existRows()) {
                $result = $accounts->getResult()[0];
                $result['message'] = Message::EXIST;
            } else {
                $result = ['message' => Message::NOT_EXIST];
            }
            return $result;
        });
    }

    public function getHowKnowRegisters() : array {
        return Query::helper((new Select($this->con))->table('how_know')->columns('id, how')->run(), function ($query) {
            $result['message'] = $query->existRows() ?  Message::EXIST : Message::NOT_EXIST;
            return $result;
        });
    }
    
    public function checkEmail(string $email) : array {
        $user = new User($email);
        if ($user->getId() != null) {
            $response['user'] = $user;
        }
        $response['message'] = $user->getId() != null ?  Message::EXIST : Message::NOT_EXIST;
        return $response;
    }
    
    public function createVerificationCode($user) : array {
        $raw_code = uniqid(rand(), true);
        $code = md5($raw_code);
        return [insertDB($this->con, 'verification_codes', 'user, code, register_date, register_time', [$user, $code, date('Y-m-d'), date('H:i:s')]), 'message' => Message::SAVED_WITH_SUCCESS, 'code' => $raw_code];
    }
    
    public function checkVerificationCode($id, $code) : array {
        $check = selectDB($this->con, 'verification_codes', 'user', 'WHERE id = :id AND code = :code', [':id' => $id, ':code' => md5($code)]);
        if ($check != null) {
            updateDB($this->con, 'verification_codes', 'verificated', 'id', [true, $id]);
        }
        $result = $check != null ? $check[0] : $check;
        $result['message'] = $check != null ? Message::EXIST : Message::NOT_EXIST;
        return $result;
    }
    
    public function changePhoto($photo) : array {
        if (move_uploaded_file($photo['tmp_name'], '/TRIAL/images/' . get_class($this->account) . '/' . $this->account->getId() . '/' . $this->account->getId() . '.jpg')) {
            return json_encode(['message' => Message::SAVED_WITH_SUCCESS]);
        }
        return json_encode(['message' => Message::ERROR, 'error' => ['message' => 'File can\'t be uploaded']]);
    }
    
}