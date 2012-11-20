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
		list($host, $port) = explode(":", $server);
		if(!($port > 0)){
			$port = 11211;
		}
		$this->mem->connect($host, $port);
		$this->values = $this->mem->get($file);
	}
	
	public function save(){
		$this->mem->set($this->file, $this->values, $this->expires);
	}
}
 