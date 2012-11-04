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
 