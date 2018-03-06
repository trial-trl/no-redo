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

use NoRedo\Database\Proxy;

/**
 * Description of Model
 *
 * @copyright (c) 2018, TRIAL
 * @author Matheus Leonardo dos Santos Martins
 * 
 * @version 1.0
 * @package Model
 */
abstract class Model {
  
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
   * @param string $column    Proxy-column related to property
   * @param mixed  $property  Model's property to be assigned the value
   * @param mixed  $value     Value to be assigned
   */
  protected function set(string $column, &$property, $value) {
    $property = $value;
    if (!$this->inDatabase() || !array_key_exists($column, $this->original)) {
      $this->original[$column] = $value;
    } else {
      $this->changes[$column] = $value;
    }
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
    return $this->changes;
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
   * Returns this model as array
   * <p>
   * Models that are extending {@link DatabaseModel} needs to override this 
   * method to support return it's data as array as in example below:
   * </p>
   * <p>
   * public function toArray(): array {<br />
   * &nbsp;&nbsp;&nbsp;&nbsp;return array_merge(<i>data to return</i>, parent::toArray());<br />
   * }
   * </p>
   * 
   * @return array Model as array
   */
  public function toArray(): array {
    return [Proxy::ID => $this->getId()];
  }
  
  abstract protected function revertColumn(string $column, $old);
  
}
