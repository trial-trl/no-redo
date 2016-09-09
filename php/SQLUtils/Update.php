<?php
/**
 * Description of Delete
 * 
 * Created on 05/09/2016, ~18:23:18
 * @author Matheus Leonardo dos Santos Martins
 * @copyright (c) 2016, TRIAL
 * 
 * @package SQLUtils
 */

require_once 'Query.php';
require_once 'DeleteClauses.php';
require_once 'QueryResponse.php';

class Delete extends Query implements DeleteClauses {
    
    /**
     * Columns that'll be returned in this query.
     * 
     * @var string 
     */
    private $columns;
    /**
     * GROUP BY clause that'll be used in this query.
     * 
     * @var string 
     */
    public $group_by;
    /**
     * Limit rows.
     * 
     * @var string
     */
    private $limit;
    /**
     * Option of DELETE query. Must be one of these values: LOW_PRIORITY | QUICK | IGNORE.
     * 
     * @var string
     */
    private $option;
    /**
     * Main database table name that this query will run in.
     * 
     * @var string 
     */
    private $table;
    /**
     * Values.
     * 
     * @var string
     */
    private $values;
    /**
     * WHERE clause that'll be used in this query.
     * 
     * @var string 
     */
    public $where;
    
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
        $this->statement = $this->conn->prepare('DELETE' . ($this->option != null ? ' ' . $this->option : null) . ' FROM ' . $this->table . ($this->where != null ? ' ' . $this->where : null) . ($this->order_by != null ? ' ' . $this->order_by : null) . ($this->limit != null ? ' ' . $this->limit : null));
    }

    public function run() {
        $this->prepare();
        return new QueryResponse($this->statement, $this->bind(), function () {
            return true;
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
    
    public function limit($limit) {
        $this->limit = 'LIMIT ' . $limit;
        return $this;
    }
    
    public function where($where) {
        $this->where = 'WHERE ' . $where;
        return $this;
    }
    
    public function groupBy($group_by) {
        $this->group_by = 'GROUP BY ' . $group_by;
        return $this;
    }
    
    public function lowPriority($low_priority) {
        $this->option = $low_priority ? 'LOW_PRIORITY' : null;
        return $this;
    }
    
    public function quick($quick) {
        $this->option = $quick ? 'QUICK' : null;
        return $this;
    }
    
    public function ignore($ignore) {
        $this->option = $ignore ? 'IGNORE' : null;
        return $this;
    }

}