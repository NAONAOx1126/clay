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
		// PHPのバージョンIDを設定する。
		if (!defined('PHP_VERSION_ID')) {
		    $version = explode('.', PHP_VERSION);
		    define('PHP_VERSION_ID', ($version[0] * 10000 + $version[1] * 100 + $version[2]));
		}
		
		// システムのルートディレクトリを設定
		if (!defined('CLAY_ROOT')) {
			define('CLAY_ROOT', realpath(dirname(__FILE__).DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR.".."));
		}
		
		// クラスライブラリのディレクトリを設定
		if (!defined('CLAY_CLASSES_ROOT')) {
			define('CLAY_CLASSES_ROOT', realpath(dirname(__FILE__)));
			// ライブラリのクラス自動ローダーを初期化する。
			require(CLAY_CLASSES_ROOT.DIRECTORY_SEPARATOR."Clay".DIRECTORY_SEPARATOR."Autoloader.php");
			Clay_Autoloader::register();
		}

		// キャッシュのディレクトリを設定
		if (!defined('CLAY_CACHE_ROOT')) {
			define('CLAY_CACHE_ROOT', realpath(CLAY_ROOT.DIRECTORY_SEPARATOR."cache"));
		}
		
		// 起動処理を追加
		
		register_shutdown_function(array("Clay", "shutdown"));
	}
	
	/**
	 * フレームワークの終了処理を行うメソッドです。
	 */
	public static function shutdown(){
		
	}
}
