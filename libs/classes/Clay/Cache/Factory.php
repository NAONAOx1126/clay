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
 * データキャッシュファクトリクラス
 * @package Cache
 * @author Naohisa Minagawa <info@clay-system.jp>
 */
class Clay_Cache_Factory{
	public static function create($file, $expires = 3600){
		$server = $_SERVER["SERVER_NAME"];
		if(class_exists("Memcached")){
			return new Clay_Cache_Memory($server, $file, $expires);
		}else{
			return new Clay_Cache_File($server, $file, $expires);
		}
	}
}
 