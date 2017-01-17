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

namespace SQL;

use InsertClauses, PDO;

require_once __DIR__ . '/Query.class.php';
require_once __DIR__ . '/InsertClauses.class.php';
require_once __DIR__ . '/QueryResponse.class.php';

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

    public function run() : QueryResponse {
        $this->prepare();
        return new QueryResponse($this->statement, $this->bind(), function () {
            return ['id' => $this->conn->lastInsertId()];
        });
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