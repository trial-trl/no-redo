<?php
/**
 * Description of Builder
 *
 * Created on 07/12/2016, 19:16:19
 * @author Matheus Leonardo dos Santos Martins
 * @copyright (c) 2016, TRIAL
 * 
 * @package Institution
 * 
 * @version 1.0
 */

namespace Institution;

use AccountBuilder;
use Institution;

require_once __DIR__ . '/../AccountBuilder.class.php';
require_once __DIR__ . '/Institution.class.php';

class Builder extends AccountBuilder {
    
    public function setCNPJ($cnpj) {
        $this->values['cnpj'] = $cnpj;
        return $this;
    }
    
    public function setName($name) {
        $this->values['name'] = $name;
        return $this;
    }
    
    public function setInfos($infos) {
        $this->values['infos'] = $infos;// removed on 04/01/2017, 18h34min: ? new DecodeInstitutionInfos(base64_decode($infos)) : null;
        return $this;
    }
    
    public function build() {
        return new Institution($this->values);
    }
    
}
