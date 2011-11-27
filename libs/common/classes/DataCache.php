<?php
/**
 * キー - 値型のキャッシュクラスをまとめたファイルです。
 *
 * @category  Common
 * @package   Exception
 * @author    Naohisa Minagawa <info@sweetberry.jp>
 * @copyright 2010-2012 Naohisa Minagawa
 * @license http://www.apache.org/licenses/LICENSE-2.0.html Apache License, Version 2.0
 * @since PHP 5.3
 * @version   1.0.0
 */

/**
 * データキャッシュファクトリクラス
 * @package Cache
 * @author Naohisa Minagawa <info@sweetberry.jp>
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
 * @author Naohisa Minagawa <info@sweetberry.jp>
 */
abstract class DataCache{
	public abstract function init($server, $file, $expires);
	
	public abstract function import($values);
	
	public abstract function get($key);
	
	public abstract function set($key, $value);
	
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
 * @author Naohisa Minagawa <info@sweetberry.jp>
 */
class MemoryDataCache extends DataCache{
	private $expires;
	
	private $mem;
	
	public function __construct($server, $file, $expires){
		$this->init($server, $file, $expires);
	}
	
	public function init($server, $file, $expires){
		$this->expires = $expires;
		$this->mem = new Memcached($server.":".$file);
	}
	
	public function import($values){
		foreach($values as $key => $value){
			$this->set($key, $value);
		}
	}
	
	public function set($key, $value){
		$this->mem->set($key, $value, 0, $this->expires);
	}
	
	public function get($key){
		return $this->mem->get($key);
	}
}

/**
 * ファイルによるデータキャッシュクラスです。
 *
 * @package Cache
 * @author Naohisa Minagawa <info@sweetberry.jp>
 */
class FileDataCache extends DataCache{
	private $server;
	
	private $file;
	
	private $expires;
	
	private $values;
	
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
	
	private function save(){
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
		}
	}
	
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
}