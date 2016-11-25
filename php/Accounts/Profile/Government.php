<?php
/**
 * Description of Government
 * 
 * Created on 24/11/2016, 18:03:23
 * @author Matheus Leonardo dos Santos Martins
 * @copyright (c) 2016, TRIAL
 * 
 * @package Profile
 * 
 * @version 1.0
 */

require_once 'Account.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/no-redo/repository/php/Request.php';

class Government extends Account {
    
    private $name;
    private $permission;
    
    public function __construct($search = null) {
        $con = (new ConnectDB(DB_PREFIX . DATABASE_USERS))->connect();
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
    
    public function getPermission() {
        return $this->permission;
    }
    
}
