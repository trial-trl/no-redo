<?php
/**
 * Description of Select
 * 
 * Created on 04/09/2016, ~22:09:29
 * @author Matheus Leonardo dos Santos Martins
 * @copyright (c) 2016, TRIAL
 * 
 * @package SQLUtils
 */

require_once __DIR__ . '/Query.php';
require_once __DIR__ . '/InsertClauses.php';

class Insert extends Query implements InsertClauses {
    
    /**
     * Columns that'll be returned in this query.
     * 
     * @var string 
     */
    public $columns;
    /**
     * Main database table name that this query will run in.
     * 
     * @var string 
     */
    public $table;
    /**
     * Values.
     * 
     * @var string
     */
    public $values;
    
    public function columns($columns) {
        if (gettype($columns) === 'array') {
            $columns = implode(',', $columns);
        }
        $this->columns = $columns;
        return $this;
    }
    
    public function prepare() {
	$prepared = $this->prepareToBind($this->columns);
	$this->bind($this->prepareInputParameters($prepared, $this->values));
        $this->statement = $this->conn->prepare('INSERT INTO ' . $this->table . ' ' . ($this->columns != null ? '(' . $this->columns . ') ' : null) . 'VALUES(' . implode(', ', $prepared) . ')');
    }

    public function run() {
        $this->statement->execute($this->bind());
        if ($this->statement) {
            return ['id' => $this->conn->lastInsertId()];
        } else {
            $error_info = $this->statement->errorInfo();
            return ['error' => true, 'error_info' => ['SQLSTATE' => $error_info[0], 'code' => $error_info[1], 'message' => $error_info[2]]];
        }
    }

    public function table($table) {
        $this->table = $table;
        return $this;
    }
    
    public function values(array $values) {
        $this->values = $values;
        return $this;
    }

}