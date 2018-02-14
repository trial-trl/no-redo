<?php

namespace NoRedo\TRIAL\Account;

use NoRedo\TRIAL\Account as TRIALAccount, NoRedo\Utils\Database, NoRedo\Utils\SQL\Query, NoRedo\Utils\SQL\Select, NoRedo\Address, NoRedo\TRIAL\Account\Register;

/**
 * Description of Institution
 *
 * Created on 05/09/2016, ~20:18:19
 * @author Matheus Leonardo dos Santos Martins
 * @copyright (c) 2016, TRIAL
 * 
 * @version 1.02
 * @package Account
 */
class Institution extends TRIALAccount implements \JsonSerializable {
    
    const TABLE = 'institutions';
    
    const CNPJ = 'cnpj';
    const REGISTER = 'infos';
    const NAME = 'name';
    
    protected $cnpj;
    protected $infos;
    protected $register;
    protected $name;
    
    protected function __construct($custom = []) {
        parent::__construct(TRIALAccount::INSTITUTION);
        $loop = empty($custom) ? [self::ID => $this->id, self::CNPJ => $this->cnpj, self::REGISTER => $this->infos, self::NAME => $this->name, self::EMAIL => $this->email, self::ACTIVATED => $this->activated] : $custom;
        foreach ($loop as $k => $v) {
            if ($k === 'cnpj' && !empty($v) && empty($loop['infos'])) {
                $this->register = (new Register\Builder())->setCNPJ($v)->build();
            } else if ($k === 'infos' && !empty($v)) {
                $i = json_decode(utf8_encode(base64_decode($v)), true);
                $this->register = (new Register\Builder())->setCNPJ($i['cnpj'])->setType($i['tipo'])->setOpening(date('Y-m-d', strtotime(str_replace('/', '-', $i['abertura']))))->setCompanyName($i['nome'])->setTradingName($i['fantasia'])->setMainActivity($i['atividade_principal'][0])->setSecondaryActivities($i['atividades_secundarias'])->setAddress(new Address($i['endereco'] ?? [Address::NUMBER => $i['numero'], Address::POSTAL_CODE => $i['cep'], Address::DISTRICT => $i['logradouro'], Address::CITY => $i['municipio'], Address::STATE => $i['uf'], Address::COUNTRY => 'BR']))->setLegalNature($i['natureza_juridica'])->setEmail($i['email'])->setPhone($i['telefone'])->setERF($i['efr'])->setSituation($i['situacao'])->setSituationDate(date('Y-m-d', strtotime(str_replace('/', '-', $i['data_situacao']))))->setSituationReason($i['motivo_situacao'])->setSpecialSituation($i['situacao_especial'])->setSpecialSituationDate(date('Y-m-d', strtotime(str_replace('/', '-', $i['data_situacao_especial']))))->setLastUpdate(date('Y-m-d', strtotime(str_replace('/', '-', $i['ultima_atualizacao']))))->build();
            } else {
                $this->{$k} = $v;
            }
        }
        unset($this->infos);
        unset($this->cnpj);
    }
    
    /**
     * Added on 01/05/2017, 12:08:53
     * 
     * @param type $search
     * @param array $columns
     * @return type
     * @throws \InvalidArgumentException
     * 
     * @version 1.0
     * @since 1.02
     */
    public static function get($search, array $columns = [self::ID, self::CNPJ, self::REGISTER, self::NAME, self::EMAIL, self::ACTIVATED]) {
        if (!is_int($search) && !is_string($search)) {
            throw new \InvalidArgumentException('Institution::get(): search must be an integer or string');
        }
        $q = (new Select(Database::connect(DATABASE_USERS)))->table(self::TABLE)->columns($columns);
        if (is_string($search)) {
            $q->where(self::EMAIL . ' = :email')->values([':email' => $search]);
        } else if (is_int($search)) {
            $q->where(self::ID . ' = :id')->values([':id' => $search]);
        }
        return Query::helper($q->fetchMode(\PDO::FETCH_CLASS, self::class)->run(), function ($query) {
            return $query->getResult()[0] ?? null;
        });
    }
    
    /**
     * @param array $copy
     * @return \NoRedo\TRIAL\Account\Institution
     * 
     * @version 1.0
     * @since 1.02
     */
    public static function copy(array $copy) {
        return new self($copy);
    }
    
    public function getRegister() : Register {
        return $this->register ?? Register::copy([]);
    }
    
    public function getName() {
        return $this->name;
    }

    public function jsonSerialize() {
        return array_filter(array_merge([self::NAME => $this->name, self::REGISTER => $this->register], parent::jsonSerialize()));
    }

}