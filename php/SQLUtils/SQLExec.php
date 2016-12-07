<?php
/**
 * Description of SQLExec
 * 
 * Created on 24/09/2016, 23:51:04
 * @author Matheus Leonardo dos Santos Martins
 * @copyright (c) 2016, TRIAL
 * 
 * @package SQLUtils
 */

namespace SQL;

require_once 'CommonQuery.php';

class SQLExec extends CommonQuery {
    
    /**
     * 14/10/2016, 02:35:46:
     *      added FETCH_ASSOC option
     * 
     * @param string $exec
     * @return \QueryResponse
     */
    public function run(string $exec) {
        $this->statement = $this->conn->prepare($exec);
        return new QueryResponse($this->statement, null, function () {
            $response = [];
            $count_rows = $this->statement->rowCount();
            if ($count_rows) {
                if (strpos($this->statement->queryString, 'INSERT') === false && strpos($this->statement->queryString, 'UPDATE') === false) {
                    $response = $this->statement->fetchAll(PDO::FETCH_ASSOC);
                } else {
                    $response['id'] = $this->conn->lastInsertId();
                }
                $response['total'] = $count_rows;
            }
            return $response;
        });
    }
    
}
