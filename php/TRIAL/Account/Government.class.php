<?php
/**
 * Description of Government
 * 
 * Created on 24/11/2016, 18:03:23
 * @author Matheus Leonardo dos Santos Martins
 * @copyright (c) 2016, TRIAL
 * 
 * @version 1.01
 * @package Account
 */

/*
 * 16/12/2016
 *      16:26:36 => remove $permission and getPermission()
 *      19:40:15 => added parent::__construct(TRIALAccount::GOVENRMENT); removed namespace, update Connection code
 * 
 * 21/01/2017
 *      01:26:14 => added namespace TRIAL\Account
 *      20:39:43 => renamed namespace from TRIAL\Account to NoRedo\TRIAL\Account
 */
     

namespace NoRedo\TRIAL\Account;

use NoRedo\TRIAL\Account as TRIALAccount, NoRedo\Utils\SQL\Select;

final class Government extends TRIALAccount {
    
    private $name;
    
    public function __construct($search = null) {
        parent::__construct(TRIALAccount::GOVERNMENT);
        $con = Database::connect(DATABASE_USERS);
        if ($search != null) {
            switch (gettype($search)) {
                case 'string':
                    $data = (new Select($con))->table('governments')->columns('id, name, email, password, activated')->where('email = :email')->values([':email' => $search])->run();
                    break;
                case 'integer':
                    $data = (new Select($con))->table('governments')->columns('id, name, email, password, activated')->where('id = :id')->values([':id' => $search])->run();
                    break;
            }
            $data = $data->success() && $data->existRows() ? $data->getResult()[0] : [];
            foreach ($data as $key => $value) {
                $this->{$key} = $value;
            }
        }
    }
     
    public function setName(string $name) {
        $this->name = $name;
    }
     
    public function setPermission(string $permission) {
        $this->permission = $permission;
    }
    
    public function getName() {
        return $this->name;
    }
    
}
