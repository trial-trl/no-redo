<?php

/* 
 * Description of Map
 * 
 * Last edition: 18/06/2016 22h08min
 *
 * Created on ??/??/2016
 * @author Matheus Leonardo dos Santos Martins
 * @copyright (c) 2016, TRIAL
 * 
 * @package TRIAL
 * 
 * @version 1.2
 */

namespace NoRedo\Utils;

require_once $_SERVER['DOCUMENT_ROOT'] . '/no-redo/repository/php/autoload.inc.php';

use NoRedo\Utils\Database, NoRedo\Utils\SQL\Query, NoRedo\Utils\SQL\Select;

/*
 * 21/10/2016
 *      02:02:26 => updated ConnectDB deprecated code in __construct
 *      02:10:22 => added buildResponse(); all deprecated code was updated
 *      02:12:40 => all $_SERVER codes changed to filter_input()
 * 
 * 01/05/2017 (v1.11NC) | Updated all codes to recent NoRedo repository codes
 *      18:55:44
 *              moved to Utils directory
 *              removed buildResponse() and replaced to Query::helper
 *              added namespace NoRedo\Utils and 'use' keyword
 *      19:01:15
 *              added con() method
 *      19:02:23 => all methods are now static methods
 * 
 * 20/11/2017 (v1.2)
 *      11:37:53
 *              added getPolygon() method
 *              replace calls to gettype() to specific type verification, like is_string()
 *      11:50:14 => added gpoly request, which calls recent-added getPolygon()
 *      11:58 => renamed file from Map.php to Map.class.php
 * 
 * 23/11/2017
 *      13:20:48 => added getCountyData($county) method
 *      13:45:02 => added gctinf request, which calls recent-added getCountyData($county)
 */
class Map {
    
    private static $con;
    
    /**
     * @return \PDO
     * 
     * @version 1.0
     * @since 1.11NC
     */
    public static function con() : \PDO {
Database::setHost('216.172.161.44');
Database::setAuth('trial105', 'i1eDq8*5lVwHLk*!gB');
        if (!self::$con) {
            self::$con = Database::connect(DB_PREFIX . 'map');
        }
        return self::$con;
    }
    
    /**
     * @return array States
     * 
     * @version 1.01
     * @since 1.0
     */
    public static function getStates() : array {
        return Query::helper((new Select(self::con()))->table('states')->columns('id, abbr AS name')->run(), function ($query) {
            if ($query->existRows()) {
                $result = $query->getResult();
                $result['message'] = Message::EXIST;
            } else {
            	$result['message'] = Message::NOT_EXIST;
            }
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
        if (is_string($state)) {
            $query = (new Select(self::con()))->table('states AS s')->columns('c.id, c.county')->leftJoin(['counties AS c ON c.state = s.id'])->where('s.abbr = :abbr')->values([':abbr' => $state]);
        } else {
            $query = (new Select(self::con()))->table('counties')->columns('id, county')->where('state = :state')->values([':state' => $state]);
        }
        return Query::helper($query->run(), function ($query) {
            $result = $query->getResult();
            $result['message'] = $query->existRows() ? Message::EXIST : Message::NOT_EXIST;
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
        if (is_string($county)) {
            $query = (new Select(self::con()))->table('counties')->columns('AsText(boundaries) AS polygon')->where('county = :county')->values([':county' => $county]);
        } else {
            $query = (new Select(self::con()))->table('counties')->columns('AsText(boundaries) AS polygon')->where('id = :id')->values([':id' => $county]);
        }
        return Query::helper($query->run(), function ($query) {
            $result = $query->getResult()[0];
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
        $query = (new Select(self::con()))->table('counties AS c')->columns('c.id, c.county, CONCAT(\'{"id": \', c.state, \', "abbr": "\', s.abbr, \'", "name": "\', s.state, \'"}\') AS state, CONCAT(\'{"micro": {"id": \', c.microrregiao, \', "name": "\', c.microrregiao_nome, \'"}, "meso": {"id": \', c.mesorregiao, \', "name": "\', c.mesorregiao_nome, \'"}, "name": "\', c.regiao, \'"}\') AS regiao, c.geocodigo, CONCAT(\'{"total": {"valor": \', i.populacao, \', "ano": \', i.ano_populacao, \'}, "estimado": {"valor": \', i.populacao_estimada, \', "ano": \', i.ano_populacao_estimada, \'}, "homens": {"valor": \', i.pop_res_homens, \', "porcentagem": \', ((i.pop_res_homens / i.pop_res) * 100), \'}, "mulheres": {"valor": \', i.pop_res_mulheres, \', "porcentagem": \', ((i.pop_res_mulheres / i.pop_res) * 100), \'}, "alfabetizada": {"valor": \', i.pop_res_alfabetizada, \', "porcentagem": \', ((i.pop_res_alfabetizada / i.pop_res) * 100), \'}}\') AS populacao, i.area_unidade_territorial_km2, i.densidade_demografica_hab_km2, i.gentilico, i.prefeito, i.estabelecimentos_saude_sus, CONCAT(\'{"valor": \', i.idhm, \', "ano": \', i.ano_idhm, \'}\') AS idhm, CONCAT(\'{"fund": {"valor": \', i.matricula_fund, \', "ano": \', i.ano_matricula_fund, \'}, "medio": {"valor": \', i.matricula_medio, \', "ano": \', i.ano_matricula_medio, \'}\') AS matricula, i.unidades_locais, i.pessoal_ocupado_total, CONCAT(\'{"valor": \', i.pib, \', "ano": \', i.ano_pib, \'}\') AS pib, i.pop_res_freq_cre_esc, i.pop_res_rel_cat, i.pop_res_rel_esp, i.pop_res_rel_evan, i.val_rend_part_rural, i.val_rend_part_urbano, i.val_rend_part_dom_rural, i.val_rend_part_dom_urbano, i.historico, AsText(c.boundaries) AS polygon')->rightJoin(['states AS s ON s.id = c.state', 'infos AS i ON i.county = c.id'])->limit(1);
        $where = '';
        $values = [];
        if (is_string($county)) {
            $where .= 'c.county = :county';
            $values += [':county' => $county];
            if (!empty($state) && is_string($state)) {
                $where .= ' AND s.abbr = :abbr';
                $values += [':abbr' => $state];
            }
        } else {
            $where .= 'c.id = :id';
            $values += [':id ' => $county];
        }
        $query->where($where)->values($values);
        return Query::helper($query->run(), function ($query) {
            $result = $query->getResult()[0];
            $result['polygon'] = json_decode('[' . preg_replace(['/POLYGON/i', '/(\(|\))/i', '/(-[0-9]{1,2}.[0-9]{10,})\s(-[0-9]{1,2}.[0-9]{10,})/i'], ['', '', '{"lng":$1,"lat":$2}'], $result['polygon']) . ']', true);
            return $result;
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