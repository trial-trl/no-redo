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
abstract class Query {
    
    private $bind;
    
    public $conn;
    public $statement;
    
    /**
     * 
     * 
     * @param PDO $conn
     */
    public function __construct(PDO $conn) {
        $this->conn = $conn;
    }
    
    /**
     * 
     * 
     * @param PDO $conn
     */
    public function conn(PDO $conn) {
        $this->conn = $conn;
        return $this;
    }
    
    /**
     * 
     * 
     * @param array|string $columns
     */
    abstract public function columns($columns);
    
    abstract public function run();
    
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
    
    abstract public function prepare();
    
    public function bind($bind = null) {
        if ($bind == null) {
            return $this->bind;
        } else {
            $this->bind = $bind;
        }
    }
    
    /**
     * @param string $columns
     */
    public function prepareToBind($columns) {
        $exploded_columns = explode(',', preg_replace('/\s+/', '', $columns));
        $prepared_columns = [];
        foreach ($exploded_columns as $i => $column) {
            $prepared_columns[$i] = ':' . $column;
        }
        return $prepared_columns;
    }

    /**
     * @param array $prepared_columns Columns prepared to bind
     * @param array $values Values to be binded
     * @return array
     */
    public function prepareInputParameters(array $prepared_columns, array $values) {
        $input_parameters = [];
        foreach ($values as $i => $value) {
            $input_parameters[$prepared_columns[$i]] = $value;
        }
        return $input_parameters;
    }
    
}