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
    
    // 14/04/2017, 19:44:18 - 20:13:10 => added support to insert multiple rows in a single query
    public function prepare() {
	$prepared = $this->prepareToBind($this->columns, ($contain_array_values = is_array($this->values[0])) ? count($this->values) : false);
	$this->bind($this->prepareInputParameters($prepared, $this->values));
        if ($contain_array_values) {
            foreach ($prepared as $k => $v) {
                $binded[$k] = '(' . implode(', ', $v) . ')';
            }
            $binded = implode(', ', $binded);
        } else {
            $binded = '(' . implode(', ', $prepared) . ')';
        }
        $this->statement = $this->conn->prepare('INSERT INTO ' . $this->table . ' ' . ($this->columns != null ? '(' . $this->columns . ') ' : null) . 'VALUES ' . $binded);
    }

    // added ROW_COUNT() on 14/04/2017, 19:12:45
    public function run() : QueryResponse {
        $this->prepare();
        return new QueryResponse($this->statement, $this->bind(), function () {
            return ['id' => $this->conn->lastInsertId(), 'n' => (new SQLExec($this->conn))->run("SELECT ROW_COUNT() AS n")->getResult()[0]['n']];
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