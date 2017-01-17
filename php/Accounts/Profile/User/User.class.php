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
 * @version 1.21
 */

use Account\Base as Account,
        SQL\Select;

require_once __DIR__ . '/../Account.class.php';
require_once __DIR__ . '/../../../Request.class.php';
require_once __DIR__ . '/../../../Address.class.php';

class User extends Account {
    
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
    
    public function __construct($search = null, array $columns = null) {
        parent::__construct(TRIALAccount::USER);
        if ($columns == null) {
            $columns = 'id, first_name, middle_name, last_name, nickname, birthday, sex, rg, cpf, nationality, CONCAT(\'{"city": "\', city, \'", "state": "\', state, \'", "postal_code": \', postal_code, \', "landline": \', landline, \', "cell_phone": \', cell_phone, \', "email": "\', email, \'", "password": "\', password, \'", "activated": "\', activated, \'", "permission": "\', permission, \'"}\') AS location';
        } else {
            $columns = implode(', ', $columns);
        }
        $type = gettype($search);
        $con = Database::connect(DATABASE_USERS);
        switch ($type) {
            case 'string':
                $data = (new Select($con))->table(TABLE_USERS)->columns($columns)->where('email = :email')->values([':email' => $search])->run();
                break;
            case 'integer':
                $data = (new Select($con))->table(TABLE_USERS)->columns($columns)->where('id = :id')->values([':id' => $search])->run();
                break;
            default:
                $data = $search != null ? $search : [];
        }
        if (gettype($data) === 'object') {
            if ($data->success() && $data->existRows()) {
                $data = $data->getResult()[0];
            }
        }
        foreach ($data as $key => $value) {
            if ($key === 'location') {
                $this->location = new Address(json_decode($value));
            } else {
                $this->{$key} = $value;
            }
        }
    }
    
    /**
     * @return string User first name
     * @since 1.1
     */
    public function getFirstName() : string {
        return $this->first_name;
    }
    
    /**
     * @return string User middle name
     * @since 1.1
     */
    public function getMiddleName() : string {
        return $this->middle_name;
    }
    
    /**
     * @return string User last name
     * @since 1.0
     */
    public function getLastName() : string {
        return $this->last_name;
    }
    
    /**
     * @return string User nickname
     * @since 1.1
     */
    public function getNickname() : string {
        return $this->nickname;
    }
    
    /**
     * @return int User RG
     * @since 1.1
     */
    public function getRG() : int {
        return $this->rg;
    }
    
    /**
     * @return int User CPF
     * @since 1.1
     */
    public function getCPF() : int {
        return $this->cpf;
    }
    
    /**
     * @return DateTime User birthday
     * @since 1.0
     */
    public function getBirthday() : DateTime {
        return new DateTime($this->birthday);
    }
    
    /**
     * @return int User birthday
     * @since 1.2
     */
    public function getAge() : int {
        return $this->getBirthday()->diff(new DateTime('today'))->y;
    }
    
    /**
     * @return string User sex
     * @since 1.0
     */
    public function getSex() : string {
        return $this->sex;
    }
    
    /**
     * @return string User nationality
     * @since 1.2
     */
    public function getNationality() : string {
        return $this->nationality;
    }
    
    /**
     * @return Address User address
     * @since 1.2
     */
    public function getAddress() : Address {
        return $this->location;
    }
    
    /**
     * @return int User landline
     * @since 1.1
     */
    public function getLandline() : int {
        return $this->landline;
    }
    
    /**
     * @return int User cell phone
     * @since 1.1
     */
    public function getCellPhone() : int {
        return $this->cell_phone;
    }
    
    /**
     * @return string User schooling level
     * @since 1.2
     */
    public function getSchoolingLevel() : string {
        return $this->schooling_level;
    }
    
    /**
     * @return string User schooling level
     * @since 1.2
     */
    public function getMainOccupation() : string {
        return $this->main_occupation;
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