<?php
/**
 * Description of Account
 * 
 * Created on 24/11/2016, 18:05:31
 * @author Matheus Leonardo dos Santos Martins
 * @copyright (c) 2016, TRIAL
 * 
 * @package Profile
 * 
 * @version 1.0
 */

namespace Account;

use Request;

class Base {
    
    private $type_account;
    
    protected $id;
    protected $email;
    protected $password;
    protected $activated;
    
    public function __construct() {
        if ($this instanceof User) {
            $this->type_account = 'user';
        } else if ($this instanceof Institution) {
            $this->type_account = 'institution';
        } else {
            $this->type_account = 'government';
        }
    }
    
    public function setId($id) {
        $this->id = $id;
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
    
    public function getId() {
        return $this->id;
    }
    
    public function getEmail() {
        return $this->email;
    }
    
    public function getPassword() {
        return $this->password;
    }
    
    public function checkPassword($password) {
        return password_verify($password, $this->password) || $this->password === $password;
    }
    
    public function isActivated() {
        return !$this->activated;
    }
    
    public function getPhotoUrl() {
        $url = 'http://www.trialent.com/images/' . $this->type_account . '/profile/' . $this->id . '/' . $this->id . '.jpg';
        $response = Request::make($url);
        return  $response->success() && $response->getResult()['http_code'] === 200 ? $url : '/no-redo/images/TRIAL/logo/icon/social/min/T_icon_social_invert.png';
    }
    
}

class Permission {
    
    const ROOT = 'ROOT';
    const ADMIN = 'ADMIN';
    const NORMAL = 'NORMAL';
    
}