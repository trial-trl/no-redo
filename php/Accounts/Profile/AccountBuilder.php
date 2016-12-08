<?php
/**
 * Description of AccountBuilder
 *
 * Created on 07/12/2016, 17:03:12
 * @author Matheus Leonardo dos Santos Martins
 * @copyright (c) 2016, TRIAL
 * 
 * @package Profile
 * 
 * @version 1.0
 */
abstract class AccountBuilder {
    
    protected $values = [];
    
    public function setId($id) {
        $this->values['id'] = $id;
        return $this;
    }
    
    public function setEmail($email) {
        $this->values['email'] = $email;
        return $this;
    }
    
    public function setPassword($password) {
        $this->values['password'] = $password;
        return $this;
    }
    
    public function setActivated($activated) {
        $this->values['activated'] = $activated;
        return $this;
    }
    
    public abstract function build();
    
}
