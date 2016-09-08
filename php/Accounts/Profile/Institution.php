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
class Institution implements JsonSerializable {
    
    private $id;
    private $cnpj;
    private $name;
    private $email;
    private $infos;
    private $activated;
    
    public function __construct($id, $cnpj = null, $name = null, $email = null, $infos = null, $activated = null) {
        $this->setId($id);
        $this->setCNPJ($cnpj);
        $this->setName($name);
        $this->setEmail($email);
        $this->setInfos($infos);
        $this->setActivated($activated);
    }
    
    public function setId($id) {
        $this->id = $id;
        return $this;
    }
    
    public function setCNPJ($cnpj) {
        $this->cnpj = $cnpj;
        return $this;
    }
    
    public function setName($name) {
        $this->name = $name;
        return $this;
    }
    
    public function setEmail($email) {
        $this->email = $email;
        return $this;
    }
    
    public function setInfos($infos) {
        $this->infos = new DecodeInstitutionInfos(base64_decode($infos));
        return $this;
    }
    
    public function setActivated($activated) {
        $this->activated = $activated;
        return $this;
    }
    
    public function getId() {
        return $this->id;
    }
    
    public function getCNPJ() {
        return $this->cnpj;
    }
    
    public function getName() {
        return $this->name;
    }
    
    public function getEmail() {
        return $this->email;
    }
    
    public function getInfos() {
        return $this->infos;
    }
    
    public function isActivated() {
        return $this->activated;
    }

    public function jsonSerialize() {
        return ['a' => $this->jsonSerialize()];
    }

}
 
class DecodeInstitutionInfos {
    
    private $infos;
    
    private $main_activity;
    
    public function __construct($infos) {
        $this->infos = json_decode(utf8_encode($infos));
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

class InstitutionActivity {
    
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
    
}