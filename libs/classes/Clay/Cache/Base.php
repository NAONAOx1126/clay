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
 * データキャッシュ用のインターフェイス
 * @package Cache
 * @author Naohisa Minagawa <info@clay-system.jp>
 */
abstract class Clay_Cache_Base{
	protected $values;
	
	public abstract function init($server, $file, $expires);
	
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
}
