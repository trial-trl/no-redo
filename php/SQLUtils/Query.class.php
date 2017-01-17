<?php
/**
 * Description of Query
 *
 * Created on 04/09/2016, ~22:22:37
 * @author Matheus Leonardo dos Santos Martins
 * @copyright (c) 2016, TRIAL
 * 
 * @package SQLUtils
 */

namespace SQL;

require_once 'CommonQuery.class.php';

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
    public function prepareToBind($columns, $keep_name_column = false) : array {
        $exploded_columns = explode(',', preg_replace('/\s+/', '', $columns));
        $prepared_columns = [];
        foreach ($exploded_columns as $i => $column) {
            $prepared_columns[$i] = $keep_name_column ? $column . ' = :' . $column : ':' . $column;
        }
        return $prepared_columns;
    }

    /**
     * @param array $prepared_columns Columns prepared to bind
     * @param array $values Values to be binded
     * @return array
     */
    public function prepareInputParameters(array $prepared_columns, array $values) : array {
        $input_parameters = [];
        foreach ($values as $i => $value) {
            $input_parameters[$prepared_columns[$i]] = $value;
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