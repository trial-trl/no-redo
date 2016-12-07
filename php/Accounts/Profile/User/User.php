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
 * @version 1.2
 */

/* 
 * 18/10/2016
 *      22:38:18 => setted all vars to private, $con argument is created with no necessity of pass argument
 *      01:13:45 - 01:32:40 => added getPhotoUrl()
 * 
 * 19/10/2016, 19:29:15 - 19:52:57
 *      __construct: 
 *          added $search
 *          removed func__args() use
 *          reorganized and rewritten parts of the code
 *      array $location => fixed errors in getPostalCode(), getCity(), and getState() about null values inside.
 * 
 * 20/10/2016
 *      11:52:22
 *          added:
 *              setFirstName() => substitute of setName()
 *              getFirstName() => substitute of getName()
 *          deprecated:
 *              getName() => use instead getFirstName()
 *          renamed:
 *              $name => $first_name
 *      11:53:05
 *          added:
 *              $middle_name
 *              setMiddleName() => sets a middle name to $middle_name
 *      11:55:28
 *          added:
 *              $nickname
 *              setNickname() => sets a nickname to $nickname
 *      12:00:31
 *          deprecated:
 *              setName() => use instead setFirstName()
 *          renamed:
 *              $phone => $landline
 *              setPhone() => setLandline()
 *              setCellphone() => setCellPhone()
 *              getPhone() => getLandline()
 *              getCellphone() => getCellPhone()
 *          added:
 *              getMiddleName() => gets the middle name inside $middle_name
 *              getNickname() => gets the nickname inside $nickname
 * 
 * 24/11/2016
 *      18:24:28
 *          now this class extends Account. Id, Email, Password, checkPassword(), getPhotoUrl(), and Activate are now handle by Account class, not being need to implement these items here.
 * 
 */

use Account\Base as Account,
        SQL\Select;

require_once __DIR__ . '/../Account.php';
require_once __DIR__ . '/../../../Request.php';

class User extends Account {
    
    protected $first_name;
    protected $middle_name;
    protected $last_name;
    protected $nickname;
    protected $rg;
    protected $cpf;
    protected $birthday;
    protected $sex;
    protected $nationality;
    protected $location = [];
    protected $landline;
    protected $cell_phone;
    protected $schooling_level;
    protected $main_occupation;
    protected $permission;
    
    public function __construct($search = null) {
        parent::__construct();
        $type = gettype($search);
        $con = (new ConnectDB(DB_PREFIX . DATABASE_USERS))->connect();
        if ($type === 'string' || $type === 'integer') {
            switch ($type) {
                case 'string':
                    $data = (new Select($con))->table(TABLE_USERS)->columns('id, first_name, middle_name, last_name, nickname, birthday, sex, rg, cpf, nationality, city, state, postal_code, landline, cell_phone, email, password, activated, permission')->where('email = :email')->values([':email' => $search])->run();
                    break;
                case 'integer':
                    $data = (new Select($con))->table(TABLE_USERS)->columns('id, first_name, middle_name, last_name, nickname, birthday, sex, rg, cpf, nationality, city, state, postal_code, landline, cell_phone, email, password, activated, permission')->where('id = :id')->values([':id' => $search])->run();
                    break;
            }
            $data = $data->success() && $data->existRows() ? $data->getResult()[0] : [];
        } else {
            $data = $search;
        }
        foreach ($data as $key => $value) {
            if ($key == 'postal_code' || $key == 'city' || $key == 'state') {
                $this->location[$key == 'city' ? 'county' : $key] = $value;
            } else {
                $this->{$key} = $value;
            }
        }
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
    
    // added on 21/10/2016, 19:57:06 - 20:00:11
    public function getAge() {
        return (new DateTime($this->birthday))->diff(new DateTime('today'))->y;
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
    
    public function getPermission() {
        return $this->permission;
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