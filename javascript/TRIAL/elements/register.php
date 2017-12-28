<?php

/* 
 * (c) 2017 TRIAL.
 * Created on 18/06/2017, 23:32:09.
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

header('Content-Type', 'application/x-javascript');
$trl_elements = explode(',', filter_input($_SERVER['REQUEST_METHOD'] === 'GET' ? INPUT_GET : INPUT_POST, 'elements'));
$contents = '';
foreach ($trl_elements as $trl_element) {
    $contents .= file_get_contents($trl_element . '.js');
}
echo $contents;