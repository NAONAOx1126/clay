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
		$wurflConfig = new WURFL_Configuration_InMemoryConfig();
		
		// ライブラリのファイルパスを設定
		$wurflConfig->wurflFile(CLAY_ROOT.DIRECTORY_SEPARATOR."libs".DIRECTORY_SEPARATOR."data".DIRECTORY_SEPARATOR."WURFL".DIRECTORY_SEPARATOR."wurfl-2.0.27.zip");
		
		// 判定モード設定
		$wurflConfig->matchMode('performance');
		
		// データ変更時の再読み込みを許可しない
		$wurflConfig->allowReload(false);
		
		// Setup WURFL Persistence
		$wurflConfig->persistence('file', array('dir' => CLAY_ROOT.DIRECTORY_SEPARATOR."cache".DIRECTORY_SEPARATOR."WURFLData"));
		
		// キャッシュのディレクトリを設定
		$wurflConfig->cache('file', array('dir' => CLAY_ROOT.DIRECTORY_SEPARATOR."cache".DIRECTORY_SEPARATOR."WURFL", 'expiration' => 36000));
		
		// WURFLのインスタンスを作成し、デバイス情報を取得
		$wurflManagerFactory = new WURFL_WURFLManagerFactory($wurflConfig);
		$wurflManager = $wurflManagerFactory->create();
		$requestingDevice = $wurflManager->getDeviceForHttpRequest($_SERVER);
		$_SERVER["CLIENT_DEVICE"] = $requestingDevice;
		// print_r($requestingDevice->getAllCapabilities());
	}
}
 