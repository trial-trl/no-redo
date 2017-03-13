<?php

/*
 * (c) 2017 TRIAL.
 * Created on 23/02/2017, 16:43:45.
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

namespace NoRedo\TRIAL\Account\Clicker;

use \NoRedo\TRIAL\Account\Clicker;

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
    
    protected $values = [];
    
    public function setId($id) {
        $this->values['id'] = $id;
        return $this;
    }
    
    public function setUser(User $user) {
        $this->values['user'] = $user;
        return $this;
    }
    
    public function setType($type) {
        if ($type !== Clicker::TEACHER && $type !== Clicker::STUDENT) {
            throw new \InvalidArgumentException('$type must be one of these values: Clicker::TEACHER | Clicker::STUDENT');
        }
        $this->values['type'] = $type;
        return $this;
    }
    
    public function setUniqueCode(string $unique_code) {
        $this->values['unique_code'] = $unique_code;
        return $this;
    }
    
    public function addInstitution(Institution $institution) {
        if (!isset($this->values['institutions']) || gettype($this->values['institutions']) !== 'array') {
            $this->values['institutions'] = [];
        }
        $this->values['institutions'][] = $institution;
        return $this;
    }
    
    public function build() {
        return new Clicker($this->values);
    }
    
}
