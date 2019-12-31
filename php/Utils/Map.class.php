<?php

/*
 * (c) 2016 TRIAL.
 * Created on 18/06/2016, 22:08.
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

namespace NoRedo\Utils;

require_once __DIR__ . '/../autoload.inc.php';

use NoRedo\Utils\Database, NoRedo\Utils\SQL\Query, NoRedo\Utils\SQL\Select;

/**
 * Description of Map
 *
 * @copyright (c) 2016, TRIAL
 * @author MLSM<mlsm@trialent.com>
 * 
 * @version 1.2
 * @package \NoRedo\Utils
 */
class Map {
  
    private static $DRIVER   = 'mysql';
    private static $DATABASE = 'trial105_map';
    private static $HOST     = '216.172.161.44';
    private static $USER     = 'trial105';
    private static $PASS     = 'i1eDq8*5lVwHLk*!gB';
    
    private static $conn;
    
    /**
     * @return \PDO
     * 
     * @version 1.0
     * @since 1.11NC
     */
    public static function con() : \atk4\dsql\Connection {
        if (!(self::$conn instanceof \atk4\dsql\Connection)) {
          self::$conn = \atk4\dsql\Connection::connect(self::$DRIVER . ':host=' . self::$HOST . ';dbname=' . self::$DATABASE . ';charset=utf8', self::$USER, self::$PASS);
        }
        return self::$conn;
    }
    
    /**
     * @return array States
     * 
     * @version 1.01
     * @since 1.0
     */
    public static function getStates() : array {
        $dsql = self::con()->dsql();
        return self::con()->atomic(function () use ($dsql) {
            $q = $dsql->table('states')->field('id, state, abbr AS name');
            $result = $q->getRow();
            $result['message'] = !empty($result) ? 'E' : 'NE';
            return $result;
        });
    }
    
    /**
     * @return array Counties
     * 
     * @version 1.01
     * @since 1.0
     */
    public static function getCounties($state) : array {
        $dsql = self::con()->dsql();
        return self::con()->atomic(function () use ($dsql, $state) {
            if (is_string($state)) {
                $q = $dsql->table('states', 's')
                        ->field('c.id, c.county')
                        ->join('counties c', $dsql->expr('c.state = s.id'))
                        ->where('s.abbr', $state);
            } else {
                $q = $dsql->table('counties')
                        ->field('id, county')
                        ->where('state', $state);
            }
            $result = $q->getRow();
            $result['message'] = !empty($result) ? 'E' : 'NE';
            return $result;
        });
    }
    
    /**
     * @return array Polygon
     * 
     * @version 1.0
     * @since 1.2
     */
    public static function getPolygon($county) : array {
        $dsql = self::con()->dsql();
        return self::con()->atomic(function () use ($dsql, $county) {
            $result = $dsql->table('counties')
                    ->field($dsql->expr('AsText(boundaries)'), 'polygon')
                    ->where(is_string($county) ? 'county' : 'id', $county)
                    ->getRow();
            return json_decode('[' . preg_replace(['/POLYGON/i', '/(\(|\))/i', '/(-[0-9]{1,2}.[0-9]{10,})\s(-[0-9]{1,2}.[0-9]{10,})/i'], ['', '', '{"lng":$1,"lat":$2}'], $result['polygon']) . ']', true);
        });
    }
    
    /**
     * @return array County's all data
     * 
     * @version 1.0
     * @since 1.2
     */
    public static function getCountyData($county, $state) : array {
        $dsql = self::con()->dsql();
        return self::con()->atomic(function () use ($dsql, $state, $county) {
            $q = $dsql->table('counties', 'c')
                    ->field($dsql->expr('c.id, c.county, CONCAT(\'{"id": \', c.state, \', "abbr": "\', s.abbr, \'", "name": "\', s.state, \'"}\') AS state, CONCAT(\'{"micro": {"id": \', c.microrregiao, \', "name": "\', c.microrregiao_nome, \'"}, "meso": {"id": \', c.mesorregiao, \', "name": "\', c.mesorregiao_nome, \'"}, "name": "\', c.regiao, \'"}\') AS regiao, c.geocodigo, CONCAT(\'{"total": {"valor": \', i.populacao, \', "ano": \', i.ano_populacao, \'}, "estimado": {"valor": \', i.populacao_estimada, \', "ano": \', i.ano_populacao_estimada, \'}, "homens": {"valor": \', i.pop_res_homens, \', "porcentagem": \', ((i.pop_res_homens / i.pop_res) * 100), \'}, "mulheres": {"valor": \', i.pop_res_mulheres, \', "porcentagem": \', ((i.pop_res_mulheres / i.pop_res) * 100), \'}, "alfabetizada": {"valor": \', i.pop_res_alfabetizada, \', "porcentagem": \', ((i.pop_res_alfabetizada / i.pop_res) * 100), \'}}\') AS populacao, i.area_unidade_territorial_km2, i.densidade_demografica_hab_km2, i.gentilico, i.prefeito, i.estabelecimentos_saude_sus, CONCAT(\'{"valor": \', i.idhm, \', "ano": \', i.ano_idhm, \'}\') AS idhm, CONCAT(\'{"fund": {"valor": \', i.matricula_fund, \', "ano": \', i.ano_matricula_fund, \'}, "medio": {"valor": \', i.matricula_medio, \', "ano": \', i.ano_matricula_medio, \'}\') AS matricula, i.unidades_locais, i.pessoal_ocupado_total, CONCAT(\'{"valor": \', i.pib, \', "ano": \', i.ano_pib, \'}\') AS pib, i.pop_res_freq_cre_esc, i.pop_res_rel_cat, i.pop_res_rel_esp, i.pop_res_rel_evan, i.val_rend_part_rural, i.val_rend_part_urbano, i.val_rend_part_dom_rural, i.val_rend_part_dom_urbano, i.historico, AsText(c.boundaries) AS polygon'))
                    ->join('states s', 'c.state', 'right')
                    ->join('infos i', $dsql->expr('i.county = c.id'), 'right');
            if (is_string($county)) {
                $q->where($dsql->expr('CONVERT(CAST(c.county AS BINARY) USING utf8)'), 'LIKE', '%'. $county . '%');
                if (!empty($state) && is_string($state)) {
                    $q->where('s.abbr', $state);
                }
            } else {
                $q->where('c.id', $county);
            }
            $place = $q->getRow();
            if (!empty($place)) {
                  $place['polygon'] = json_decode('[' . preg_replace(['/POLYGON/i', '/(\(|\))/i', '/(-[0-9]{1,2}.[0-9]{10,})\s(-[0-9]{1,2}.[0-9]{10,})/i'], ['', '', '{"lng":$1,"lat":$2}'], $place['polygon']) . ']', true);
                  return $place;
            }
            return [];
        });
    }
    
}

$request = json_decode(file_get_contents('php://input'));
$r = $request->{'r'} ?? filter_input($method = ($_SERVER['REQUEST_METHOD'] === 'GET' ? INPUT_GET : INPUT_POST), 'r');
if (!empty($r)) {
    if (!empty($request)) {
        switch ($r) {
            case 'gstats':
                echo json_encode(Map::getStates());
                break;
            case 'gcntes':
                echo json_encode(Map::getCounties($request->{'state'}));
                break;
            case 'gpoly':
                echo json_encode(Map::getPolygon($request->{'county'}));
                break;
            case 'gctinf':
                echo json_encode(Map::getCountyData($request->{'county'}, $request->{'state'}));
                break;
        }
    } else {
        switch ($r) {
            case 'gstats':
                echo json_encode(Map::getStates());
                break;
            case 'gcntes':
                echo json_encode(Map::getCounties(filter_input($method, 'state')));
                break;
            case 'gpoly':
                echo json_encode(Map::getPolygon(filter_input($method, 'county')));
                break;
            case 'gctinf':
                echo json_encode(Map::getCountyData(filter_input($method, 'county'), filter_input($method, 'state')));
                break;
        }
    }
}