<?php

/*
 * (c) 2017 TRIAL.
 * Created on 07/03/2017, 16:20:51.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *      http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace NoRedo\Utils;

use \PDO,
        NoRedo\Utils\Database;

/**
 * Description of ConnectionManager
 *
 * @copyright (c) 2017, TRIAL
 * @author Matheus Leonardo dos Santos Martins
 * 
 * @version 1.0
 * @package Utils
 */
final class ConnectionManager {
    
    private function __construct() {}
    
    /**
     * Returns an connection by ID. If no connection has been estabilished, a new one is created to $database with given ID.
     * 
     * @param string $uniq_id ID of connection to be got or created (if no one has been estabilished yet)
     * @param string $database Database to connect if no connection has been estabilished yet
     * @return PDO Estabilished connection
     * @since 1.0
     */
    public static function get(string $uniq_id, string $database = null) : PDO {
        $uniq_id = 'con' . $uniq_id;
        if (!isset($GLOBALS[$uniq_id])) {
            if (empty($database)) {
                throw new \InvalidArgumentException('Database name is empty');
            }
            $GLOBALS[$uniq_id] = Database::connect($database);
        }
        return $GLOBALS[$uniq_id];
    }
    
}
