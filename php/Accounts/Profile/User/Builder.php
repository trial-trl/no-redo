<?php
/**
 * Description of Builder
 *
 * Created on 06/12/2016, 23:05:29
 * @author Matheus Leonardo dos Santos Martins
 * @copyright (c) 2016, TRIAL
 * 
 * @package User
 * 
 * @version 1.0
 */

namespace User;

use User;

require_once __DIR__ . '/User.php';

class Builder {
    
    private $values = [];
    
    public function setFirstName($first_name) {
        $this->values['first_name'] = $first_name;
        return $this;
    }
    
    public function setMiddleName($middle_name) {
        $this->values['middle_name'] = $middle_name;
        return $this;
    }
    
    public function setLastName($last_name) {
        $this->values['last_name'] = $last_name;
        return $this;
    }
    
    public function setNickname($nickname) {
        $this->values['nickname'] = $nickname;
        return $this;
    }
    
    public function setRG($rg) {
        $this->values['rg'] = $rg;
        return $this;
    }
    
    public function setCPF($cpf) {
        $this->values['cpf'] = $cpf;
        return $this;
    }
    
    public function setBirthday($birthday) {
        $this->values['birthday'] = date_format(new DateTime($birthday), 'Y-m-d');
        return $this;
    }
    
    public function setSex($sex) {
        switch ($sex) {
            case Sex::MALE:
            case Sex::FEMALE:
            case Sex::UNDEFINED:
                $this->values['sex'] = $sex;
                return $this;
            default:
                throw new InvalidArgumentException('Sex arg must be one of these values: Sex::MALE | Sex::FEMALE | Sex::UNDEFINED');
        }
    }
    
    public function setNationality($nationality) {
        $this->values['nationality'] = $nationality;
        return $this;
    }
    
    public function setCity($city) {
        $this->values['location']['county'] = $city;
        return $this;
    }
    public function setState($state) {
        $this->values['location']['state'] = $state;
        return $this;
    }
    
    public function setPostalCode($postal_code) {
        $this->values['location']['postal_code'] = $postal_code;
        return $this;
    }
    
    public function setLandline($landline) {
        $this->values['landline'] = $landline;
        return $this;
    }
    
    public function setCellPhone($cell_phone) {
        $this->values['cell_phone'] = $cell_phone;
        return $this;
    }
    
    public function setSchoolingLevel($schooling_level) {
        $this->values['schooling_level'] = $schooling_level;
        return $this;
    }
    
    public function setMainOccupation($main_occupation) {
        $this->values['main_occupation'] = $main_occupation;
        return $this;
    }
    
    public function setPermission($permission) {
        $this->values['permission'] = $permission;
        return $this;
    }
    
    public function build() : User {
        return new User($this->values);
    }
    
}
