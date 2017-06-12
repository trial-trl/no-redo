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

class Address implements \JsonSerializable {
    
    const NUMBER = 'number';
    const COMPLEMENT = 'complement';
    const DISTRICT = 'district';
    const CITY = 'city';
    const STATE = 'state';
    const COUNTRY = 'country';
    const POSTAL_CODE = 'postal_code';
    
    private $number;
    private $complement;
    private $district;
    private $city;
    private $state;
    private $country;
    private $postal_code;
    
    public function __construct($location) {
        $loop = empty($location) ? [] : $location;
        foreach ($loop as $k => $v) {
            $this->{$k} = $v;
        }
    }
    
    /**
     * @return mixed Address number
     * @since 1.0
     */
    public function getNumber() {
        return $this->number;
    }
    
    /**
     * @return mixed Address complement
     * @since 1.0
     */
    public function getComplement() {
        return $this->complement;
    }
    
    /**
     * @return mixed Address district
     * @since 1.0
     */
    public function getDistrict() {
        return $this->district;
    }
    
    /**
     * @return mixed Address city
     * @since 1.0
     */
    public function getCity() {
        return $this->city;
    }
    
    /**
     * @return mixed Address state
     * @since 1.0
     */
    public function getState() {
        return $this->state;
    }
    
    /**
     * @return string Address country
     * @since 1.0
     */
    public function getCountry() : string {
        return $this->country ?? 'BR';
    }
    
    /**
     * @return mixed Address postal code
     * @since 1.0
     */
    public function getPostalCode() {
        return $this->postal_code;
    }

    public function jsonSerialize() {
        return [self::POSTAL_CODE => $this->postal_code, self::NUMBER => $this->number, self::COMPLEMENT => $this->complement, self::DISTRICT => $this->district, self::CITY => $this->city, self::STATE => $this->state, self::COUNTRY => $this->country];
    }

}