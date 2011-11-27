<?php
/**
 * WEBからのアクセスを受け取るためのメインPHPです。
 *
 * @category  Web
 * @package   Main
 * @author    Naohisa Minagawa <info@sweetberry.jp>
 * @copyright 2010-2012 Naohisa Minagawa
 * @license http://www.apache.org/licenses/LICENSE-2.0.html Apache License, Version 2.0
 * @version   1.0.0
 */

// 出力を抑制
ob_start();

try{
	// 共通のライブラリの呼び出し。
	include(dirname(__FILE__)."/libs/common/require.php");
	
	$_SERVER["TEMPLATE"] = new Template();
	
	$_SERVER["TEMPLATE"]->assign("u", FRAMEWORK_URL_BASE);
	foreach($_SERVER as $name =>$value){
		$_SERVER["TEMPLATE"]->assign($name, $value);
	}
	try{
		$_SERVER["TEMPLATE"]->display(substr($_SERVER["TEMPLATE_NAME"], 1));
	}catch(Exception $ex){
		showHttpError("404", "Not Found", $ex);
	}
	
	// 抑制していた出力内容を出力
	Logger::writeTimer("END");
	ob_end_flush();
}catch(Exception $ex){
	ob_end_clean();
	echo $ex->getMessage();
	Logger::writeTimer("END");
}
?>
