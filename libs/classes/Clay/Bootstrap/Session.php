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
 * セッションを初期化するための起動処理です。
 * 
 * @package Bootstrap
 * @author Naohisa Minagawa <info@clay-system.jp>
 */
class Clay_Bootstrap_Session{
	public static function start(){
		// セッション管理クラスをインクルード
		switch($_SERVER["CONFIGURE"]->get("SESSION_MANAGER")){
			case "":
				ini_set("session.save_handler", "files");
				break;
			case "memcached":
				ini_set("session.save_handler", "memcache");
				ini_set("session.save_path", "localhost:11211");	
				break;
			default:
				ini_set("session.save_handler", "user");
				$manager = "Clay_Session_Handler_".str_replace("SessionHandler", "", $_SERVER["CONFIGURE"]->get("SESSION_MANAGER"));
				Clay_Session_Manager::create(new $manager());
				break;
		}
	}
}
 