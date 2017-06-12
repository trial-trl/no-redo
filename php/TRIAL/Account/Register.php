<?php

/*
 * (c) 2017 TRIAL.
 * Created on 30/04/2017, 23:34:56.
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

namespace NoRedo\TRIAL\Account;

use \DateTime;

/**
 * Description of Register
 *
 * @copyright (c) 2017, TRIAL
 * @author Matheus Leonardo dos Santos Martins
 * 
 * @version 1.0
 * @package Register
 */
class Register implements \JsonSerializable {
    
    const TYPE_MATRIZ = 'MATRIZ';
    const TYPE_FILIAL = 'FILIAL';
    
    private $cnpj;
    private $type;
    private $opening;
    private $company_name;
    private $trading_name;
    private $main_activity;
    private $secondary_activities;
    private $legal_nature;
    private $address;
    private $email;
    private $phone;
    private $erf;
    private $situation;
    private $situation_date;
    private $situation_reason;
    private $special_situation;
    private $special_situation_date;
    private $share_capital;
    private $membership;
    private $last_update;
    
    public function __construct($data) {
        foreach ($data as $k => $v) {
            $this->{$k} = $v;
        }
        //$this->encoded = $this->infos = json_decode(utf8_encode($data));
    }
    
    public static function copy(array $copy) : Register {
        return new self($copy);
    }
    
    /**
     * @return string
     * 
     * @version 1.0
     * @since 1.0
     */
    public function getCNPJ() : string {
        return $this->cnpj;
    }
    
    /**
     * @return string
     * 
     * @version 1.0
     * @since 1.0
     */
    public function getType() : string {
        return $this->type;
    }
    
    /**
     * @return DateTime
     * 
     * @version 1.0
     * @since 1.0
     */
    public function getOpening() : DateTime {
        return $this->opening;
    }
    
    /**
     * @return string
     * 
     * @version 1.0
     * @since 1.0
     */
    public function getCompanyName() : string {
        return $this->company_name;
    }
    
    /**
     * @return string
     * 
     * @version 1.0
     * @since 1.0
     */
    public function getTradingName() : string {
        return $this->trading_name;
    }
    
    /**
     * @return \Register\InstitutionActivity
     * 
     * @version 1.0
     * @since 1.0
     */
    public function getMainActivity() : \Register\InstitutionActivity {
        return $this->main_activity;
    }
    
    /**
     * @return array
     * 
     * @version 1.0
     * @since 1.0
     */
    public function getSecondaryActivities() : array {
        return $this->secondary_activities;
    }
    
    /**
     * @return string
     * 
     * @version 1.0
     * @since 1.0
     */
    public function getLegalNature() : string {
        return $this->legal_nature;
    }
    
    /**
     * @return \NoRedo\Address
     * 
     * @version 1.0
     * @since 1.0
     */
    public function getAddress() : \NoRedo\Address {
        return $this->address;
    }
    
    /**
     * @return string
     * 
     * @version 1.0
     * @since 1.0
     */
    public function getEmail() : string {
        return $this->email;
    }
    
    /**
     * @return string
     * 
     * @version 1.0
     * @since 1.0
     */
    public function getPhone() : string {
        return $this->phone;
    }
    
    /**
     * @return string
     * 
     * @version 1.0
     * @since 1.0
     */
    public function getERF() : string {
        return $this->erf;
    }
    
    /**
     * @return string
     * 
     * @version 1.0
     * @since 1.0
     */
    public function getSituation() : string {
        return $this->situation;
    }
    
    /**
     * @return DateTime
     * 
     * @version 1.0
     * @since 1.0
     */
    public function getSituationDate() : DateTime {
        return $this->situation_date;
    }
    
    /**
     * @return string
     * 
     * @version 1.0
     * @since 1.0
     */
    public function getSituationReason() : string {
        return $this->situation_reason;
    }
    
    /**
     * @return string
     * 
     * @version 1.0
     * @since 1.0
     */
    public function getSpecialSituation() : string {
        return $this->special_situation;
    }
    
    /**
     * @return DateTime
     * 
     * @version 1.0
     * @since 1.0
     */
    public function getSpecialSituationDate() : DateTime {
        return $this->special_situation_date;
    }
    
    /**
     * @return float
     * 
     * @version 1.0
     * @since 1.0
     */
    public function getShareCapital() : float {
        return $this->share_capital;
    }
    
    /**
     * @return array
     * 
     * @version 1.0
     * @since 1.0
     */
    public function getMembership() : array {
        return $this->membership;
    }
    
    /**
     * @return DateTime
     * 
     * @version 1.0
     * @since 1.0
     */
    public function getLastUpdate() : DateTime {
        return $this->last_update;
    }

    public function jsonSerialize() {
        return [
            'cnpj' => $this->cnpj,
            'type' => $this->type,
            'opening' => $this->opening,
            'company_name' => $this->company_name,
            'trading_name' => $this->trading_name,
            'main_activity' => $this->main_activity,
            'secondary_activities' => $this->secondary_activities,
            'legal_nature' => $this->legal_nature,
            'address' => $this->address,
            'email' => $this->email,
            'phone' => $this->phone,
            'erf' => $this->erf,
            'situation' => $this->situation,
            'situation_date' => $this->situation_date,
            'situation_reason' => $this->situation_reason,
            'special_situation' => $this->special_situation,
            'special_situation_date' => $this->special_situation_date,
            'share_capital' => $this->share_capital,
            'membership' => $this->membership,
            'last_update' => $this->last_update
        ];
    }

}