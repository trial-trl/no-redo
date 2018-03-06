<?php

/*
 * (c) 2018 TRIAL.
 * Created on 18/01/2018, 21:40:54.
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
 * Description of BuildError
 *
 * @copyright (c) 2018, TRIAL
 * @author Matheus Leonardo dos Santos Martins
 * 
 * @version 1.0
 * @package \NoRedo\Utils
 * @project No Redo
 */
class BuildError {
  
  const MALFORMED_REQUEST = '1000:Malformed request';
  
  const TO_JSON = 'application/json';
  
  private $error_code = 0;
  private $error = '';
  private $stacktrace = [];
  
  public function type(string $type) {
    if (!in_array($type, [self::MALFORMED_REQUEST])) {
      throw new Exception('Type is unknown');
    }
    $e = explode(':', $type);
    $this->error_code = $e[0];
    $this->error = $e[1];
  }
  
  public function add(string $at, string $cause) {
    if (!empty($this->stacktrace[$at])) {
      $this->stacktrace[$at][] = $cause;
      return;
    }
    $this->stacktrace[$at] = [$cause];
  }
  
  public function has(): bool {
    return !empty($this->stacktrace);
  }
  
  public function format(string $to): string {
    if (!in_array($to, [self::TO_JSON])) {
      throw new Exception('Unsupported format type');
    }
    
    $formatted = '';
    
    if ($to === self::TO_JSON) {
      $formatted = json_encode([
        'error' => $this->error,
        'error_code' => $this->error_code,
        'stacktrace' => $this->stacktrace
      ]);
    }
    
    return $formatted;
  }
  
  public function __toString() {
    return json_encode($this);
  }

}
