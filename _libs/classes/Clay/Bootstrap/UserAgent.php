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
 * ユーザーエージェントの情報取得の起動処理です。
 * 
 * @package Bootstrap
 * @author Naohisa Minagawa <info@clay-system.jp>
 */
class Clay_Bootstrap_UserAgent{
	public static function start(){
		// ユーザーエージェントが存在しない場合はダミーを設定
		if(!isset($_SERVER["HTTP_USER_AGENT"])){
			$_SERVER["HTTP_USER_AGENT"] = "Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/28.0.1500.52 Safari/537.36";
		}
		
		// カスタムクライアントのユーザーエージェントを補正
		if(preg_match("/^CLAY-(.+)-CLIENT\\[(.+)\\]$/", $_SERVER["HTTP_USER_AGENT"], $params) > 0){
			$_SERVER["HTTP_USER_AGENT"] = "Mozilla/5.0 (Linux; U; Android 1.6; ja-jp; CLAY-ANDROID-CLIENT)";
			$_SERVER["USER_TEMPLATE"] = "/".strtolower($params[1]);
			$_SERVER["HTTP_X_DCMGUID"] = $params[2];
		}
		
		// UA解析用のライブラリの初期設定
		$mobileInfo = Clay_Mobile::create();
		$_SERVER["CLIENT_DEVICE"] = $mobileInfo;
		// print_r($mobileInfo);
	}
}
 