<?php
/**
 * 全てのスクリプト共通で読み込むスクリプトです。
 * このスクリプトで全ての設定を行います。
 *
 * @category  Common
 * @package   Settings
 * @author    Naohisa Minagawa <info@sweetberry.jp>
 * @copyright 2010-2012 Naohisa Minagawa
 * @license http://www.apache.org/licenses/LICENSE-2.0.html Apache License, Version 2.0
 * @version   1.0.0
 */

// カスタムクライアントのユーザーエージェントを補正
if(preg_match("/^CLAY-(.+)-CLIENT\\[(.+)\\]$/", $_SERVER["HTTP_USER_AGENT"], $params) > 0){
	$_SERVER["HTTP_USER_AGENT"] = "Mozilla/5.0 (Linux; U; Android 1.6; ja-jp; CLAY-ANDROID-CLIENT)";
	$_SERVER["USER_TEMPLATE"] = "/".strtolower($params[1]);
	$_SERVER["HTTP_X_DCMGUID"] = $params[2];
}

// デフォルトのインクルードパスを全て無効にする。
ini_set("include_path", ".");

// グローバルの定数定義ファイルを読み込み
require_once(dirname(__FILE__)."/settings/global_definition.php");

// 基本設定ファイルを読み込み
require(FRAMEWORK_COMMON_LIBRARY_HOME."/settings/basic.php");

// パーミッションチェックの実行
require(FRAMEWORK_COMMON_LIBRARY_HOME."/settings/permissions.php");

// サーバー別設定読み込み
require(FRAMEWORK_COMMON_LIBRARY_HOME."/settings/configure.php");

// 定数定義設定
require(FRAMEWORK_COMMON_LIBRARY_HOME."/settings/local_definition.php");

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
	Logger::writeError($message."(".$code.")", $ex);
	
	// カスタムエラーページのパス
	$path = "";
	if(defined("MODULE_HOME")){
		$path = MODULE_HOME.$_SERVER["USER_TEMPLATE"].DS."ERROR_".$code.".html";
	}

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
?>
