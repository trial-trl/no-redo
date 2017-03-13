<?php
/**
 * Description of Institution
 *
 * Created on 05/09/2016, ~20:18:19
 * @author Matheus Leonardo dos Santos Martins
 * @copyright (c) 2016, TRIAL
 * 
 * @version 1.01
 * @package Account
 */

namespace NoRedo\TRIAL\Account;

use NoRedo\TRIAL\Account as TRIALAccount, NoRedo\Utils\Database, NoRedo\Utils\SQL\Select;

class Institution extends TRIALAccount implements \JsonSerializable {
    
    const TABLE = 'institutions';
    
    const CNPJ = 'cnpj';
    const NAME = 'name';
    
    private $cnpj;
    private $name;
    private $infos;
    
    public function __construct($search = null, array $columns = null) {
        parent::__construct(TRIALAccount::INSTITUTION);
        $columns = $columns == null ? self::ID . ', ' . self::CNPJ . ', ' . self::NAME . ', ' . self::EMAIL . ', ' . self::ACTIVATED : implode(', ', $columns);
        $type = gettype($search);
        $con = Database::connect(DATABASE_USERS);
        switch ($type) {
            case 'string':
                $data = (new Select($con))->table(self::TABLE)->columns($columns)->where(self::EMAIL . ' = :email')->values([':email' => $search])->run();
                break;
            case 'integer':
                $data = (new Select($con))->table(self::TABLE)->columns($columns)->where(self::ID . ' = :id')->values([':id' => $search])->run();
                break;
            default:
                $data = $search != null ? $search : [];
        }
        // added 04/01/2017, 18h17min
        if (gettype($data) === 'object') {
            if ($data->success() && $data->existRows()) {
                $data = $data->getResult()[0];
            }
        }
        foreach ($data as $key => $value) {
            $this->{$key} = $value;
        }
    }
    
    public function getCNPJ() {
        return $this->cnpj;
    }
    
    public function getName() {
        return $this->name;
    }
    
    public function getInfos() {
        return $this->infos;
    }

    public function jsonSerialize() {
        return [self::ID => $this->id, self::CNPJ => $this->cnpj, self::NAME => $this->name, self::EMAIL => $this->email, 'infos' => $this->infos != null ? $this->infos->encoded : null, self::ACTIVATED => $this->activated];
    }

}
 
class DecodeInstitutionInfos {
    
    public $encoded;
    private $infos;
    
    private $main_activity;
    
    public function __construct($infos) {
        $this->encoded = $this->infos = json_decode(utf8_encode($infos));
        $this->setMainActivity();
        $this->situation_date = $this->infos->{'data_situacao'};
        $this->type = $this->infos->{'tipo'};
        $this->company_name = $this->infos->{'nome'};
        $this->phone = $this->infos->{'telefone'};
        $this->situation_date = $this->infos->{'data_situacao'};
        $this->setSecondaryActivities();
        $this->situation = $this->infos->{'situacao'};
        $this->sublocality = $this->infos->{'situacao'};
        $this->route = $this->infos->{'logradouro'};
        $this->street_number = $this->infos->{'numero'};
        $this->postal_code = $this->infos->{'cep'};
        $this->county = $this->infos->{'municipio'};
        $this->state = $this->infos->{'uf'};
        $this->opening = $this->infos->{'abertura'};
        $this->legal_nature = $this->infos->{'natureza_juridica'};
        $this->trading_name = $this->infos->{'natureza_juridica'};
        $this->last_update = $this->infos->{'ultima_atualizacao'};
        $this->status = $this->infos->{'status'};
        $this->additional = $this->infos->{'complemento'};
        $this->email = $this->infos->{'email'};
        $this->efr = $this->infos->{'efr'};
        $this->situation_reason = $this->infos->{'motivo_situacao'};
        $this->special_situation = $this->infos->{'situacao_especial'};
        $this->special_situation_date = $this->infos->{'data_situacao_especial'};
    }
    
    private function setMainActivity() {
        $activity = $this->infos->{'atividade_principal'}[0];
        $this->main_activity = new InstitutionActivity($activity->{'code'}, $activity->{'text'});
    }
    
    private function setSecondaryActivities() {
        $activities = $this->infos->{'atividades_secundarias'};
        foreach ($activities as $i => $activity) {
            $this->secondary_activity[$i] = new InstitutionActivity($activity->{'code'}, $activity->{'text'});
        }
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