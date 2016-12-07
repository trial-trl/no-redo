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

namespace Profile;

use Account\Base as Account;
use JsonSerializable;

require_once 'Account.php';
require_once __DIR__ . '/../../Request.php';

class Institution extends Account implements JsonSerializable {
    
    private $cnpj;
    private $name;
    private $infos;
    
    public function __construct($id = null) {
        parent::__construct();
    }
    
    public function setCNPJ($cnpj) {
        $this->cnpj = $cnpj;
        return $this;
    }
    
    public function setName($name) {
        $this->name = $name;
        return $this;
    }
    
    public function setInfos($infos) {
        $this->infos = $infos ? new DecodeInstitutionInfos(base64_decode($infos)) : null;
        return $this;
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