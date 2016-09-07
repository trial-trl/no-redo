<?php
/**
 * Description of NewUser
 *
 * Created on 07/09/2016, ~13:13:30
 * @author Matheus Leonardo dos Santos Martins
 * @copyright (c) 2016, TRIAL
 * 
 * @package SQLUtils
 */

require_once 'User.php';

class NewUser extends User {
    
    public function save($con) {
        return (new Insert($con))->table(TABLE_USERS)->columns('name, last_name, birthday, sex, email, zip, password, ip, register_date_time')->values([$this->getName(), $this->getLastName(), $this->getBirthday(), $this->getSex(), $this->getEmail(), $this->getPostalCode(), $this->getPassword(), '', date('Y-m-d H:i:s')])->run();
    }
    
}