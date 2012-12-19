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
 