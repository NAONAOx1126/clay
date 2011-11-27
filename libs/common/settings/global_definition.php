<?php
/**
 * システム上のグローバルな定数の設定を行うためのスクリプトです。
 *
 * @category  Common
 * @package   Settings
 * @author    Naohisa Minagawa <info@sweetberry.jp>
 * @copyright 2010-2012 Naohisa Minagawa
 * @license http://www.apache.org/licenses/LICENSE-2.0.html Apache License, Version 2.0
 * @since PHP 5.3
 * @version   1.0.0
 */

// デフォルトパッケージ名を設定
define("DEFAULT_PACKAGE_NAME", "Base");

// PHPのバージョンIDを設定する。
if (!defined('PHP_VERSION_ID')) {
    $version = explode('.', PHP_VERSION);
    define('PHP_VERSION_ID', ($version[0] * 10000 + $version[1] * 100 + $version[2]));
}

// システムのホームディレクトリを設定する。
$_SERVER["FRAMEWORK_HOME"] = realpath(dirname(__FILE__)."/../../../");
define("FRAMEWORK_HOME", $_SERVER["FRAMEWORK_HOME"]);

// システムのURLホストパスを取得
$_SERVER["FRAMEWORK_URL_HOST"] = "http".(($_SERVER["HTTPS"] == "on")?"s":"")."://".$_SERVER["SERVER_NAME"];
define("FRAMEWORK_URL_HOST", $_SERVER["FRAMEWORK_URL_HOST"]);

// システムのURLのベースパスを取得
if(!empty($_SERVER["DOCUMENT_ROOT"])){
	if(substr($_SERVER["DOCUMENT_ROOT"], -1) == "/"){
		$_SERVER["DOCUMENT_ROOT"] = substr($_SERVER["DOCUMENT_ROOT"], 0, -1);
	}
}
$_SERVER["FRAMEWORK_URL_BASE"] = str_replace($_SERVER["DOCUMENT_ROOT"], "", FRAMEWORK_HOME);
define("FRAMEWORK_URL_BASE", $_SERVER["FRAMEWORK_URL_BASE"]);

// システムのURLホームパスを取得
$_SERVER["FRAMEWORK_URL_HOME"] = FRAMEWORK_URL_HOST.FRAMEWORK_URL_BASE;
define("FRAMEWORK_URL_HOME", $_SERVER["FRAMEWORK_URL_HOME"]);

// 設定ファイルのパスを取得
$_SERVER["FRAMEWORK_CONFIGURE_HOME"] = FRAMEWORK_HOME."/configure";
define("FRAMEWORK_CONFIGURE_HOME", $_SERVER["FRAMEWORK_CONFIGURE_HOME"]);

// キャッシュファイルのパスを取得
$_SERVER["FRAMEWORK_CACHE_HOME"] = FRAMEWORK_HOME."/cache";
define("FRAMEWORK_CACHE_HOME", $_SERVER["FRAMEWORK_CACHE_HOME"]);

// コンテンツファイルのパスを取得
$_SERVER["FRAMEWORK_CONTENTS_HOME"] = FRAMEWORK_HOME."/contents";
define("FRAMEWORK_CONTENTS_HOME", $_SERVER["FRAMEWORK_CONTENTS_HOME"]);

// ログファイルのパスを取得
$_SERVER["FRAMEWORK_LOGS_HOME"] = FRAMEWORK_HOME."/logs";
define("FRAMEWORK_LOGS_HOME", $_SERVER["FRAMEWORK_LOGS_HOME"]);

// ライブラリ関連のパスを取得
$_SERVER["FRAMEWORK_LIBRARY_HOME"] = FRAMEWORK_HOME."/libs";
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

// フォントファイルのパスを取得
$_SERVER["FRAMEWORK_FONTS_HOME"] = FRAMEWORK_LIBRARY_HOME."/fonts";
define("FRAMEWORK_FONTS_HOME", $_SERVER["FRAMEWORK_FONTS_HOME"]);

// 共通クラスプログラムファイルのパスを取得
$_SERVER["FRAMEWORK_CLASS_LIBRARY_HOME"] = FRAMEWORK_COMMON_LIBRARY_HOME."/classes";
define("FRAMEWORK_CLASS_LIBRARY_HOME", $_SERVER["FRAMEWORK_CLASS_LIBRARY_HOME"]);

// プラグインのホームディレクトリ
$_SERVER["FRAMEWORK_PLUGIN_HOME"] = FRAMEWORK_HOME."/plugins";
define("FRAMEWORK_PLUGIN_HOME", $_SERVER["FRAMEWORK_PLUGIN_HOME"]);

// グローバル設定の取得
require(FRAMEWORK_CONFIGURE_HOME."/global.php");
?>