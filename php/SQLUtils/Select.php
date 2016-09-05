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

require_once 'Query.php';
require_once 'SelectClauses.php';

class Select extends Query implements SelectClauses {
    
    /**
     * Columns that'll be returned in this query.
     * 
     * @var string 
     */
    public $columns;
    /**
     * Defines the construct args of determined class when the query result will be fetched into him.
     * 
     * @var string
     */
    public $construct_args;
    /**
     * GROUP BY clause that'll be used in this query.
     * 
     * @var string 
     */
    public $group_by;
    /**
     * INNER JOIN clause that'll be used in this query.
     * 
     * @var string 
     */
    public $inner_join;
    /**
     * LEFT JOIN clause that'll be used in this query.
     * 
     * @var string 
     */
    public $left_join;
    /**
     * ORDER BY clause that'll be used in this query.
     * 
     * @var string 
     */
    public $order_by;
    /**
     * RIGHT JOIN clause that'll be used in this query.
     * 
     * @var string 
     */
    public $right_join;
    /**
     * Main database table name that this query will run in.
     * 
     * @var string 
     */
    public $table;
    /**
     * Defines the name of class the query result will be fetched into.
     * 
     * @var string
     */
    public $to_class;
    /**
     * Values.
     * 
     * @var string
     */
    public $values;
    /**
     * WHERE clause that'll be used in this query.
     * 
     * @var string 
     */
    public $where;
    
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

    public function rightJoin($right_join) {
        $this->right_join = 'RIGHT JOIN ' . (gettype($right_join) === 'array' ? implode(' RIGHT JOIN ', $right_join) : $right_join);
        return $this;
    }

    public function run() {
        $response = [];
        $shd = $this->conn->prepare("SELECT $this->columns FROM $this->table $this->inner_join $this->right_join $this->left_join $this->where $this->order_by $this->group_by");
        $shd->execute($this->values);
        if ($shd->rowCount()) {
            if ($this->to_class != null) {
                if ($this->construct_args != null) {
                    $response = $shd->fetchAll(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, $this->to_class, $this->construct_args);
                } else {
                    $response = $shd->fetchAll(PDO::FETCH_CLASS, $this->to_class);
                }
            } else {
                $count = 0;
                while ($r = $shd->fetch(PDO::FETCH_ASSOC)) {
                    $response[$count] = $r;
                    $count++;
                }
                $response['total'] = $shd->rowCount();
            }
        }
        return $response;
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