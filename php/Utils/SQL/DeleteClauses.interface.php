<?php
/**
 * Description of DeleteClauses
 *
 * Created on 04/09/2016, ~18:50:30
 * @author Matheus Leonardo dos Santos Martins
 * @copyright (c) 2016, TRIAL
 * 
 * @package SQLUtils
 */

/*
 * 21/01/2017
 *      19:40:50 => added namespace NoRedo\Utils\SQL
 */

namespace NoRedo\Utils\SQL;

interface DeleteClauses {
    
    /**
     * 
     * 
     * @param bool $low_priority 
     * @return \Delete
     */
    public function lowPriority($low_priority);
    
    /**
     * 
     * 
     * @param bool $quick 
     * @return \Delete
     */
    public function quick($quick);
    
    /**
     * 
     * 
     * @param bool $ignore 
     * @return \Delete
     */
    public function ignore($ignore);
    
    /**
     * 
     * 
     * @param string $limit 
     * @return \Delete
     */
    public function limit($limit);
    
    /**
     * 
     * 
     * @param array|string $order_by 
     * @return \Delete
     */
    public function orderBy($order_by);
    
    /**
     * 
     * 
     * @param string $where 
     * @return \Delete
     */
    public function where($where);
    
}
