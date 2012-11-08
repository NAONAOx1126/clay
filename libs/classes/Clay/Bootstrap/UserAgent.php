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
 * ユーザーエージェントの情報取得の起動処理です。
 * 
 * @package Bootstrap
 * @author Naohisa Minagawa <info@clay-system.jp>
 */
class Clay_Bootstrap_UserAgent{
	public static function start(){
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
 