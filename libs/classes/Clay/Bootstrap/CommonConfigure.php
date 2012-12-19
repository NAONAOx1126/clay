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
 * 共通設定読み込み用の起動処理です。
 * 
 * @package Bootstrap
 * @author Naohisa Minagawa <info@clay-system.jp>
 */
class Clay_Bootstrap_CommonConfigure{
	public static function start(){
		// SERVER_NAMEが未設定の場合はlocalhostを割当
		if(!isset($_SERVER["SERVER_NAME"])){
			$_SERVER["SERVER_NAME"] = "localhost";
		}
		
		// 共通設定ファイルを読み込み
		if(file_exists(CLAY_ROOT.DIRECTORY_SEPARATOR."configure".DIRECTORY_SEPARATOR."configure_".$_SERVER["SERVER_NAME"].".php")){
			require(CLAY_ROOT.DIRECTORY_SEPARATOR."configure".DIRECTORY_SEPARATOR."configure_".$_SERVER["SERVER_NAME"].".php");
		}else{
			require(CLAY_ROOT.DIRECTORY_SEPARATOR."configure".DIRECTORY_SEPARATOR."configure.php");
		}
	}
}
 