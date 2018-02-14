<?php

/*
 * (c) 2017 TRIAL.
 * Created on 28/03/2017, 16:10:29.
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

use \PDO,
        NoRedo\Utils\Database, NoRedo\TRIAL\Account\Institution as InstitutionAccount, NoRedo\Utils\SQL\Query, NoRedo\Utils\SQL\Select, NoRedo\Utils\SQL\Update, NoRedo\Utils\Message;

/**
 * Description of Institution
 *
 * @copyright (c) 2017, TRIAL
 * @author Matheus Leonardo dos Santos Martins
 * 
 * @version 1.01
 * @package Institution
 */
class Institution extends InstitutionAccount implements \JsonSerializable {
    
    const TABLE = 'users_institutions';
    
    const TYPE = 'type';
    const UNIQUE_CODE = 'unique_code';
    const IN_CLASS = 'in_class';
    const ALLOWED = 'allowed';
    const LICENSED = 'licensed';
    
    /**
     * Connection
     * 
     * @var PDO 
     */
    private static $con;
    /**
     * Clicker account type
     * 
     * @var string 
     */
    protected $type;
    /**
     * Clicker unique code
     * 
     * @var mixed 
     */
    protected $unique_code;
    /**
     * Clicker account in class
     * 
     * @var int 
     */
    protected $in_class;
    /**
     * Institution is licensed
     * 
     * @var bool 
     */
    protected $licensed;
    /**
     * User is allowed
     * 
     * @var bool 
     */
    protected $allowed;
    
    protected function __construct(array $copy = []) {
        parent::__construct();
        $loop = array_merge([self::TYPE => $this->type, self::UNIQUE_CODE => $this->unique_code, self::LICENSED => $this->licensed], $copy);
        foreach ($loop as $k => $v) {
            $this->{$k} = $v;
        }
    }
    
    /**
     * @return type
     * 
     * @version 1.0
     * @since 1.01
     */
    private static function con() : PDO {
        if (!self::$con) {
            self::$con = Database::connect(DATABASE_CLICKER);
        }
        return self::$con;
    }
    
    /**
     * @param type $id
     * @return type
     * @throws \InvalidArgumentException
     * 
     * @version 1.0
     * @since 1.01
     */
    public static function get($institution, array $columns = []) {
        $int_id = is_numeric($institution);
        if (empty($institution) || (!is_array($institution) && !$int_id)) {
            throw new \InvalidArgumentException();
        }
        return Query::helper((new Select(self::con()))->table(DATABASE_CLICKER . '.' . self::TABLE . ' AS ui')->columns(['ui.' . self::ALLOWED, 'ci.' . self::LICENSED, 'i.' . self::ID, 'i.' . self::CNPJ, 'i.' . self::REGISTER, 'i.' . self::NAME, 'i.' . self::EMAIL, 'i.' . self::ACTIVATED])->leftJoin([DATABASE_CLICKER . '.' . \NoRedo\TRIAL\Account\Clicker::TABLE_INSTITUTIONS . ' AS ci ON ui.institution = ci.id', DATABASE_USERS . '.' . InstitutionAccount::TABLE . ' AS i ON ci.institution = i.id'])->where('ui.institution ' . ($int_id ? '= :i' : 'IN (:i)'))->values([':i' => $int_id ? $institution : implode(', ', $institution)])->fetchMode(PDO::FETCH_CLASS, self::class)->run(), function ($query) use ($int_id) {
            return $query->existRows() ? ($int_id ? $query->getResult()[0] : $query->getResult()) : null;
        });
    }
    
    /**
     * @param mixed $institution
     * @param array $which
     * @return array
     * @throws \InvalidArgumentException
     * 
     * @version 1.0
     * @since 1.01
    public static function edit() : array {
        $institution = func_get_arg(0); $user = func_get_arg(1); $which = func_get_arg(2);
        if (!is_int($institution) && !($institution instanceof self)) {
            throw new \InvalidArgumentException();
        }
        if (!is_int($user)) {
            throw new \InvalidArgumentException();
        }
        if (!is_array($which)) {
            throw new \InvalidArgumentExceptio\n();
        }
        return Query::helper((new Update(self::con()))->table(self::TABLE)->columns(array_keys($which))->where(self::USER . ' = :user AND ' . self::INSTITUTION . ' = :institution')->values(array_values($which))->valuesWhere([':user' => $institution, ':institution' => $user])->run(), function ($query) {
            return ['message' => $query->success() ? Message::SAVED_WITH_SUCCESS : Message::ERROR];
        });
    }
     */
    
    /**
     * @param array $copy
     * @return \self
     * 
     * @version 1.0
     * @since 1.01
     */
    public static function copy(array $copy) {
        return new self($copy);
    }
    
    /**
     * @param string $type
     * 
     * @version 1.0
     * @since 1.0
     */
    public function setClickerType(string $type) {
        $this->type = $type;
    }
    
    /**
     * @param string $unique_code
     * 
     * @version 1.0
     * @since 1.0
     */
    public function setUniqueCode(string $unique_code) {
        $this->unique_code = $unique_code;
    }
    
    /**
     * @param bool $licensed
     * 
     * @version 1.0
     * @since 1.01
     */
    public function setLicensed(bool $licensed) {
        $this->licensed = $licensed;
    }
    
    /**
     * @return string Clicker type Account
     * 
     * @version 1.0
     * @since 1.0
     */
    public function getClickerType() : string {
        return $this->type;
    }
    
    /**
     * @return string Clicker user institution Unique Code
     * 
     * @version 1.0
     * @since 1.0
     */
    public function getUniqueCode() : string {
        return $this->unique_code;
    }
    
    /**
     * @return mixed Clicker type Account
     * 
     * @version 1.0
     * @since 1.01
     */
    public function getClass() {
        return $this->in_class;
    }
    
    /**
     * @return bool Institution is licensed
     * 
     * @version 1.0
     * @since 1.01
     */
    public function isLicensed() : bool {
        return $this->licensed ?? false;
    }
    
    /**
     * @return bool User is allowed
     * 
     * @version 1.0
     * @since 1.01
     */
    public function isAllowed() : bool {
        return $this->allowed ?? false;
    }

    /**
     * 
     * @since 1.8
     */
    public function jsonSerialize() {
        return [self::TYPE => $this->type, self::INSTITUTION => parent::jsonSerialize(), self::UNIQUE_CODE => $this->unique_code, self::LICENSED => $this->licensed];
    }
    
}
