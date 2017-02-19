<?php
/**
 * Description of Update
 * 
 * Created on 08/09/2016, 12:54:20
 * @author Matheus Leonardo dos Santos Martins
 * @copyright (c) 2016, TRIAL
 * 
 * @version 1.0
 * @package SQL
 */

/* Finished last editions on 20/09/2016 19:57:43
 * 
 * 21/01/2017
 *      03:19:19 => renamed namespace from SQL to Utils\SQL
 *      19:40:50 => renamed namespace from Utils\SQL to NoRedo\Utils\SQL
 */

namespace NoRedo\Utils\SQL;

use \PDO;

class Update extends Query {
    
    /**
     * Values that'll replace ones inside table
     * 
     * @var array 
     */
    private $bind_where;
    /**
     * Columns that'll be returned in this query.
     * 
     * @var string 
     */
    private $columns;
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
     * ORDER BY clause that'll be used in this query.
     * 
     * @var string 
     */
    private $order_by;
    /**
     * Values that'll replace ones inside table
     * 
     * @var array 
     */
    private $set;
    /**
     * Main database table name that this query will run in.
     * 
     * @var string 
     */
    private $table;
    /**
     * WHERE clause that'll be used in this query.
     * 
     * @var string 
     */
    private $where;
    
    public function columns($columns) {
        $this->columns = $columns;
        return $this;
    }
    
    public function prepare() {
	$set = $this->prepareToBind($this->columns, true);
	$prepared = $this->prepareToBind($this->columns);
        $input_parameters = $this->prepareInputParameters($prepared, $this->set);
        foreach ($this->bind_where as $key => $bind) {
            $input_parameters[$key] = $bind;
        }
	$this->bind($input_parameters);
        $this->statement = $this->conn->prepare('UPDATE ' . $this->table . ' SET ' . implode(', ', $set) . ' ' . $this->where . ($this->order_by ? ' ' . $this->order_by : null) . ($this->limit ? ' ' . $this->limit : null));
    }

    public function run() : QueryResponse {
        $this->prepare();
        return new QueryResponse($this->statement, $this->bind(), function () {
            return true;
        });
    }

    public function table($table) {
        $this->table = $table;
        return $this;
    }
    
    public function values(array $set) {
        $this->set = $set;
        return $this;
    }
    
    public function valuesWhere(array $where) {
        $this->bind_where = $where;
        return $this;
    }
    
    public function limit($limit) {
        $this->limit = 'LIMIT ' . $limit;
        return $this;
    }
    
    public function orderBy($order_by) {
        $this->order_by = 'ORDER BY ' . (gettype($order_by) === 'array' ? implode(', ', $order_by) : $order_by);
        return $this;
    }
    
    public function where($where) {
        $this->where = 'WHERE ' . $where;
        return $this;
    }
    
    public function lowPriority($low_priority) {
        $this->option = $low_priority ? 'LOW_PRIORITY' : null;
        return $this;
    }
    
    public function ignore($ignore) {
        $this->option = $ignore ? 'IGNORE' : null;
        return $this;
    }

}