<?php

/*
 * (c) 2018 TRIAL.
 * Created on 23/02/2018, 20:05:41.
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

use \DateTime,
    NoRedo\Database\Proxy;

/**
 *
 * @copyright (c) 2018, TRIAL
 * @author MLSM<mlsm@trialent.com>
 * 
 * @version 1.0
 * @package Model
 */
abstract class Model implements \JsonSerializable {

  const QUERY_IGNORE_ID = 'ignore_id';
  const QUERY_LIMIT = 'limit';
  const QUERY_OFFSET = 'offset';
  
  /** @var int */
  private $id;
  /** @var Proxy */
  private $proxy;
  /** @var array */
  private $original = [];
  /** @var array */
  private $changes = [];
  
  /**
   * 
   * @param string|object $proxy  Model's Proxy
   * @param int $id               Model's ID
   * 
   * @throws \UnexpectedValueException
   * @throws \InvalidArgumentException  
   */
  protected function __construct($proxy, int $id = null) {
    $is_class_name = is_string($proxy);
    if ($is_class_name && !class_exists($proxy)) {
      throw new \UnexpectedValueException;
    }
    $this->setProxy($is_class_name ? new $proxy : $proxy);
    if (!($this->getProxy() instanceof Proxy)) {
      throw new \InvalidArgumentException;
    }
    if (is_int($id)) {
      $this->setId($id);
    }
  }
  
  /**
   * Sets the proxy to be used by model
   * 
   * @param Proxy $proxy Proxy
   */
  private function setProxy(Proxy $proxy) {
    $this->proxy = $proxy;
  }
  
  /**
   * Sets model ID
   * 
   * @param int $id Model's ID
   * @throws \InvalidArgumentException if <i>id</i> is less than or equal 0
   */
  protected function setId(int $id) {
    if ($id <= 0) {
      throw new \InvalidArgumentException;
    }
    $this->original[Proxy::ID] = $this->id = $id;
    return $this;
  }
  
  /**
   * Gets used proxy
   * 
   * @return DatabaseProxy Model's proxy
   */
  protected function getProxy() {
    return $this->proxy;
  }

  /**
   * Gets model ID
   * 
   * @return int
   */
  public function getId(): int {
    return $this->id ?? 0;
  }
  
  /**
   * 
   * @throws \InvalidArgumentException
   * 
   * Created on 16/02/2020 02:24:34
   */
  protected static function parseValue(&$value) {
    if (is_string($value) && ($value[0] === '{' || $value[0] === '[')) {
      $tmp = json_decode($value, true);
      if (json_last_error() == JSON_ERROR_NONE) {
        $raw = ($value[0] === '{' ? static::getIdentifier($value) : null) ?? $value;
        $value = $tmp;
      }
    } else if ($value instanceof DateTime || (is_string($value) && (bool) strtotime($value))) {
      $raw = static::parseDate($value);
    }
    return $raw ?? null;
  }
  
  /**
   * 
   * @throws \InvalidArgumentException
   * 
   * Created on 16/02/2020 02:24:34
   */
  protected static function getIdentifier($model) {
    if (is_array($model) && !empty($model[Proxy::ID])) {
      return $model[Proxy::ID];
    } else if ($model instanceof self) {
      return $model->getId();
    }
    return null;
  }
  
  /**
   * @param string $column    Proxy-column related to property
   * @param mixed  $property  Model's property to be assigned the value
   * @param mixed  $value     Value to be assigned
   */
  protected function set(string $column, &$property, $value) {
    $put = static::parseValue($value);
    if (!$this->inDatabase() || !array_key_exists($column, $this->original)) {
      $this->original[$column] = $put ?? $value;
    } else {
      $this->changes[$column] = $put ?? $value;
    }
    $property = $value;
  }
  
  public function inDatabase(): bool {
    return !empty($this->getId());
  }
  
  /**
   * 
   * @return array
   */
  public function getBackup(): array {
    return $this->original;
  }
  
  /**
   * 
   * @return array
   */
  public function getChanges(): array {
    return !empty($this->changes) ? $this->changes : $this->original;
  }
  
  /**
   * Reverts model state to it's original state
   */
  public function revert() {
    foreach ($this->changes as $k => $v) {
      $this->revertColumn($k, $this->original[$k]);
    }
    $this->changes = [];
  }
  
  /**
   * Save this model to database
   * <p>
   * If this model is in database, it'll be updated, else, it'll be inserted
   * </p>
   * 
   * @return bool <b>TRUE</b> if inserted/updated successfully, <b>FALSE</b> otherwise
   */
  public final function save(): bool {
    if (!$this->inDatabase()) {
      $id = $this->insert();
      if ($id > 0) {
        $this->setId($id);
      }
      return $id > 0;
    } else {
      return $this->update();
    }
  }
  
  /**
   * Insert this model to database
   * 
   * @return int Inserted ID
   */
  protected function insert(): int {
    return $this->getProxy()->insert($this);
  }
  
  /**
   * Send the changes made in this model to database
   * 
   * @return bool <b>TRUE</b> if updated successfully, <b>FALSE</b> otherwise
   */
  protected function update(): bool {
    return $this->getProxy()->edit($this->getId(), $this->getChanges());
  }
  
  /**
   * Delete this model from database
   * 
   * @return bool <b>FALSE</b> if model isn't in database or occurred a 
   *              failure on delete, <b>TRUE</b> if successfully deleted
   */
  public function delete() : bool {
    if ($this->inDatabase()) {
      return $this->getProxy()->deleteById($this->getId());
    }
    return false;
  }
  
  /**
   * 
   * @param DateTime|string $obj
   * @return string
   * @throws \InvalidArgumentException
   */
  protected static function parseDate(&$obj): string {
    $is_datetime = $obj instanceof DateTime;
    $is_str      = is_string($obj);
    if ($is_str) {
      $str = $obj;
      $str = preg_replace('/\b\d{4}-\d{2}-\d{2}\b/', 'Y-m-d', $str);
      $str = preg_replace('/\b\d{2}\/\d{2}\/\d{4}\b/', 'm/d/Y', $str);
      $str = preg_replace('/\b\d{2}\.\d{2}\.\d{4}\b/', 'd.m.Y', $str);
      $str = preg_replace('/\b\d{2}:\d{2}\b:\d{2}\b/', 'H:i:s', $str);
      $str = preg_replace('/\b\d{2}:\d{2}\b/', 'H:i', $str);
      $str = preg_replace('/\.\d{3}\b/', '.v', $str);
      $obj = DateTime::createFromFormat($str, $obj);
    } else if ($is_datetime) {
      $str = $obj->format('Y-m-d H:i:s');
    } else {
      throw new \InvalidArgumentException();
    }
    return $str;
  }
  
  /**
   * Returns this model as array
   * <p>
   * Models that are extending {@link DatabaseModel} needs to override this 
   * method to support return it's data as array as in example below:
   * </p>
   * <p>
   * public function toArray(): array {<br />
   * &nbsp;&nbsp;&nbsp;&nbsp;return <i>[data to return]</i>;<br />
   * }
   * </p>
   * 
   * @return array Model as array
   */
  public final function toArray(): array {
    return array_merge([Proxy::ID => $this->getId()], $this->__toArray());
  }
  
  /**
   * Returns this model as database-compatible data
   * <p>
   * Models that are extending {@link DatabaseModel} needs to override this 
   * method to support return it's database-compatible data as in example below:
   * </p>
   * <p>
   * public function toDatabase(): array {<br />
   * &nbsp;&nbsp;&nbsp;&nbsp;return <i>[data to return]</i>;<br />
   * }
   * </p>
   * 
   * @return array Model as array
   */
  public final function toDatabase(): array {
    $model = $this->toArray();
    array_walk($model, function (&$v, $k) use (&$model) {
      $this->__filterToDatabase($v, $k, $model);
      if ($v === '') {
        $v = null;
      } else if ($v === null) {
        unset($model[$k]);
      }
    });
    return $model;
  }
  
  public function jsonSerialize() {
    return $this->toArray();
  }
  
  protected function __toArray(): array {
    return [];
  }
  
  /**
   * Callback function for filtering data before using it to manipulate database.
   * 
   * <p>To <b>unset</b> some entry from array, set $value to null.</p>
   * <p>To keep some entry, but with <b>NULL</b> value, set $value to empty string.</p>
   * 
   * @param mixed $value Value of array entry
   * @param mixed $key   Key of array entry
   * @param array $arr   Entries
   */
  protected function __filterToDatabase(&$value, $key, array &$arr) {}
  
  abstract protected function revertColumn(string $column, $old);
  
}