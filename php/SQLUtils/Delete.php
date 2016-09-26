<?php
/**
 * Description of Delete
 * 
 * Created on 05/09/2016, 18:23:18
 * @author Matheus Leonardo dos Santos Martins
 * @copyright (c) 2016, TRIAL
 * 
 * @package SQLUtils
 * 
 * 26/09/2016, 00:00:00: columns() removed
 */

require_once 'Query.php';
require_once 'DeleteClauses.php';
require_once 'QueryResponse.php';

class Delete extends Query implements DeleteClauses {
    
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
     * GROUP BY clause that'll be used in this query.
     * 
     * @var string 
     */
    private $order_by;
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
    private $where;
    
    public function columns($columns) {
        return $this;
    }

    public function prepare() {
        $this->statement = $this->conn->prepare('DELETE' . ($this->option != null ? ' ' . $this->option : null) . ' FROM ' . $this->table . ($this->where != null ? ' ' . $this->where : null) . ($this->order_by != null ? ' ' . $this->order_by : null) . ($this->limit != null ? ' ' . $this->limit : null));
    }

    public function run() : QueryResponse {
        $this->prepare();
        return new QueryResponse($this->statement, $this->values, function () {
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
    
    public function orderBy($order_by) {
        $this->order_by = 'ORDER BY ' . $order_by;
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