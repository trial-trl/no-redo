<?php

/*
 * (c) 2017 TRIAL.
 * Created on 17/01/2017, 15:56:35.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *      http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
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

/*
 * 20/01/2017
 *      02:14:22 => added constants: NUMBER, COMPLEMENT, DISTRICT, CITY, STATE, COUNTRY, POSTAL_CODE
 * 
 * 21/01/2017
 *      21:04:01 => added namespace NoRedo
 */

namespace NoRedo;

class Address {
    
    const NUMBER = 'number';
    const COMPLEMENT = 'complement';
    const DISTRICT = 'district';
    const CITY = 'city';
    const STATE = 'state';
    const COUNTRY = 'country';
    const POSTAL_CODE = 'postal_code';
    
    private $location;
    
    public function __construct($location) {
        $this->location = $location;
    }
    
    /**
     * @return mixed Address number
     * @since 1.0
     */
    public function getNumber() {
        return isset($this->location->{'number'}) ? $this->location->{'number'} : null;
    }
    
    /**
     * @return mixed Address complement
     * @since 1.0
     */
    public function getComplement() {
        return isset($this->location->{'complement'}) ? $this->location->{'complement'} : null;
    }
    
    /**
     * @return mixed Address district
     * @since 1.0
     */
    public function getDistrict() {
        return isset($this->location->{'district'}) ? $this->location->{'district'} : null;
    }
    
    /**
     * @return mixed Address city
     * @since 1.0
     */
    public function getCity() {
        return isset($this->location->{'county'}) ? $this->location->{'county'} : null;
    }
    
    /**
     * @return mixed Address state
     * @since 1.0
     */
    public function getState() {
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
     * @return mixed Address postal code
     * @since 1.0
     */
    public function getPostalCode() {
        return isset($this->location->{'postal_code'}) ? $this->location->{'postal_code'} : null;
    }
    
}
