<?php

/*
 * (c) 2017 TRIAL.
 * Created on 30/04/2017, 23:35:09.
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

namespace NoRedo\TRIAL\Account\Register;

use \DateTime,
        NoRedo\TRIAL\Account\Register, NoRedo\Address;

/**
 * Description of Builder
 *
 * @copyright (c) 2017, TRIAL
 * @author Matheus Leonardo dos Santos Martins
 * 
 * @version 1.0
 * @package Builder
 */
class Builder {
    
    private $values = [];
    
    public function setCNPJ(string $cnpj) : Builder {
        $this->values['cnpj'] = $cnpj;
        return $this;
    }
    
    public function setType(string $type) : Builder {
        $this->values['type'] = $type;
        return $this;
    }
    
    public function setOpening(string $opening) : Builder {
        $this->values['opening'] = DateTime::createFromFormat('Y-m-d', $opening);
        return $this;
    }
    
    public function setCompanyName(string $name) : Builder {
        $this->values['company_name'] = $name;
        return $this;
    }
    
    public function setTradingName(string $name) : Builder {
        $this->values['trading_name'] = $name;
        return $this;
    }
    
    public function setMainActivity($activity) : Builder {
        $this->values['main_activity'] = new InstitutionActivity($activity->{'code'}, $activity->{'text'});
        return $this;
    }
    
    public function setSecondaryActivities($activities) : Builder {
        $secondary_activities = [];
        foreach ($activities as $i => $activity) {
            $secondary_activities[$i] = new InstitutionActivity($activity->{'code'}, $activity->{'text'});
        }
        $this->values['secondary_activities'] = $secondary_activities;
        return $this;
    }
    
    public function setLegalNature(string $legal_nature) : Builder {
        $this->values['legal_nature'] = $legal_nature;
        return $this;
    }
    
    public function setAddress(Address $address) : Builder {
        $this->values['address'] = $address;
        return $this;
    }
    
    public function setEmail(string $email) : Builder {
        $this->values['email'] = $email;
        return $this;
    }
    
    public function setPhone(string $phone) : Builder {
        $this->values['phone'] = $phone;
        return $this;
    }
    
    public function setERF(string $erf) : Builder {
        $this->values['erf'] = $erf;
        return $this;
    }
    
    public function setSituation(string $situation) : Builder {
        $this->values['situation'] = $situation;
        return $this;
    }
    
    public function setSituationDate(string $date) : Builder {
        $this->values['situation_date'] = DateTime::createFromFormat('Y-m-d', $date);
        return $this;
    }
    
    public function setSituationReason(string $reason) : Builder {
        $this->values['situation_reason'] = $reason;
        return $this;
    }
    
    public function setSpecialSituation(string $situation) : Builder {
        $this->values['special_situation'] = $situation;
        return $this;
    }
    
    public function setSpecialSituationDate(string $date) : Builder {
        $this->values['special_situation_date'] = DateTime::createFromFormat('Y-m-d', $date);
        return $this;
    }
    
    public function setShareCapital(float $capital) : Builder {
        $this->values['share_capital'] = $capital;
        return $this;
    }
    
    public function setMembership(array $membership) : Builder {
        $this->values['membership'] = $membership;
        return $this;
    }
    
    public function setLastUpdate(string $date) : Builder {
        $this->values['last_update'] = DateTime::createFromFormat('Y-m-d', $date);
        return $this;
    }
    
    public function build() : Register {
        return Register::copy($this->values);
    }
    
}

class InstitutionActivity implements \JsonSerializable {
    
    private $code;
    private $text;
    
    public function __construct($code, $text) {
        $this->code = $code;
        $this->text = $text;
    }
    
    public function getCode() {
        return $this->code;
    }
    
    public function getText() {
        return $this->text;
    }
    
    public function jsonSerialize() {
        return ['code' => $this->code, 'text' => $this->text];
    }
    
}