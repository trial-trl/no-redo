<?php
/**
 * Description of User
 *
 * Created on 06/09/2016, ~15:50:45
 * @author Matheus Leonardo dos Santos Martins
 * @copyright (c) 2016, TRIAL
 * 
 * @version 1.22
 * @package Account
 */

namespace NoRedo\TRIAL\Account;

use \DateTime, NoRedo\TRIAL\Account as TRIALAccount, NoRedo\Utils\Database, NoRedo\Utils\SQL\Query, NoRedo\Utils\SQL\Select, NoRedo\Address;

final class User extends TRIALAccount implements \JsonSerializable {
    
    const TABLE = 'users';
    
    const FIRST_NAME = 'first_name';
    const MIDDLE_NAME = 'middle_name';
    const LAST_NAME = 'last_name';
    const NICKNAME = 'nickname';
    const BIRTHDATE = 'birthday';
    const SEX = 'sex';
    const RG = 'rg';
    const CPF = 'CPF';
    const CELL_PHONE = 'cell_phone';
    const LANDLINE = 'landline';
    const NATIONALITY = 'nationality';
    
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
    
    protected function __construct($custom = []) {
        parent::__construct(TRIALAccount::USER);
        $loop = empty($custom) ? ['location' => $this->location] : $custom;
        foreach ($loop as $k => $v) {
            $this->{$k} = $k === 'location' ? (new Address(is_array($v) ? $v : json_decode($v, true))) : $v;
        }
    }
    
    /**
     * @param mixed $search
     * @param array $columns
     * @return type
     * 
     * @version 1.0
     * @since 1.22
     */
    public static function get($search, array $columns = [self::ID, self::FIRST_NAME, self::MIDDLE_NAME, self::LAST_NAME, self::NICKNAME, self::BIRTHDATE, self::SEX, self::RG, self::CPF, self::NATIONALITY, 'CONCAT(\'{"' . Address::CITY . '": "\', ' . Address::CITY . ', \'", "' . Address::STATE . '": "\', ' . Address::STATE . ', \'", "' . Address::POSTAL_CODE . '": \', ' . Address::POSTAL_CODE . ', \'}\') AS location', self::LANDLINE, self::CELL_PHONE, self::EMAIL, self::PASSWORD, self::ACTIVATED, self::PERMISSION]) {
        $con = Database::connect(DATABASE_USERS);
        $q = (new Select($con))->table(self::TABLE)->columns($columns)->fetchMode(\PDO::FETCH_CLASS, self::class);
        if (is_string($search)) {
            $q->where(self::EMAIL . ' = :email')->values([':email' => $search]);
        } else if (is_int($search)) {
            $q->where(self::ID . ' = :id')->values([':id' => $search]);
        }
        return Query::helper($q->run(), function ($query) {
            return $query->getResult()[0] ?? null;
        });
    }
    
    /**
     * @param array $copy
     * @return \NoRedo\TRIAL\Account\User
     * 
     * @version 1.0
     * @since 1.22
     */
    public static function copy(array $copy) : User {
        return new self($copy);
    }
    
    /**
     * @return string User first name
     * @since 1.1
     */
    public function getFirstName() : string {
        return $this->first_name ?? '';
    }
    
    /**
     * @return mixed User middle name
     * @since 1.1
     */
    public function getMiddleName() {
        return $this->middle_name ?? '';
    }
    
    /**
     * @return string User last name
     * @since 1.0
     */
    public function getLastName() : string {
        return $this->last_name ?? '';
    }
    
    /**
     * @return string User last name
     * @since 1.212
     */
    public function getFullName() : string {
        return $this->getFirstName() . ($this->getMiddleName() ? ' ' . $this->getMiddleName() : null) . ($this->getLastName() ? ' ' . $this->getLastName() : null);
    }
    
    /**
     * @return mixed User nickname
     * @since 1.1
     */
    public function getNickname() {
        return $this->nickname ?? '';
    }
    
    /**
     * @return mixed User RG
     * @since 1.1
     */
    public function getRG() {
        return $this->rg;
    }
    
    /**
     * @return mixed User CPF
     * @since 1.1
     */
    public function getCPF() {
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
        return $this->sex ?? Sex::UNDEFINED;
    }
    
    /**
     * @return mixed User nationality
     * @since 1.2
     */
    public function getNationality() {
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
     * @return mixed User landline
     * @since 1.1
     */
    public function getLandline() {
        return $this->landline;
    }
    
    /**
     * @return mixed User cell phone
     * @since 1.1
     */
    public function getCellPhone() {
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

    /**
     * 
     * @version 1.01
     * @since 1.2
     */
    public function jsonSerialize() {
    	return array_filter(array_merge([self::FIRST_NAME => $this->getFirstName(), self::MIDDLE_NAME => $this->getMiddleName(), self::LAST_NAME => $this->getLastName(), self::NICKNAME => $this->getNickname()], parent::jsonSerialize()));
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