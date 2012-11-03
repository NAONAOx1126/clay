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

// システムのホームディレクトリを設定する。
$_SERVER["CLAY_ROOT"] = realpath(dirname(__FILE__)."/../../../");
define("CLAY_ROOT", $_SERVER["CLAY_ROOT"]);

// システムのURLホストパスを取得
$_SERVER["FRAMEWORK_URL_HOST"] = "http".((isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on")?"s":"")."://".$_SERVER["SERVER_NAME"];
define("FRAMEWORK_URL_HOST", $_SERVER["FRAMEWORK_URL_HOST"]);

// システムのURLのベースパスを取得
if(!empty($_SERVER["DOCUMENT_ROOT"])){
	if(substr($_SERVER["DOCUMENT_ROOT"], -1) == "/"){
		$_SERVER["DOCUMENT_ROOT"] = substr($_SERVER["DOCUMENT_ROOT"], 0, -1);
	}
}
$_SERVER["FRAMEWORK_URL_BASE"] = str_replace($_SERVER["DOCUMENT_ROOT"], "", CLAY_ROOT);
define("FRAMEWORK_URL_BASE", $_SERVER["FRAMEWORK_URL_BASE"]);

// システムのURLホームパスを取得
$_SERVER["FRAMEWORK_URL_HOME"] = FRAMEWORK_URL_HOST.FRAMEWORK_URL_BASE;
define("FRAMEWORK_URL_HOME", $_SERVER["FRAMEWORK_URL_HOME"]);

// 設定ファイルのパスを取得
$_SERVER["FRAMEWORK_CONFIGURE_HOME"] = CLAY_ROOT."/configure";
define("FRAMEWORK_CONFIGURE_HOME", $_SERVER["FRAMEWORK_CONFIGURE_HOME"]);

// グローバル設定の取得
require(FRAMEWORK_CONFIGURE_HOME."/configure.php");

// キャッシュファイルのパスを取得
$_SERVER["FRAMEWORK_CACHE_HOME"] = CLAY_ROOT."/cache";
define("FRAMEWORK_CACHE_HOME", $_SERVER["FRAMEWORK_CACHE_HOME"]);

// コンテンツファイルのパスを取得
$_SERVER["FRAMEWORK_CONTENTS_HOME"] = CLAY_ROOT."/contents";
define("FRAMEWORK_CONTENTS_HOME", $_SERVER["FRAMEWORK_CONTENTS_HOME"]);

// ログファイルのパスを取得
$_SERVER["FRAMEWORK_LOGS_HOME"] = CLAY_ROOT."/logs";
define("FRAMEWORK_LOGS_HOME", $_SERVER["FRAMEWORK_LOGS_HOME"]);

// ライブラリ関連のパスを取得
$_SERVER["FRAMEWORK_LIBRARY_HOME"] = CLAY_ROOT."/libs";
define("FRAMEWORK_LIBRARY_HOME", $_SERVER["FRAMEWORK_LIBRARY_HOME"]);

// 共通ライブラリ関連のパスを取得
$_SERVER["FRAMEWORK_COMMON_LIBRARY_HOME"] = FRAMEWORK_LIBRARY_HOME."/common";
define("FRAMEWORK_COMMON_LIBRARY_HOME", $_SERVER["FRAMEWORK_COMMON_LIBRARY_HOME"]);

// Smarty処理プログラムファイルのパスを取得
$_SERVER["FRAMEWORK_SMARTY_LIBRARY_HOME"] = FRAMEWORK_LIBRARY_HOME."/Smarty";
define("FRAMEWORK_SMARTY_LIBRARY_HOME", $_SERVER["FRAMEWORK_SMARTY_LIBRARY_HOME"]);

// PEARライブラリプログラムファイルのパスを取得
$_SERVER["FRAMEWORK_PEAR_LIBRARY_HOME"] = FRAMEWORK_LIBRARY_HOME."/PEAR";
define("FRAMEWORK_PEAR_LIBRARY_HOME", $_SERVER["FRAMEWORK_PEAR_LIBRARY_HOME"]);

// FPDFライブラリプログラムファイルのパスを取得
$_SERVER["FRAMEWORK_FPDF_LIBRARY_HOME"] = FRAMEWORK_LIBRARY_HOME."/FPDF";
define("FRAMEWORK_FPDF_LIBRARY_HOME", $_SERVER["FRAMEWORK_FPDF_LIBRARY_HOME"]);

// FPDFライブラリプログラムファイルのパスを取得
$_SERVER["FRAMEWORK_ZEND_LIBRARY_HOME"] = FRAMEWORK_LIBRARY_HOME."/Zend";
define("FRAMEWORK_ZEND_LIBRARY_HOME", $_SERVER["FRAMEWORK_ZEND_LIBRARY_HOME"]);

// フォントファイルのパスを取得
$_SERVER["FRAMEWORK_FONTS_HOME"] = FRAMEWORK_LIBRARY_HOME."/fonts";
define("FRAMEWORK_FONTS_HOME", $_SERVER["FRAMEWORK_FONTS_HOME"]);

// 共通クラスプログラムファイルのパスを取得
$_SERVER["FRAMEWORK_CLASS_LIBRARY_HOME"] = FRAMEWORK_COMMON_LIBRARY_HOME."/classes";
define("FRAMEWORK_CLASS_LIBRARY_HOME", $_SERVER["FRAMEWORK_CLASS_LIBRARY_HOME"]);

// プラグインのホームディレクトリ
if(!isset($_SERVER["FRAMEWORK_PLUGIN_HOME"])){
	$_SERVER["FRAMEWORK_PLUGIN_HOME"] = CLAY_ROOT."/../clay_plugins";
}
define("FRAMEWORK_PLUGIN_HOME", $_SERVER["FRAMEWORK_PLUGIN_HOME"]);
?>
