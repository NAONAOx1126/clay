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
 * ファイルによるデータキャッシュクラスです。
 *
 * @package Cache
 * @author Naohisa Minagawa <info@clay-system.jp>
 */
class Clay_Cache_File extends Clay_Cache_Base{
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
		$filename = CLAY_CACHE_ROOT.DIRECTORY_SEPARATOR.$this->server.DIRECTORY_SEPARATOR.$this->file.".php";
		if(file_exists($filename) && time() < fileatime($filename) + $this->expires){
			require_once($filename);
		}
	}
	
	protected function save(){
		if(!is_dir(CLAY_CACHE_ROOT)){
			mkdir(CLAY_CACHE_ROOT);
		}
		if(!is_dir(CLAY_CACHE_ROOT.DIRECTORY_SEPARATOR.$this->server)){
			mkdir(CLAY_CACHE_ROOT.DIRECTORY_SEPARATOR.$this->server);
		}
		if(($fp = fopen(CLAY_CACHE_ROOT.DIRECTORY_SEPARATOR.$this->server.DIRECTORY_SEPARATOR.$this->file.".php", "w+")) !== FALSE){
			fwrite($fp, "<"."?php\r\n");
			foreach($this->values as $key => $value){
				fwrite($fp, '$this->values["'.$key.'"] = '.var_export($value, TRUE).";\r\n");
			}
			fwrite($fp, "?".">\r\n");
			fclose($fp);
		}
	}
}
