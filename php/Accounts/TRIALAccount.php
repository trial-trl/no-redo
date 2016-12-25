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
 * 
 * @version 1.2NC
 */

use SQL\Query, SQL\Select, SQL\Insert, SQL\Update, SQL\Delete;

require_once __DIR__ . '/Profile/User/User.php';
require_once __DIR__ . '/Profile/User/Builder.php';
require_once __DIR__ . '/Profile/Institution/Institution.php';
require_once __DIR__ . '/Profile/Institution/Builder.php';
require_once __DIR__ . '/Profile/Department/Department.php';
require_once __DIR__ . '/Profile/Government.php';

TRIALAccount::$con = DB::connect(DATABASE_USERS);

class TRIALAccount {
    
    const USER = 'user';
    const INSTITUTION = 'institution';
    const INSTITUTION_MEMBER = 'institution_member';
    const GOVERNMENT = 'government';
    const GOVERNMENTAL_DEPARTMENT = 'governmental_department';
    
    public static $con;
    private $account;
    
    public function __construct($account = null) {
        if (!self::$con) {
            self::$con = DB::connect(DATABASE_USERS);
        }
        if ($account != null) {
            $this->account = $account;
            if (!($account instanceof User) && !($account instanceof Institution) && !($account instanceof Government) && !($account instanceof Department)) {
                throw new InvalidArgumentException("account argument isn't a instance of User, Institution, Government, or Department class");
            }
        }
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
    
    public static function create($account) : array {
        $is_user = $account instanceof User;
        $is_institution = $account instanceof Institution;
        $is_government = $account instanceof Government;
        $is_governmental_department = $account instanceof Department;
        if (!$is_user && !$is_institution && !$is_government && !$is_governmental_department) {
            throw new InvalidArgumentException("Account isn't a instance of User, Institution, Government, or Department class");
        } else {
            if ($is_user) {
                $query = (new Insert(self::$con))->table(TABLE_USERS)->columns('first_name, last_name, birthday, sex, email, postal_code, password, ip, register_date_time')->values([$account->getName(), $account->getLastName(), $account->getBirthday(), $account->getSex(), $account->getEmail(), $account->getPostalCode(), $account->getPassword(), null, date('Y-m-d H:i:s')]);
            } else if ($is_institution) {
                $query = (new Insert(self::$con))->table(TABLE_INSTITUTIONS)->columns('cnpj, name, infos, email, password, register_date_time')->values([$account->getCNPJ(), $account->getName(), $account->getInfos(), $account->getEmail(), password_hash($account->getPassword(), PASSWORD_DEFAULT), date('Y-m-d H:i:s')]);
            } else if ($is_government) {
                $query = (new Insert(self::$con))->table(TABLE_GOVERNMENTALS)->columns('name, email, password, register_date_time')->values([$account->getName(), $account->getEmail(), password_hash($account->getPassword(), PASSWORD_DEFAULT), date('Y-m-d H:i:s')]);
            } else if ($is_governmental_department) {
                $query = (new Insert(self::$con))->table('governamental_departments')->columns('name, login, password, register_date_time')->values([$account->getName(), $account->getLogin(), password_hash($account->getPassword(), PASSWORD_DEFAULT), date('Y-m-d H:i:s')]);
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
        return Query::helper((new Select(self::$con))->table(TABLE_USERS)->columns('id, first_name, last_name, email, password, activated, permission')->where($id_login ? 'id = :id' : 'email = :email')->values($id_login ? [':id' => $login] : [':email' => $login])->fetchMode(PDO::FETCH_CLASS, 'User')->run(), function ($user) use ($login, $password, $permanent) {
            if ($user->existRows()) {
                $account = $user->getResult()[0];
                if ($account->checkPassword($password)) {
                    $result = ['message' => $account->isActivated() ? Message::EXIST : Message::NOT_ACTIVATED, 'id' => $account->getId(), 'name' => $account->getFirstName(), 'last_name' => $account->getLastName(), 'photo_url' => $account->getPhotoUrl(), 'permission' => $account->getPermission(), 'account' => self::USER];
                    self::concludeAuthenticationWeb($account, $permanent);
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
        return Query::helper((new Select(self::$con))->table(TABLE_INSTITUTIONS)->columns('id, name, email, password, activated')->where($id_login ? 'id = :id' : 'email = :email')->values($id_login ? [':id' => $login] : [':email' => $login])->fetchMode(PDO::FETCH_CLASS, 'Institution')->run(), function ($account) use ($login, $password, $permanent) {
            if ($account->existRows()) {
                $account = $account->getResult()[0];
                if ($account->checkPassword($password)) {
                    $result = ['message' => $account->isActivated() ? Message::EXIST : Message::NOT_ACTIVATED, 'id' => $account->getId(), 'name' => $account->getName(), 'photo_url' => $account->getPhotoUrl(), 'account' => self::INSTITUTION];
                    self::concludeAuthenticationWeb($account, $permanent);
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
        return Query::helper((new Select(self::$con))->table(TABLE_GOVERNMENTS)->columns('id, name, email, password, activated')->where($id_login ? 'id = :id' : 'email = :email')->values($id_login ? [':id' => $login] : [':email' => $login])->fetchMode(PDO::FETCH_CLASS, 'Government')->run(), function ($account) use ($login, $password, $permanent) {
            if ($account->existRows()) {
                $account = $account->getResult()[0];
                if ($account->checkPassword($password)) {
                    $result = ['message' => $account->isActivated() ? Message::EXIST : Message::NOT_ACTIVATED, 'id' => $account->getId(), 'name' => $account->getName(), 'permission' => $account->getPermission(), 'photo_url' => $account->getPhotoUrl(), 'account' => self::GOVERNMENT];
                    self::concludeAuthenticationWeb($account, $permanent);
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
        return Query::helper((new Select(self::$con))->table('governmental_departments')->columns('id, government, name, login, password, activated')->where($id_login ? 'id = :id' : 'login = :login')->values($id_login ? [':id' => $login] : [':login' => $login])->fetchMode(PDO::FETCH_CLASS, 'Department')->run(), function ($account) use ($login, $password, $permanent) {
            if ($account->existRows()) {
                $account = $account->getResult()[0];
                if ($account->checkPassword($password)) {
                    $result = ['message' => $account->isActivated() ? Message::EXIST : Message::NOT_ACTIVATED, 'id' => $account->getGovernmentId(), 'department_id' => $account->getId(), 'name' => $account->getName(), 'permission' => $account->getPermission(), 'photo_url' => $account->getPhotoUrl(), 'account' => self::GOVERNMENTAL_DEPARTMENT];
                    self::concludeAuthenticationWeb($account, $permanent);
                } else {
                    $result['message'] = Message::ERROR_PASSWORD_INCORRECT;
                }
            }  else {
                $result['message'] = Message::NOT_EXIST;
            }
            return $result;
        });
    }
    
    private static function concludeAuthenticationWeb(&$account, bool &$permanent) {
        if ($account instanceof User) {
            self::createCookies([COOKIE_ID_TRIAL, COOKIE_NAME, COOKIE_EMAIL, COOKIE_PERMISSION, COOKIE_TYPE], [$account->getId(), $account->getFirstName(), $account->getEmail(), $account->getPermission(), self::USER], $permanent);
        } else if ($account instanceof Institution) {
            self::createCookies([COOKIE_TI_ID_TRIAL, COOKIE_TI_NAME, COOKIE_TI_EMAIL, COOKIE_TYPE], [$account->getId('id'), $account->getName(), $account->getEmail(), self::INSTITUTION], $permanent);
        } else if ($account instanceof Government) {
            self::createCookies([COOKIE_TG_ID_TRIAL, COOKIE_TG_NAME, COOKIE_TG_EMAIL, COOKIE_TYPE], [$account->getId('id'), $account->getName(), $account->getEmail(), self::GOVERNMENT], $permanent);
        } else if ($account instanceof Department) {
            self::createCookies([COOKIE_TGD_ID_TRIAL, COOKIE_TGD_NAME, COOKIE_PERMISSION, COOKIE_TG_ID_TRIAL, COOKIE_TYPE], [$account->getId(), $account->getName(), $account->getPermission(), $account->getGovernmentId(), self::GOVERNMENTAL_DEPARTMENT], $permanent);
        } else if ($account instanceof TRIALAccount) {
            self::createCookies([COOKIE_ID_TRIAL, COOKIE_NAME, COOKIE_PERMISSION, COOKIE_TI_ID_TRIAL, COOKIE_TI_NAME, COOKIE_TI_EMAIL, COOKIE_TYPE], [$account['member_id'], $account['member_name'], $account['member_permission'], $account['id'], $account['name'], $account['email'], self::INSTITUTION_MEMBER], $permanent);
        }
    }
    
    private static function createCookies(array $name_cookies, array $value_cookies, bool &$permanent) {
        foreach ($name_cookies as $i => $cookie) {
            $domain = $_SERVER['HTTP_HOST'] !== 'localhost' ? '.trialent.com' : 'localhost';
            setcookie($cookie, $value_cookies[$i], !$permanent ? 0 : strtotime('+30 days'), '/', $domain);
        }
    }
    
    public static function logout() : array {
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
        $get = selectDB(self::$con, TABLE_USERS, 'id, name, last_name, birthday, city, state, zip, email, permission', 'WHERE id IN (' . $ids . ')', null);
        $get['message'] = $get != null ? Message::EXIST : Message::NOT_EXIST;
        return $get;
    }
    
    public function getProfilesByPermission($permission) : array {
        $get = selectDB(self::$con, TABLE_USERS, 'id, name, last_name, email, permission', 'WHERE permission = :permission ORDER BY name ASC', [':permission' => $permission]);
        $get['message'] = $get != null ? Message::EXIST : Message::NOT_EXIST;
        return $get;
    }
    
    public function editData($field, $new_value) : array {
        return Query::helper((new Update(self::$con))->table(TABLE_USERS)->columns($field)->where('id = :id')->values([$new_value == '' ? null : $new_value])->valuesWhere([':id' => $this->account->getId()])->run(), function ($query) {
            return ['message' => Message::SAVED_WITH_SUCCESS];
        });
    }
    
    public function changeProfileData($name, $last_name, $email) : array {
        $update = updateDB(self::$con, TABLE_USERS, 'name = :name, last_name = :last_name, email = :email', 'WHERE id = :id', [':name' => $name, ':last_name' => $last_name, ':email' => $email, ':id' => $id]);
        return ['message' => $update ? Message::SAVED_WITH_SUCCESS : Message::ERROR, 'echo_message' => 'Informações de perfil atualizadas!'];
    }
    
    public function changeLocalizationData($city, $state, $zip) : array {
        $update = updateDB(self::$con, TABLE_USERS, 'city = :city, state = :state, zip = :zip', 'WHERE id = :id', [':city' => $city, ':state' => $state, ':zip' => $zip, ':id' => $id]);
        return ['message' => $update ? Message::SAVED_WITH_SUCCESS : Message::ERROR, 'echo_message' => 'Informações de localização atualizadas!'];
    }
    
    public function changePassword($old_password, $new_password) : array {
        $update = updateDB(self::$con, TABLE_USERS, 'password = :password', 'WHERE id = :id AND password = :old_password', [':password' => $new_password, ':id' => $id, ':old_password' => $old_password]);
        return ['message' => $update ? Message::SAVED_WITH_SUCCESS : Message::ERROR, 'echo_message' => 'Senha alterada!'];
    }
    
    public function recoverChangePassword($new_password) : array {
        $update = updateDB(self::$con, TABLE_USERS, 'password', 'id', [password_hash($new_password, PASSWORD_DEFAULT), $user]);
        return ['message' => $update ? Message::SAVED_WITH_SUCCESS : Message::ERROR];
    }
    
    public function activateAccount(bool $activate) : array {
        return Query::helper((new Update($GLOBALS['con']))->table(TABLE_USERS)->columns('activated')->where('id = :id')->values([$activate])->valuesWhere([':id' => $this->account->getId()])->run(), function ($query) {
            return ['message' => Message::SAVED_WITH_SUCCESS];
        });
    }
    
    public function getAllAccounts() : array {
        return Query::helper((new Select(self::$con))->table(DATABASE_USERS . '.' . TABLE_USERS . ' AS trial')->columns('CONCAT(\'{"id": \', clicker.id, \', "type": "\', clicker.type, \'", "register_date_time": "\', clicker.register_date, \' \', clicker.register_time, \'"}\') AS clicker')->leftJoin(DATABASE_CLICKER . '.' . TABLE_USERS . ' AS clicker ON clicker.user = trial.id')->where('trial.id = :user')->values([':user' => $this->account->getId()])->run(), function ($accounts) {
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
        return Query::helper((new Select(self::$con))->table('how_know')->columns('id, how')->run(), function ($query) {
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
    
    public static function createVerificationCode($user) : array {
        $raw_code = uniqid(rand(), true);
        $code = md5($raw_code);
        return Query::helper((new Insert(self::$con))->table('verification_codes')->columns('user, code, register_date_time')->values([$user, $code, date('Y-m-d H:i:s')])->run(), function ($query) use ($raw_code) {
            return ['id' => $query->getResult()['id'], 'code' => $raw_code, 'message' => Message::SAVED_WITH_SUCCESS];
        });
    }
    
    public static function checkVerificationCode($user, $code) : array {
        return Query::helper((new Select(self::$con))->table('verification_codes')->columns('id')->where('user = :user AND code = :code')->values([':user' => $user, ':code' => md5($code)])->run(), function ($query) {
            if ($query->existRows()) {
                $result = $query->getResult()[0];
                $delete = (new Delete(self::$con))->table('verification_codes')->where('id = :id')->values([$result['id']])->run();
            }
            $result['message'] = $query->existRows() ? Message::EXIST : Message::NOT_EXIST;
            return $result;
        });
    }
    
    public function changePhoto($photo) : array {
        $url = '/TRIAL/images/' . strtolower(get_class($this->account)) . '/profile/' . $this->account->getId() . '/' . $this->account->getId() . '.jpg';
        if (move_uploaded_file($photo['tmp_name'], $_SERVER['DOCUMENT_ROOT'] . $url)) {
            return ['message' => Message::SAVED_WITH_SUCCESS, 'url' => $url];
        }
        return ['message' => Message::ERROR, 'error' => ['message' => 'File can\'t be uploaded']];
    }
    
}