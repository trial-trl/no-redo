<?php

/*
 * (c) 2018 TRIAL.
 * Created on 23/02/2018, 13:36:21.
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

namespace NoRedo\Database\Proxy;

/**
 *
 * @author Matheus Leonardo dos Santos Martins <mlsm@trialent.com>
 * @package \NoRedo\Database\Proxy
 */
interface IProxy {
  
  /**
   * 
   */
  public function get(int $model_id);
  
  /**
   * 
   * @param \NoRedo\Database\Model $model
   */
  public function insert(\NoRedo\Database\Model $model): int;
  
  /**
   * 
   * @param int   $model_id
   * @param array $changes
   */
  public function edit(int $model_id, array $changes): bool;
  
  /**
   * 
   * @param int $model_id
   */
  public function deleteById(int $model_id): bool;
  
  /**
   * 
   * @param \NoRedo\Database\Model $model
   */
  public function delete(\NoRedo\Database\Model $model);
  
}
