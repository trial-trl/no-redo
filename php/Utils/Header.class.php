<?php

/*
 * (c) 2018 TRIAL.
 * Created on 18/01/2018, 22:39:43.
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

/**
 * Description of Header
 *
 * @copyright (c) 2018, TRIAL
 * @author MLSM<mlsm@trialent.com>
 * 
 * @version 1.0
 * @package \NoRedo\Utils
 * @project No Redo
 */
class Header {
  
  private $name;
  private $value;
  
  /**
   * 
   * @param string $name
   * @param string $default
   */
  public function __construct(string $name, string $default = '') {
    $this->name = $name;
    $this->value = $_SERVER[$name] ?? $default;
  }

  /**
   * Created on 18/01/2018 21:20:19 as global isHeader(string $header, string $value) function
   * Moved to Header class as is(string $value) method on 18/02/2018 22:43:10
   * 
   * @param string|array $value
   * @return bool
   */
  public function is($value, bool $match_all = false): bool {
    $matches = [];
    
    if (is_array($value)) {
      
      foreach ($value as $v) {
        $m = strpos($this->value, (string) $v) !== false;
        if ($m && !$match_all) {
          return true;
        }
        $matches[] = $m;
      }
      
      if ($match_all) {
        return !in_array(false, $matches, true);
      }
      
    } else {
      return strpos($this->value, (string) $value) !== false;
    }
    
    return false;
  }
  
}