<?php
/**
 * Copyright (C) 2012 Clay System All Rights Reserved.
 * 
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 * 
 * http://www.apache.org/licenses/LICENSE-2.0
 * 
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 * @author    Naohisa Minagawa <info@clay-system.jp>
 * @copyright Copyright (c) 2010, Clay System
 * @license http://www.apache.org/licenses/LICENSE-2.0.html Apache License, Version 2.0
 * @since PHP 5.3
 * @version   4.0.0
 */

/**
 * データキャッシュ用のインターフェイス
 * @package Cache
 * @author Naohisa Minagawa <info@clay-system.jp>
 */
abstract class Clay_Cache_Base{
	protected $values;
	
	protected $server;
	
	protected $file;
	
	protected $expires;
	
	public function init($server, $file, $expires){
		$this->server = $server;
		$this->file = $file;
		$this->expires = $expires;
	}
	
	protected abstract function save();
	
	public function import($values){
		foreach($values as $key => $value){
			$this->values[$key] = $value;
		}
		$this->save();
	}
	
	public function set($key, $value){
		$this->values[$key] = $value;
		$this->save();
	}
	
	public function get($key){
		if(isset($this->values[$key])){
			return $this->values[$key];
		}
		return "";
	}
	
	public function __get($key){
		return $this->get($key);
	}
	
	public function __set($key, $value){
		$this->set($key, $value);
	}
	
	public function __isset($name){
		return isset($this->values[$name]);
	}
}
