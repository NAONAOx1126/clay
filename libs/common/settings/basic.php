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

// エラーメッセージを限定させる。
if(!isset($_SERVER["CONFIGURE"]->DISPLAY_ERROR)){
	$_SERVER["CONFIGURE"]->DISPLAY_ERROR = "Off";
}
if($_SERVER["CONFIGURE"]->DEBUG){
	if(defined("E_DEPRECATED")){
		error_reporting(E_ALL ^ E_DEPRECATED);
	}else{
		error_reporting(E_ALL);
	}
	ini_set('display_errors', $_SERVER["CONFIGURE"]->DISPLAY_ERROR);
	ini_set('log_errors', 'On');
}else{
	error_reporting(E_ERROR);
	ini_set('display_errors', $_SERVER["CONFIGURE"]->DISPLAY_ERROR);
	ini_set('log_errors', 'On');
}

// デフォルトのタイムゾーンを設定する。
if($_SERVER["CONFIGURE"]->TIMEZONE != ""){
	date_default_timezone_set($_SERVER["CONFIGURE"]->TIMEZONE);
}

// デフォルトのロケールを設定する。
if($_SERVER["CONFIGURE"]->LOCALE != ""){
	setlocale(LC_ALL, $_SERVER["CONFIGURE"]->LOCALE); 
}

// 引数にセッションIDが指定された場合、セッションIDを上書き
if(!empty($_GET[session_name()])){
	session_id($_GET[session_name()]);
	unset($_GET[session_name()]);
}

// HTTPのパラメータを統合する。（POST優先）
foreach($_POST as $name => $value){
	$_GET[$name] = $value;
}

// input-imageによって渡されたパラメータを展開
$inputImageKeys = array();
foreach($_GET as $name => $value){
	if(preg_match("/^(.+)_([xy])$/", $name, $params) > 0){
		$inputImageKeys[$params[1]][$params[2]] = $value;
	}
}
foreach($inputImageKeys as $key => $inputImage){
	if(isset($inputImage["x"]) && isset($inputImage["y"])){
		$_GET[$key] = $inputImage["x"].",".$inputImage["y"];
		unset($_GET[$key."_x"]);
		unset($_GET[$key."_y"]);
	}
}
$_POST = $_GET;

// PEARのパスをinclude_pathに追加
set_include_path(get_include_path().PATH_SEPARATOR.FRAMEWORK_PEAR_LIBRARY_HOME);

// FPDFのパスをinclude_pathに追加
set_include_path(get_include_path().PATH_SEPARATOR.FRAMEWORK_FPDF_LIBRARY_HOME);

// Zendのパスをinclude_pathに追加
set_include_path(get_include_path().PATH_SEPARATOR.FRAMEWORK_ZEND_LIBRARY_HOME);

// 共通で使用するクラスをインクルード
require(FRAMEWORK_CLASS_LIBRARY_HOME."/Template.php");
require(FRAMEWORK_CLASS_LIBRARY_HOME."/ImageFilter.php");

// REQUEST URIから実際に出力するテンプレートファイルを特定
$_SERVER["TEMPLATE_NAME"] = str_replace("?".$_SERVER["QUERY_STRING"], "", $_SERVER["REQUEST_URI"]);
if(FRAMEWORK_URL_BASE != ""){
	if(strpos($_SERVER["TEMPLATE_NAME"], FRAMEWORK_URL_BASE) === 0){
		$_SERVER["TEMPLATE_NAME"] = substr($_SERVER["TEMPLATE_NAME"], strlen(FRAMEWORK_URL_BASE));
	}	
}
?>
