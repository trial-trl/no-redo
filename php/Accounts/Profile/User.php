<?php
/**
 * Description of User
 *
 * Created on 06/09/2016, ~15:50:45
 * @author Matheus Leonardo dos Santos Martins
 * @copyright (c) 2016, TRIAL
 * 
 * @package Profile
 * 
 * @version 1.1
 */

require_once __DIR__ . '/../../Request.php';

class User {
    
    private $id;
    private $first_name;
    private $middle_name;
    private $last_name;
    private $nickname;
    private $rg;
    private $cpf;
    private $birthday;
    private $sex;
    private $nationality;
    private $location = [];
    private $landline;
    private $cell_phone;
    private $schooling_level;
    private $main_occupation;
    private $email;
    private $password;
    private $activated;
    private $permission;
    
    public function __construct($search = null) {
        $con = (new ConnectDB(DB_PREFIX . DATABASE_USERS))->connect();
        if ($search != null) {
            switch (gettype($search)) {
                case 'string':
                    $data = (new Select($con))->table(TABLE_USERS)->columns('id, first_name, last_name, email, password, activated, permission')->where('email = :email')->values([':email' => $search])->run();
                    break;
                case 'integer':
                    $data = (new Select($con))->table(TABLE_USERS)->columns('id, first_name, last_name, email, password, activated, permission')->where('id = :id')->values([':id' => $search])->run();
                    break;
            }
            $data = $data->success() && $data->existRows() ? $data->getResult()[0] : [];
            foreach ($data as $key => $value) {
                $this->{$key} = $value;
            }
        }
    }
    
    public function setId($id) {
        $this->id = $id;
        return $this;
    }
    
    /**
     * 
     * @deprecated since version 1.1
     */
    public function setName($name) {
        $this->first_name = $name;
        return $this;
    }
    
    public function setFirstName($first_name) {
        $this->first_name = $first_name;
        return $this;
    }
    
    public function setMiddleName($middle_name) {
        $this->middle_name = $middle_name;
        return $this;
    }
    
    public function setLastName($last_name) {
        $this->last_name = $last_name;
        return $this;
    }
    
    public function setNickname($nickname) {
        $this->nickname = $nickname;
        return $this;
    }
    
    public function setRG($rg) {
        $this->rg = $rg;
        return $this;
    }
    
    public function setCPF($cpf) {
        $this->cpf = $cpf;
        return $this;
    }
    
    public function setBirthday($birthday) {
        $this->birthday = date_format(new DateTime($birthday), 'Y-m-d');
        return $this;
    }
    
    public function setSex($sex) {
        switch ($sex) {
            case Sex::MALE:
            case Sex::FEMALE:
            case Sex::UNDEFINED:
                $this->sex = $sex;
                return $this;
            default:
                throw new InvalidArgumentException('Sex arg must be one of these values: Sex::MALE | Sex::FEMALE | Sex::UNDEFINED');
        }
    }
    
    public function setNationality($nationality) {
        $this->nationality = $nationality;
        return $this;
    }
    
    public function setCity($city) {
        $this->location['county'] = $city;
        return $this;
    }
    public function setState($state) {
        $this->location['state'] = $state;
        return $this;
    }
    
    public function setPostalCode($postal_code) {
        $this->location['postal_code'] = $postal_code;
        return $this;
    }
    
    public function setLandline($landline) {
        $this->landline = $landline;
        return $this;
    }
    
    public function setCellPhone($cell_phone) {
        $this->cell_phone = $cell_phone;
        return $this;
    }
    
    public function setSchoolingLevel($schooling_level) {
        $this->schooling_level = $schooling_level;
        return $this;
    }
    
    public function setMainOccupation($main_occupation) {
        $this->main_occupation = $main_occupation;
        return $this;
    }
    
    public function setEmail($email) {
        $this->email = $email;
        return $this;
    }
    
    public function setPassword($password) {
        $this->password = $password;
        return $this;
    }
    
    public function setActivated($activated) {
        $this->activated = $activated;
        return $this;
    }
    
    public function setPermission($permission) {
        $this->permission = $permission;
        return $this;
    }
    
    public function checkPassword($password) {
        return password_verify($password, $this->password) || $this->password === $password;
    }
    
    public function getId() {
        return $this->id;
    }
    
    /**
     * 
     * @deprecated since version 1.1
     */
    public function getName() {
        return $this->first_name;
    }
    
    public function getFirstName() {
        return $this->first_name;
    }
    
    public function getMiddleName() {
        return $this->middle_name;
    }
    
    public function getLastName() {
        return $this->last_name;
    }
    
    public function getNickname() {
        return $this->nickname;
    }
    
    public function getRG() {
        return $this->rg;
    }
    
    public function getCPF() {
        return $this->cpf;
    }
    
    public function getBirthday() {
        return $this->birthday;
    }
    
    public function getSex() {
        return $this->sex;
    }
    
    public function getNationality() {
        return $this->nationality;
    }
    
    public function getCity() {
        return isset($this->location['county']) ? $this->location['county'] : null;
    }
    
    public function getState() {
        return isset($this->location['state']) ? $this->location['state'] : null;
    }
    
    public function getPostalCode() {
        return isset($this->location['postal_code']) ? $this->location['postal_code'] : null;
    }
    
    public function getLandline() {
        return $this->landline;
    }
    
    public function getCellPhone() {
        return $this->cell_phone;
    }
    
    public function getSchoolingLevel() {
        return $this->schooling_level;
    }
    
    public function getMainOccupation() {
        return $this->main_occupation;
    }
    
    public function getEmail() {
        return $this->email;
    }
    
    public function getPassword() {
        return $this->password;
    }
    
    public function isActivated() {
        return !$this->activated;
    }
    
    public function getPermission() {
        return $this->permission;
    }
    
    public function getPhotoUrl() {
        $url = 'http://www.trialent.com/images/user/profile/' . $this->id . '/' . $this->id . '.jpg';
        $response = Request::make($url);
        return  $response->success() && $response->getResult()['http_code'] === 200 ? $url : '/no-redo/images/TRIAL/logo/icon/social/min/T_icon_social_invert.png';
    }

}

class Sex {
    
    const MALE = 'M';
    const FEMALE = 'F';
    const UNDEFINED = 'U';
    
}

class SchoolingLevel {
    
    const E = 'E';
    
}

class MainOccupation {
    
    const ENTREPRENEUR = 'ENTREPRENEUR';
    const BUSSINESSMAN = 'BUSSINESSMAN';
    
}

class Permission {
    
    const ROOT = 'ROOT';
    const ADMIN = 'ADMIN';
    const NORMAL = 'NORMAL';
    
}