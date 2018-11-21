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
 *
 * @copyright (c) 2018, TRIAL
 * @author MLSM<mlsm@trialent.com>
 * 
 * @version 1.01
 * @package \NoRedo\Database
 */
abstract class Proxy implements Proxy\IProxy {
  
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

  public function get($model) {
    $model_id = $this->getIdentifier($model);
    $dsql = $this->getConnection()->dsql();
    return $this->getConnection()->atomic(function () use ($dsql, $model_id) {
      $data = $dsql->table($this->getTable())->field(array_values($this->getColumns()))->where(self::ID, $model_id)->getRow();
      if (!empty($data)) {
        return static::fetchDataIntoModel($data);
      }
      return null;
    });
  }

  public function edit($model, array $changes): bool {
    $model_id = $this->getIdentifier($model);
    $q = $this->getConnection()->dsql()->table($this->getTable());
    foreach ($changes as $k => $v) {
      $q->set($k, $v);
    }
    $update = $q->where(self::ID, $model_id)->update();
    return true;//$update->rowCount() >= 1;
  }

  public function insert($model): int {
    if (!($model instanceof Model) && !is_array($model)) {
      throw new \InvalidArgumentException;
    }
    $q = $this->getConnection()->dsql()->table($this->getTable());
    return $this->getConnection()->atomic(function () use ($q, $model) {
      $put = $model instanceof Model ? $model->toDatabase() : $model;
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
  
  /**
   * 
   * @param Model|int $model
   * @return bool
   */
  public function delete($model): bool {
    $model_id = $this->getIdentifier($model);
    if ($model_id > 0) {
      return $this->getConnection()->atomic(function () use ($model_id) {
        $deleted = $this->getConnection()->dsql()->table($this->getTable())->where(self::ID, $model_id)->delete();
        return $deleted->rowCount() >= 1;
      });
    }
    return false;
  }
  
  /**
   * 
   * @throws \InvalidArgumentException
   * 
   * Created on 06/06/2018 15:48:08
   */
  protected function getIdentifier($model): int {
    $is_obj = $model instanceof Model;
    $is_int = is_int($model) || is_numeric($model);
    if (!$is_obj && !$is_int) {
      throw new \InvalidArgumentException;
    }
    return (int) ($is_obj ? $model->getId() : $model);
  }
  
  /**
   * 
   * @param array $data
   * @since 1.01
   */
  abstract public static function fetchDataIntoModel(array $data): Model;

}