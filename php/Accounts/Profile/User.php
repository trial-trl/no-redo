<?php
/**
 * Description of User
 *
 * Created on 06/09/2016, ~15:50:45
 * @author Matheus Leonardo dos Santos Martins
 * @copyright (c) 2016, TRIAL
 * 
 * @package Profile
 */
class User {
    
    public $id;
    public $name;
    public $last_name;
    public $rg;
    public $cpf;
    public $birthday;
    public $sex;
    public $nationality;
    public $location = [];
    public $phone;
    public $cell_phone;
    public $schooling_level;
    public $main_occupation;
    public $email;
    public $password;
    public $activated;
    public $permission;
    
    public function __construct() {
        if (func_num_args() >= 2) {
            $con = func_get_arg(0);
            $search = func_get_arg(1);
            if ($con && $search) {
                if (gettype($search) === 'string') {
                    (new Select($con))->table(TABLE_USERS)->columns('id, name, last_name, email, password, activated, permission')->where('email = :email')->values([':email' => $search])->noResponse()->fetchMode(PDO::FETCH_INTO, $this)->run();
                } else if (gettype($search) === 'integer') {
                    (new Select($con))->table(TABLE_USERS)->columns('name, last_name, email, password, activated, permission')->where('id = :id')->values([':id' => $search])->noResponse()->fetchMode(PDO::FETCH_INTO, $this)->run();
                }
            }
        }
    }
    
    public function setId($id) {
        $this->id = $id;
        return $this;
    }
    
    public function setName($name) {
        $this->name = $name;
        return $this;
    }
    
    public function setLastName($last_name) {
        $this->last_name = $last_name;
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
            case self::MALE:
            case self::FEMALE:
                $this->sex = $sex;
                return $this;
            default:
                throw new InvalidArgumentException('Sex arg must be one of these values: Sex::MALE | Sex::FEMALE');
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
    
    public function setPhone($phone) {
        $this->phone = $phone;
        return $this;
    }
    
    public function setCellphone($cell_phone) {
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
    
    public function getName() {
        return $this->name;
    }
    
    public function getLastName() {
        return $this->last_name;
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
        return $this->location['county'];
    }
    
    public function getState() {
        return $this->location['state'];
    }
    
    public function getPostalCode() {
        return $this->location['postal_code'];
    }
    
    public function getPhone() {
        return $this->phone;
    }
    
    public function getCellphone() {
        return $this->cellphone;
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
    
    private function getPassword() {
        return $this->password;
    }
    
    public function isActivated() {
        return !$this->activated;
    }
    
    public function getPermission() {
        return $this->permission;
    }

}

class Sex {
    
    const MALE = 'M';
    const FEMALE = 'F';
    
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