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
 * Description of ErrorBuilder
 *
 * @copyright (c) 2018, TRIAL
 * @author MLSM<mlsm@trialent.com>
 * 
 * @version 1.0
 * @package \NoRedo\Utils
 * @project No Redo
 */
class ErrorBuilder {
  
  const TO_JSON       = 'application/json';
  
  private $code       = 0;
  private $msg        = 'Error';
  private $stacktrace = [];
  
  public function type(int $code, string $msg) {
    $this->code = $code;
    $this->msg = $msg;
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
  
  public function throwError(int $status, string $to = self::TO_JSON): string {
    if (!in_array($to, [self::TO_JSON])) {
      throw new Exception('Unsupported format type');
    }
    http_response_code($status);
    echo $this->format($to);
    exit;
  }
  
  public function format(string $to = null) {
    $formatted = [
      'error' => [
        'code'       => $this->code,
        'msg'        => $this->msg
      ]
    ];
    if (!empty($this->stacktrace)) {
      $formatted['error']['stacktrace'] = $this->stacktrace;
    }
    if ($to === self::TO_JSON) {
      $formatted = json_encode($formatted);
    }
    return $formatted;
  }
  
  public function __toString() {
    return $this->format();
  }

}
