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
 * @copyright (c) 2018, TRIAL
 * @author MLSM<mlsm@trialent.com>
 * 
 * @version 1.01
 * @package \NoRedo\Database\Proxy
 */
interface IProxy {
  
  /**
   * 
   * @param \NoRedo\Database\Model|int $model
   * 
   * @since 1.0
   * @since 1.01 signature changed
   */
  public function get($model);
  
  /**
   * 
   * @param \NoRedo\Database\Model|array $model
   * 
   * @since 1.0
   * @since 1.01 signature changed
   */
  public function insert($model): int;
  
  /**
   * 
   * @param \NoRedo\Database\Model|int   $model
   * @param array $changes
   * 
   * @since 1.0
   * @since 1.01 signature changed
   */
  public function edit($model, array $changes): bool;
  
  /**
   * 
   * @param \NoRedo\Database\Model|int $model
   * 
   * @since 1.0
   * @since 1.01 signature changed
   */
  public function delete($model);
  
}
