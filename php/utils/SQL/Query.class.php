<?php
/**
 * Description of Query
 *
 * Created on 04/09/2016, ~22:22:37
 * @author Matheus Leonardo dos Santos Martins
 * @copyright (c) 2016, TRIAL
 * 
 * @version 1.0
 * @package SQL
 */

/* 
 * 21/01/2017
 *      03:19:19 => renamed namespace from SQL to Utils\SQL
 *      18:16:35 => renamed namespace from Utils\SQL to NoRedo\Utils\SQL
 */

namespace NoRedo\Utils\SQL;

abstract class Query extends CommonQuery {
    
    private $bind;
    
    /**
     * 
     * 
     * @param array|string $columns
     */
    abstract public function columns($columns);
    
    abstract public function prepare();
    
    abstract public function run() : QueryResponse;
    
    /**
     * 
     * 
     * @param array|string $table
     */
    abstract public function table($table);
    
    /**
     * 
     * 
     * @param array $values
     */
    abstract public function values(array $values);
    
    public function bind($bind = null) {
        if ($bind == null) {
            return $this->bind;
        } else {
            $this->bind = $bind;
        }
    }
    
    /**
     * @param string $columns
     * 
     * Added $keep_name_column on 20/09/2016, 20:53:58
     */
    public function prepareToBind($columns, $count = false, $keep_name_column = false) : array {
        $exploded_columns = explode(',', preg_replace('/\s+/', '', $columns));
        $prepared_columns = [];
        if (is_int($count)) {
            for ($c = 0; $c < $count; $c++) {
                $prepared_columns[$c] = [];
                foreach ($exploded_columns as $column) {
                    $prepared_columns[$c][] = ':' . $column . $c;
                }
            }
        } else {
            foreach ($exploded_columns as $i => $column) {
                $prepared_columns[$i] = $keep_name_column ? $column . ' = :' . $column : ':' . $column;
            }
        }
        return $prepared_columns;
    }

    /**
     * @param array $prepared_columns Columns prepared to bind
     * @param array $values Values to be binded
     * @return array
     * 
     * 14/04/2017, 19:44:18 - 20:18:56 => added support to prepare given $values with array values inside a different treatment
     */
    public function prepareInputParameters(array $prepared_columns, array $values) : array {
        $input_parameters = [];
        $contain_array_values = is_array($values[0]);
        foreach ($values as $i => $value) {
            if ($contain_array_values) {
                foreach ($value as $c => $v) {
                    $input_parameters[$prepared_columns[$i][$c]] = $v;
                }
            } else {
                $input_parameters[$prepared_columns[$i]] = $value;
            }
        }
        return $input_parameters;
    }
    
    /* 21/10/2016
     *      22:42:00 - 22:48:03
     *          added static buildResponse(QueryResponse $query, callable $callback)
     *      22:50:23
     *          buildResponse(QueryResponse $query, callable $callback) renamed to helper(QueryResponse $query, callable $callback)
     */
    public static function helper(QueryResponse $response, callable $success, callable $error = null) {
        if ($response->success()) {
            return $success($response);
        } else {
            $data_error = ['error' => $response->getError(), 'message' => Message::ERROR];
            if ($error != null) {
                return $error($data_error);
            } else {
                return $data_error;
            }
        }
    }
    
}