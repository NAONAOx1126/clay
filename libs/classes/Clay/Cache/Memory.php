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
 * memcacheによるデータキャッシュクラスです。
 *
 * @package Cache
 * @author Naohisa Minagawa <info@clay-system.jp>
 */
class Clay_Cache_Memory extends Clay_Cache_Base{
	private $mem;
	
	public function __construct($server, $file, $expires){
		$this->init($server, $file, $expires);
	}
	
	public function init($server, $file, $expires = 3600){
		parent::init($server, $file, $expires);
		$this->mem = new Memcache();
		if(strpos($_SERVER["CONFIGURE"]->MEMCACHED_SERVER, ":") > 0){
			list($host, $port) = explode(":", $_SERVER["CONFIGURE"]->MEMCACHED_SERVER);
		}else{
			$host = $_SERVER["CONFIGURE"]->MEMCACHED_SERVER;
			$port = 0;
		}
		if(!($port > 0)){
			$port = 11211;
		}
		$this->mem->connect($host, $port);
		$this->values = unserialize($this->mem->get($server.":".$file));
	}
	
	public function save(){
		$this->mem->set($this->server.":".$this->file, serialize($this->values), 0, $this->expires);
	}
}
 