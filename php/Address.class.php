<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Address
 *
 * @copyright (c) 2017, TRIAL
 * @author Matheus Leonardo dos Santos Martins
 * 
 * @version 1.0
 * @package TRIAL
 */
class Address {
    
    private $location;
    
    public function __construct($location) {
        $this->location = $location;
    }
    
    /**
     * @return string Address number
     * @since 1.0
     */
    public function getNumber() : string {
        return isset($this->location->{'district'}) ? $this->location->{'district'} : null;
    }
    
    /**
     * @return string Address complement
     * @since 1.0
     */
    public function getComplement() : string {
        return isset($this->location->{'complement'}) ? $this->location->{'complement'} : null;
    }
    
    /**
     * @return string Address district
     * @since 1.0
     */
    public function getDistrict() : string {
        return isset($this->location->{'district'}) ? $this->location->{'district'} : null;
    }
    
    /**
     * @return string Address city
     * @since 1.0
     */
    public function getCity() : string {
        return isset($this->location->{'county'}) ? $this->location->{'county'} : null;
    }
    
    /**
     * @return string Address state
     * @since 1.0
     */
    public function getState() : string {
        return isset($this->location->{'state'}) ? $this->location->{'state'} : null;
    }
    
    /**
     * @return string Address country
     * @since 1.0
     */
    public function getCountry() : string {
        return isset($this->location->{'country'}) ? $this->location->{'country'} : 'BR';
    }
    
    /**
     * @return int Address postal code
     * @since 1.0
     */
    public function getPostalCode() : int {
        return isset($this->location->{'postal_code'}) ? $this->location->{'postal_code'} : null;
    }
    
}
