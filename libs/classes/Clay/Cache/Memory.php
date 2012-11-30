<?php
/**
 * This file is part of CLAY Framework for view-module based system.
 *
 * @author    Naohisa Minagawa <info@clay-system.jp>
 * @copyright Copyright (c) 2010, Naohisa Minagawa
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
 