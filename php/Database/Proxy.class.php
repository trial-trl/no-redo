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
   * @var string
   */
  private $alias = null;
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
  public function getTable(bool $with_alias = false): string {
    return $this->table . ($with_alias ? ' ' . $this->getAlias(false) : null);
  }
  
  /**
   * 
   * @return array
   */
  public function getColumns(): array {
    return $this->columns;
  }
  
  /**
   * 
   * @param bool $append_dot
   * @return type
   */
  protected function getAlias(bool $append_dot = true) {
    return $this->alias ? ($this->alias . ($append_dot ? '.' : null)) : null;
  }
  
  /**
   * 
   * @param array $columns
   * @param bool $add_alias
   * @param bool $strict
   * @return type
   * @throws \InvalidArgumentException
   */
  protected function parseColumns(array $columns = [], bool $add_alias = true, bool $strict = true) {
    if ($strict === true && !empty($columns)) {
      $unknown_columns = array_diff(
                                        array_values($columns), 
                                        array_values($this->getColumns())
                         );
      if (!empty($unknown_columns)) {
        throw new \InvalidArgumentException('Unknown column \'' . implode(', ', array_keys($unknown_columns)) . '\' for table \'' . $this->getTable() . '\'');
      }
    } else {
      $columns = array_values($this->getColumns());
    }
    if ($this->hasAlias() && $add_alias === true) {
      return preg_replace('/^/', $this->getAlias(), $columns);
    }
    return $columns;
  }
  
  /**
   * 
   * @param string $column
   * @return string
   */
  protected function col(string $column) {
    return $this->getAlias() . $column;
  }
  
  /**
   * 
   * @param string $alias
   */
  protected function addAlias(string $alias = null) {
    if (!empty($alias)) {
      if ($alias === 'table') {
        $this->alias = $this->getTable();
      } else {
        $this->alias = $alias;
      }
    } else {
      $table_parts = explode('_', $this->table);
      $alias = '';
      foreach ($table_parts as $part) {
        $alias .= $part[0];
      }
      $this->alias = $alias;
    }
    if (!empty($alias)) {
      $this->alias = str_replace('.', '', strtolower($this->alias));
    } else {
      $this->removeAlias();
    }
  }
  
  /**
   * 
   */
  protected function removeAlias() {
    $this->alias = null;
  }
  
  /**
   * 
   * @return bool
   */
  protected function hasAlias(): bool {
    return !empty($this->alias);
  }
  
  /**
   * 
   * @param type $model
   * @param callable $cb
   * @param array $columns
   * @return type
   */
  public function get($model, callable $cb = null, array $columns = []) {
    $model_id = $this->getIdentifier($model);
    $dsql = $this->getConnection()->dsql();
    $columns = $this->parseColumns($columns);
    return $this->getConnection()->atomic(function () use ($dsql, $model_id, $cb, $columns) {
      $q = $dsql->table($this->getTable(), $this->getAlias(false))
                ->field($columns)
                ->where($this->getAlias() . self::ID, $model_id);
      if (is_callable($cb)) {
        $cb($q);
      }
      $data = $q->getRow();
      if (!empty($data)) {
        return static::fetchDataIntoModel($data);
      }
      return null;
    });
  }

  /**
   * 
   * @param callable $cb
   * @param array $columns
   * @param array $opts
   * @return array
   */
  public function getAll(callable $cb = null, array $columns = [], array $opts = []): array {
    $dsql = $this->getConnection()->dsql();
    $columns = $this->parseColumns($columns);
    return $this->getConnection()->atomic(function () use ($dsql, $cb, $columns, $opts) {
      $models = [];
      $q = $dsql->table($this->getTable(), $this->getAlias(false))
                ->field($columns);
      static::_applyOptions($q, $opts);
      if (is_callable($cb)) {
        $cb($q);
      }
      $data = $q->get();
      foreach ($data as $model) {
        $models[] = static::fetchDataIntoModel($model);
      }
      return $models;
    });
  }
  
  /**
   * 
   * @param type $model
   * @param array $changes
   * @return bool
   */
  public function edit($model, array $changes): bool {
    $model_id = $this->getIdentifier($model);
    $q = $this->getConnection()->dsql()->table($this->getTable());
    foreach ($changes as $k => $v) {
      $q->set($k, $v);
    }
    $update = $q->where(self::ID, $model_id)->update();
    return $update->rowCount() > 0;
  }

  /**
   * 
   * @param Model $model
   * @return int
   * @throws \InvalidArgumentException
   */
  public function insert($model): int {
    if (!($model instanceof Model) && !is_array($model)) {
      throw new \InvalidArgumentException;
    }
    $q = $this->getConnection()->dsql()->table($this->getTable());
    return $this->getConnection()->atomic(function () use ($q, $model) {
      $put = $model instanceof Model ? $model->toDatabase() : $model;
      $this->parseColumns($put, false);
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
   * @param \atk4\dsql\Query $query
   * @param array $opts
   */
  protected function _applyOptions(\atk4\dsql\Query &$query, array &$opts = []) {
    static::_parseOptions($opts);
    if (!empty($opts[Model::QUERY_IGNORE_ID])) {
      $query->where($this->getAlias() . self::ID, $query->expr('NOT IN ([ids])', ['ids' => implode(', ', $opts[Model::QUERY_IGNORE_ID])]));
    }
    $query->limit($opts[Model::QUERY_LIMIT], $opts[Model::QUERY_LIMIT] * $opts[Model::QUERY_OFFSET]);
  }
  
  /**
   * 
   * @param array $opts
   */
  protected function _parseOptions(array &$opts) {
    $opts[Model::QUERY_IGNORE_ID] = isset($opts[Model::QUERY_IGNORE_ID]) && (is_int($opts[Model::QUERY_IGNORE_ID]) || is_array($opts[Model::QUERY_IGNORE_ID])) 
                                    ? $opts[Model::QUERY_IGNORE_ID]
                                    : null;
    $opts[Model::QUERY_LIMIT]     = isset($opts[Model::QUERY_LIMIT]) && is_int($opts[Model::QUERY_LIMIT])
                                    ? $opts[Model::QUERY_LIMIT] 
                                    : 20;
    $opts[Model::QUERY_OFFSET]    = isset($opts[Model::QUERY_OFFSET]) && is_int($opts[Model::QUERY_OFFSET]) 
                                    ? $opts[Model::QUERY_OFFSET] 
                                    : 0;
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
   * @param array $columns              <b>Optional</b>
   *                                    Columns to generate
   * @param string $alias              <b>Optional</b>
   *                                    Alias to add before each column
   * @return string                     The string Json-formatted
   * @throws \InvalidArgumentException  If any column isn't declared in proxy class
   * 
   * Created on 16/02/2020 11:11:10
   */
  public function generateJson(array $columns = [], string $alias = null): string {
    $columns = $this->parseColumns($columns, false);
    if (!empty($alias)) {
      $this->addAlias($alias);
    }
    
    $cols = [];
    foreach ($columns as $key => $value) {
      if (is_string($key)) {
        $column_name = $key;
      } else {
        $column_name = $value;
        $value = null;
      }
      $column_query = $this->getAlias() . $column_name;
      
      $col = '"' . $column_name . '": \', ';
      
      if (empty($value)) {
        $col .= 'IF(' . $column_query . ' IS NOT NULL, CONCAT(\'"\', ' . $column_query . ', \'"\'), \'null\')';
      } else {
        if ($value instanceof self) {
          $col .= $value->generateJson();
        } else if (is_string($value) && strpos('CONCAT', $value) === 0) {
          $col .= $value;
        } else if (is_string($value)) {
          $col .= '\'"' . $value . '"\'';
        } else {
          $col .= '\'' . $value . '\'';
        }
      }
      
      $col .= ', \'';
      
      $cols[] = $col;
    }
    
    $json  = 'CONCAT(\'{';
    $json .= implode(', ', $cols);
    $json .= '}\')';
    
    return $json;
  }
  
  /**
   * 
   * @param array $data
   * @since 1.01
   */
  abstract public static function fetchDataIntoModel(array $data): Model;

}