<?php
/**
 * Description of Select
 * 
 * Created on 04/09/2016, ~18:00:50 - ~18:12:11
 * @author Matheus Leonardo dos Santos Martins
 * @copyright (c) 2016, TRIAL
 * 
 * @package SQLUtils
 */

require_once __DIR__ . '/Query.php';
require_once __DIR__ . '/SelectClauses.php';

class Select extends Query implements SelectClauses {
    
    /**
     * Columns that'll be returned in this query.
     * 
     * @var string 
     */
    private $columns;
    /**
     * Defines the construct args of determined class when the query result will be fetched into him.
     * 
     * @var string
     */
    private $construct_args;
    /**
     * Fetch args.
     * 
     * @var array 
     */
    private $fetch_mode;
    /**
     * GROUP BY clause that'll be used in this query.
     * 
     * @var string 
     */
    private $group_by;
    /**
     * INNER JOIN clause that'll be used in this query.
     * 
     * @var string 
     */
    private $inner_join;
    /**
     * LEFT JOIN clause that'll be used in this query.
     * 
     * @var string 
     */
    private $left_join;
    /**
     * ORDER BY clause that'll be used in this query.
     * 
     * @var string 
     */
    private $order_by;
    /**
     * RIGHT JOIN clause that'll be used in this query.
     * 
     * @var string 
     */
    private $right_join;
    /**
     * Main database table name that this query will run in.
     * 
     * @var string 
     */
    private $table;
    /**
     * Defines the name of class the query result will be fetched into.
     * 
     * @var string
     */
    private $to_class;
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
    
    private $no_response = false; 
    
    public function columns($columns) {
        /*if (gettype($columns) === 'string') {
            $columns = explode(',', preg_replace('/\s+/', '', $columns));
        }*/
        if (gettype($columns) === 'array') {
            $columns = implode(',', $columns);
        }
        $this->columns = $columns;
        return $this;
    }

    public function fetchMode($mode, $object = null, $construct = null) {
        $this->fetch_mode = ['mode' => $mode, 'object' => $object, 'construct' => $construct];
        return $this;
    }

    public function groupBy($group_by) {
        $this->group_by = 'GROUP BY ' . (gettype($group_by) === 'array' ? implode(', ', $group_by) : $group_by);
        return $this;
    }

    public function innerJoin($inner_join) {
        $this->inner_join = 'INNER JOIN ' . (gettype($inner_join) === 'array' ? implode(' INNER JOIN ', $inner_join) : $inner_join);
        return $this;
    }

    public function leftJoin($left_join) {
        $this->left_join = 'LEFT JOIN ' . (gettype($left_join) === 'array' ? implode(' LEFT JOIN ', $left_join) : $left_join);
        return $this;
    }

    public function orderBy($order_by) {
        $this->order_by = 'ORDER BY ' . implode(', ', $order_by);
        return $this;
    }
    
    public function prepare() {
        $this->statement = $this->conn->prepare("SELECT $this->columns FROM $this->table $this->inner_join $this->right_join $this->left_join $this->where $this->order_by $this->group_by");
    }

    public function rightJoin($right_join) {
        $this->right_join = 'RIGHT JOIN ' . (gettype($right_join) === 'array' ? implode(' RIGHT JOIN ', $right_join) : $right_join);
        return $this;
    }
    
    private function setFetchMode() {
        if ($this->fetch_mode['mode'] && $this->fetch_mode['object'] && $this->fetch_mode['construct']) {
            $this->statement->setFetchMode($this->fetch_mode['mode'], $this->fetch_mode['object'], $this->fetch_mode['construct']);
        } else if ($this->fetch_mode['mode'] && $this->fetch_mode['object']) {
            $this->statement->setFetchMode($this->fetch_mode['mode'], $this->fetch_mode['object']);
        } else if ($this->fetch_mode['mode']) {
            $this->statement->setFetchMode($this->fetch_mode['mode']);
        } else {
            $this->statement->setFetchMode(PDO::FETCH_ASSOC);
        }
    }
    
    public function noResponse() {
        $this->no_response = true;
        return $this;
    }

    public function run() {
        $this->prepare();
        $this->setFetchMode();
        return new QueryResponse(&$this->statement, &$this->values, !$this->no_response ? function () {
            $response = [];
            $count_rows = $this->statement->rowCount();
            if ($count_rows) {
                $response = $this->statement->fetchAll();
                if ($this->fetch_mode['mode'] == null) {
                    $response['total'] = $count_rows;
                }
            }
            return $response;
        } : null);
    }

    public function table($table) {
        $this->table = gettype($table) === 'array' ? implode(', ', $table) : $table;
        return $this;
    }
    
    public function toClass($class, array $construct_args) {
        $this->to_class = $class;
        $this->construct_args = $construct_args;
        return $this;
    }
    
    public function values(array $values) {
        $this->values = $values;
        return $this;
    }

    public function where($where) {
        $this->where = 'WHERE ' . $where;
        return $this;
    }

}