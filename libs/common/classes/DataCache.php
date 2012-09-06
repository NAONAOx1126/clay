<?php
/**
 * This file is part of CLAY Framework for view-module based system.
 *
 * @author    Naohisa Minagawa <info@clay-system.jp>
 * @copyright Copyright (c) 2010, Naohisa Minagawa
 * @license http://www.apache.org/licenses/LICENSE-2.0.html Apache License, Version 2.0
 * @since PHP 5.3
 * @version   3.0.0
 */

/**
 * データキャッシュファクトリクラス
 * @package Cache
 * @author Naohisa Minagawa <info@clay-system.jp>
 */
class DataCacheFactory{
	public static function create($file, $expires = 3600){
		$server = $_SERVER["SERVER_NAME"];
		if(class_exists("Memcached")){
			return new MemoryDataCache($server, $file, $expires);
		}else{
			return new FileDataCache($server, $file, $expires);
		}
	}
}

/**
 * データキャッシュ用のインターフェイス
 * @package Cache
 * @author Naohisa Minagawa <info@clay-system.jp>
 */
abstract class DataCache{
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

/**
 * memcachedによるデータキャッシュクラスです。
 *
 * @package Cache
 * @author Naohisa Minagawa <info@clay-system.jp>
 */
class MemoryDataCache extends DataCache{
	private $server;
	
	private $file;
	
	private $expires;
	
	private $mem;
	
	public function __construct($server, $file, $expires){
		$this->init($server, $file, $expires);
	}
	
	public function init($server, $file, $expires){
		$this->server = $server;
		$this->file = $file;
		$this->expires = $expires;
		$this->mem = new Memcached($server);
		$this->mem->addServer("localhost", 11211);
		$this->values = $this->mem->get($server.":".$file);
	}
	
	public function save(){
		$this->mem->set($this->server.":".$this->file, $this->values, $this->expires);
	}
}

/**
 * ファイルによるデータキャッシュクラスです。
 *
 * @package Cache
 * @author Naohisa Minagawa <info@clay-system.jp>
 */
class FileDataCache extends DataCache{
	private $server;
	
	private $file;
	
	private $expires;
	
	public function __construct($server, $file, $expires){
		$this->init($server, $file, $expires);
	}
	
	public function init($server, $file, $expires){
		$this->server = $server;
		$this->file = $file;
		$this->expires = $expires;
		$filename = FRAMEWORK_HOME."/cache/".$this->server."/".$this->file.".php";
		if(file_exists($filename) && time() < fileatime($filename) + $this->expires){
			require_once($filename);
		}
	}
	
	protected function save(){
		if(!is_dir(FRAMEWORK_HOME."/cache")){
			mkdir(FRAMEWORK_HOME."/cache");
		}
		if(!is_dir(FRAMEWORK_HOME."/cache/".$this->server)){
			mkdir(FRAMEWORK_HOME."/cache/".$this->server);
		}
		if(($fp = fopen(FRAMEWORK_HOME."/cache/".$this->server."/".$this->file.".php", "w+")) !== FALSE){
			fwrite($fp, "<"."?php\r\n");
			foreach($this->values as $key => $value){
				fwrite($fp, '$this->values["'.$key.'"] = '.var_export($value, TRUE).";\r\n");
			}
			fwrite($fp, "?".">\r\n");
			fclose($fp);
		}
	}
}