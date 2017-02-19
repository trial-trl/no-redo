<?php

/*
 * (c) 2017 TRIAL.
 * Created on 19/01/2017, 15:29:41.
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

/**
 * Interface to implement CRUD operations
 *
 * @copyright (c) 2017, TRIAL
 * @author Matheus Leonardo dos Santos Martins
 * 
 * @version 1.0
 * @package NoRedo
 */
interface CRUD {
    
    public static function create($object) : self;
    public static function get($object) : self;
    public static function edit(array $object) : self;
    public static function delete($object) : self;
    
}
