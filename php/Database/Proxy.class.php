<?php

/*
 * (c) 2018 TRIAL.
 * Created on 24/02/2018, 13:23:01.
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

namespace NoRedo\Database;

use NoRedo\Database\Model;

/**
 * Description of Proxy
 *
 * @copyright (c) 2018, TRIAL
 * @author Matheus Leonardo dos Santos Martins
 * 
 * @version 1.0
 * @package \NoRedo\Database
 */
class Proxy implements Proxy\IProxy {
  
  const ID = 'id';
  
  /**
   *
   * @var \atk4\dsql\Connection
   */
  private $conn;
  /**
   *
   * @var string
   */
  private $table;
  /**
   *
   * @var array
   */
  private $columns;
  
  /**
   * 
   * @param \atk4\dsql\Connection $conn
   * @param string $table
   */
  protected function __construct(\atk4\dsql\Connection $conn, string $table) {
    $this->conn = $conn;
    $this->table = $table;
    
    $columns = (new \ReflectionClass(static::class))->getConstants();
    if (isset($columns['TABLE'])) {
      unset($columns['TABLE']);
    }
    $this->columns = $columns;
  }
  
  /**
   * 
   * @return \atk4\dsql\Connection
   */
  public function getConnection(): \atk4\dsql\Connection {
    return $this->conn;
  }
  
  /**
   * 
   * @return string
   */
  public function getTable(): string {
    return $this->table;
  }
  
  public function getColumns(): array {
    return $this->columns;
  }

  public function get(int $model_id) {
    
  }

  public function edit(int $model_id, array $changes): bool {
    $q = $this->getConnection()->dsql()->table($this->getTable());
    foreach ($changes as $k => $v) {
      $q->set($k, $v);
    }
    return $q->where(Model::ID, $model_id)->update();
  }

  public function insert(Model $model): int {
    $q = $this->getConnection()->dsql()->table($this->getTable());
    return $this->getConnection()->atomic(function () use ($q, $model) {
      $put = $model->toArray();
      $unknown_columns = array_diff(
              array_keys($put), 
              array_values($this->getColumns())
      );
      if (!empty($unknown_columns)) {
        throw new \InvalidArgumentException('Unknown column \'' . implode(', ', array_keys($unknown_columns)) . '\' for table \'' . $this->getTable() . '\'');
      }
      foreach ($put as $c => $v) {
        $q->set($c, $v);
      }
      $q->insert();
      return (int) $this->getConnection()->connection()->lastInsertId();
    });
  }
  
  public function deleteById(int $model_id): bool {
    if ($model_id > 0) {
      return $this->getConnection()->atomic(function () use ($model_id) {
        $deleted = $this->getConnection()->dsql()->table($this->getTable())->where(Model::ID, $model_id)->delete();
        return $deleted->rowCount() >= 1;
      });
    }
    return false;
  }

  /**
   * 
   * @param Model $model
   * @return bool|int
   */
  public function delete(Model $model) {
    return 1;
  }

}
