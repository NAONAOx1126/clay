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

// デフォルトパッケージ名を設定
define("DEFAULT_PACKAGE_NAME", "Base");

/**
 * フレームワークの起点となるクラス
 * 
 * @package Base
 * @author Naohisa Minagawa <info@clay-system.jp>
 */
class Clay{
	/**
	 * フレームワークの起動処理を行うメソッドです。
	 */
	public static function startup(){
		// システムのルートディレクトリを設定
		if (!defined('CLAY_ROOT')) {
			define('CLAY_ROOT', realpath(dirname(__FILE__).DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR.".."));
		}
		
		// システムのルートURLへのサブディレクトリを設定
		if (!defined('CLAY_SUBDIR')) {
			define('CLAY_SUBDIR', str_replace($_SERVER["DOCUMENT_ROOT"], "", CLAY_ROOT));
		}
		
		// システムのルートURLを設定
		if (!defined('CLAY_URL')) {
			define('CLAY_URL', "http".((isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on")?"s":"")."://".$_SERVER["SERVER_NAME"].CLAY_SUBDIR);
		}

		// クラスライブラリのディレクトリを設定
		if (!defined('CLAY_PLUGINS_ROOT')) {
			if(is_dir(realpath(CLAY_ROOT.DIRECTORY_SEPARATOR."plugins".DIRECTORY_SEPARATOR))){
				define('CLAY_PLUGINS_ROOT', realpath(CLAY_ROOT.DIRECTORY_SEPARATOR."plugins".DIRECTORY_SEPARATOR));
			}else{
				define('CLAY_PLUGINS_ROOT', realpath(CLAY_ROOT.DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."clay_plugins".DIRECTORY_SEPARATOR));
			}
		}

		// クラスライブラリのディレクトリを設定
		if (!defined('CLAY_CLASSES_ROOT')) {
			define('CLAY_CLASSES_ROOT', realpath(dirname(__FILE__)));
		}

		// ライブラリのクラス自動ローダーを初期化する。
		require(CLAY_CLASSES_ROOT.DIRECTORY_SEPARATOR."Clay".DIRECTORY_SEPARATOR."Autoloader.php");
		Clay_Autoloader::register();
		
		// 後起動処理を追加
		Clay_Bootstrap_PhpVersion::start();
		Clay_Bootstrap_CommonConfigure::start();
		Clay_Bootstrap_ErrorMessage::start();
		Clay_Bootstrap_Timezone::start();
		Clay_Bootstrap_Locale::start();
		Clay_Bootstrap_UserAgent::start();
		Clay_Bootstrap_SessionId::start();
		Clay_Bootstrap_Parameter::start();
		Clay_Bootstrap_Configure::start();
		Clay_Bootstrap_Session::start();
		Clay_Bootstrap_TemplateName::start();
		Clay_Bootstrap_CheckPermission::start();
			
		register_shutdown_function(array("Clay", "shutdown"));
	}
	
	/**
	 * フレームワークの終了処理を行うメソッドです。
	 */
	public static function shutdown(){
		
	}
}
