<?php
/**
 * This file is part of CLAY Framework for view-module based system.
 *
 * @author    Naohisa Minagawa <info@clay-system.jp>
 * @copyright Copyright (c) 2010, Naohisa Minagawa
 * @license http://www.apache.org/licenses/LICENSE-2.0.html Apache License, Version 2.0
 * @since PHP 5.3
 * @version   3.0.0
 */

/**
 * エラーページを表示する関数。
 *
 * @params integer $code エラーコード
 * @params string $message エラーメッセージ
 * @params Exception $ex エラーの原因となった例外オブジェクト
 */
function showHttpError($code, $message, $ex = null){
	ob_end_clean();
	
	// エラーログに書き込み
	Clay_Logger::writeError($message."(".$code.")", $ex);
	
	// カスタムエラーページのパス
	$path = $_SERVER["CONFIGURE"]->site_home.$_SERVER["USER_TEMPLATE"].DIRECTORY_SEPARATOR."ERROR_".$code.".html";

	// ファイルがある場合はエラーページを指定ファイルで出力
	if(file_exists($path)){
		try{
			header("HTTP/1.0 ".$code." ".$message, true, $code);
			header("Status: ".$code." ".$message);
			header("Content-Type: text/html; charset=utf-8");
			$_SERVER["TEMPLATE"]->display("ERROR_".$code.".html");
		}catch(Exception $e){
			// エラーページでのエラーは何もしない
		}
	}else{
		// エラーページが無い場合はデフォルト
		header("HTTP/1.0 ".$code." ".$message, true, $code);
		header("Status: ".$code." ".$message);
		header("Content-Type: text/html; charset=utf-8");
		echo $message;
	}
	exit;
}

// デフォルトのインクルードパスを全て無効にする。
ini_set("include_path", ".");

// フレームワークのシステムを起動する
require_once(dirname(__FILE__)."/classes/Clay.php");

// Zendのライブラリを読み込む
require_once(dirname(__FILE__)."/classes/PHPExcel.php");

// WURFLのライブラリを読み込む
require_once(dirname(__FILE__)."/classes/WURFL.php");

// PHP Excelのライブラリを読み込む
require_once(dirname(__FILE__)."/classes/PHPExcel.php");

Clay::startup();
