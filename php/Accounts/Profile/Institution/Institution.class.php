<?php
/**
 * Description of Institution
 *
 * Created on 05/09/2016, ~20:18:19
 * @author Matheus Leonardo dos Santos Martins
 * @copyright (c) 2016, TRIAL
 * 
 * @package Profile
 */

/*
 * 18/10/2016, 01:33:36 => added getPhotoUrl()
 * 
 * 20/10/2016
 *      14:41:57 => added setPassword()
 *      14:42:33 => added getPassword()
 * 
 * 24/11/2016, 18:23:35 => now this class extends Account. Id, Email, Password, checkPassword(), getPhotoUrl(), and Activate are now handle by Account class, not being need to implement these items here.
 * 
 * 07/12/2016, 23:56:08 => added __construct() array $columns arg, allowing to select what data should be retrieved
 * 
 * 16/12/2016, 16:31:07 => now construct() can get existent Institutions by $search arg; finished switch $type from $search arg, supporting gets by email or id, added $columns arg (this allows to choose what columns should return)
 */

use Account\Base as Account;
use SQL\Select;

require_once __DIR__ . '/../Account.class.php';
require_once __DIR__ . '/../../../Request.class.php';

class Institution extends Account implements JsonSerializable {
    
    private $cnpj;
    private $name;
    private $infos;
    
    public function __construct($search = null, array $columns = null) {
        parent::__construct(TRIALAccount::INSTITUTION);
        if ($columns == null) {
            $columns = 'id, cnpj, name, email, activated';
        } else {
            $columns = implode(', ', $columns);
        }
        $type = gettype($search);
        $con = Database::connect(DATABASE_USERS);
        switch ($type) {
            case 'string':
                $data = (new Select($con))->table(TABLE_INSTITUTIONS)->columns($columns)->where('email = :email')->values([':email' => $search])->run();
                break;
            case 'integer':
                $data = (new Select($con))->table(TABLE_INSTITUTIONS)->columns($columns)->where('id = :id')->values([':id' => $search])->run();
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
        
        $data = gettype($data) === 'object' && $data->success() && $data->existRows() ? $data->getResult()[0] : [];
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
        return ['id' => $this->id, 'cnpj' => $this->cnpj, 'name' => $this->name, 'email' => $this->email, 'infos' => $this->infos != null ? $this->infos->encoded : null, 'activated' => $this->activated];
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

class InstitutionActivity implements JsonSerializable {
    
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