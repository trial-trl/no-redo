<?php
/**
 * Description of Select
 * 
 * Created on 04/09/2016, ~22:09:29
 * @author Matheus Leonardo dos Santos Martins
 * @copyright (c) 2016, TRIAL
 * 
 * @version 1.0
 * @package SQL
 */

/* 
 * 21/01/2017
 *      03:19:19 => renamed namespace from SQL to Utils\SQL
 *      19:40:50 => renamed namespace from Utils\SQL to NoRedo\Utils\SQL
 *      23:25:11 => removed implementation of InsertClauses
 */

namespace NoRedo\Utils\SQL;

use \PDO;

class Insert extends Query {
    
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