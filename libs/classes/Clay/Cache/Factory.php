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
		if(class_exists("Memcache") && !empty($_SERVER["CONFIGURE"]->MEMCACHED_SERVER)){
			return new Clay_Cache_Memory($_SERVER["CONFIGURE"]->MEMCACHED_SERVER, $file, $expires);
		}else{
			return new Clay_Cache_File($_SERVER["SERVER_NAME"], $file, $expires);
		}
	}
}
 