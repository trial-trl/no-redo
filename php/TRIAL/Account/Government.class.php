<?php
/*
 * 16/12/2016
 *      16:26:36 => remove $permission and getPermission()
 *      19:40:15 => added parent::__construct(TRIALAccount::GOVENRMENT); removed namespace, update Connection code
 * 
 * 21/01/2017
 *      01:26:14 => added namespace TRIAL\Account
 *      20:39:43 => renamed namespace from TRIAL\Account to NoRedo\TRIAL\Account
 * 
 * 20/01/2018
 *      14:12:26 => added argument array $columns to get() method
 */

namespace NoRedo\TRIAL\Account;

use NoRedo\TRIAL\Account as TRIALAccount, NoRedo\Utils\Database, NoRedo\Utils\SQL\Query, NoRedo\Utils\SQL\Select, NoRedo\TRIAL\Account\Register, NoRedo\Address;

/**
 * Description of Government
 * 
 * Created on 24/11/2016, 18:03:23
 * @author Matheus Leonardo dos Santos Martins
 * @copyright (c) 2016, TRIAL
 * 
 * @version 1.02
 * @package Account
 */
final class Government extends TRIALAccount {
    
    const TABLE = 'governments';
    
    const CNPJ = 'cnpj';
    const REGISTER = 'infos';
    const NAME = 'name';
    
    private $cnpj;
    private $infos;
    private $register;
    private $name;
    
    protected function __construct($custom = []) {
        parent::__construct(TRIALAccount::GOVERNMENT);
        $loop = empty($custom) ? [self::ID => $this->id, self::CNPJ => $this->cnpj, self::REGISTER => $this->infos, self::NAME => $this->name, self::EMAIL => $this->email, self::ACTIVATED => $this->activated] : $custom;
        foreach ($loop as $k => $v) {
            if ($k === 'cnpj' && empty($loop['infos'])) {
                $this->register = (new Register\Builder())->setCNPJ($v)->build();
            } else if ($k === 'infos' && !empty($v)) {
                $i = json_decode(utf8_encode(base64_decode($v)));
                $this->register = (new Register\Builder())->setCNPJ($i->{'cnpj'})->setType($i->{'tipo'})->setOpening($i->{'abertura'})->setCompanyName($i->{'nome'})->setTradingName($i->{'fantasia'})->setMainActivity($i->{'atividade_principal'}[0])->setSecondaryActivities($i->{'atividades_secundarias'})->setAddress(new Address([Address::NUMBER => $i->{'numero'}, Address::POSTAL_CODE => $i->{'cep'}, Address::DISTRICT => $i->{'logradouro'}, Address::CITY => $i->{'municipio'}, Address::STATE => $i->{'uf'}, Address::COUNTRY => 'BR']))->setLegalNature($i->{'natureza_juridica'})->setEmail($i->{'email'})->setPhone($i->{'telefone'})->setERF($i->{'efr'})->setSituation($i->{'situacao'})->setSituationDate($i->{'data_situacao'})->setSituationReason($i->{'motivo_situacao'})->setSpecialSituation($i->{'situacao_especial'})->setSpecialSituationDate($i->{'data_situacao_especial'})->setLastUpdate($i->{'ultima_atualizacao'})->build();
            } else {
                $this->{$k} = $v;
            }
        }
        unset($this->infos);
        unset($this->cnpj);
    }
    
    /**
     * 
     * @param type $search
     * @return type
     * @throws \InvalidArgumentException
     * 
     * @version 1.1
     * @since 1.02
     */
    public static function get($search, array $columns = [self::ID, self::CNPJ, self::REGISTER, self::NAME, self::EMAIL, self::PASSWORD, self::ACTIVATED]) {
        if (!is_int($search) && !is_string($search)) {
            throw new \InvalidArgumentException('Government::get(): search must be an integer or string');
        }
        $q = (new Select(Database::connect(DATABASE_USERS)))->table(self::TABLE)->columns($columns)->fetchMode(\PDO::FETCH_CLASS, self::class);
        if (is_string($search)) {
            $q->where(self::EMAIL . ' = :email')->values([':email' => $search]);
        } else if (is_int($search)) {
            $q->where(self::ID . ' = :id')->values([':id' => $search]);
        }
        return Query::helper($q->run(), function ($query) {
            return $query->getResult()[0];
        });
    }
    
    public static function copy(array $copy) : Government {
        return new self($copy);
    }
    
    public function getName() {
        return $this->name;
    }
    
    /**
     * @return \NoRedo\TRIAL\Account\Register
     * 
     * @version 1.0
     * @since 1.02
     */
    public function getRegister() : Register {
        return $this->register;
    }

    public function jsonSerialize() {
        return array_merge([self::NAME => $this->name, self::REGISTER => $this->register], parent::jsonSerialize());
    }
    
}
