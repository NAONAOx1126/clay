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
 * WEBからのアクセスを受け取るためのメインPHPです。
 */

// 出力を抑制
ob_start();

try{
	// 共通のライブラリの呼び出し。
	require(dirname(__FILE__)."/libs/require.php");
	
	// テンプレートを読み込む
	$TEMPLATE_ENGINE = $_SERVER["CONFIGURE"]->TEMPLATE_ENGINE;
	$_SERVER["TEMPLATE"] = new $TEMPLATE_ENGINE();
	
	$_SERVER["TEMPLATE"]->assign("u", FRAMEWORK_URL_BASE);
	foreach($_SERVER as $name =>$value){
		$_SERVER["TEMPLATE"]->assign($name, $value);
	}
	try{
		$_SERVER["TEMPLATE"]->display(substr($_SERVER["TEMPLATE_NAME"], 1));
	}catch(Exception $ex){
		showHttpError("404", "Not Found", $ex);
	}

	// 出力対象のコンテンツを取得	
	$content = trim(ob_get_contents());
	ob_end_clean();
	echo $content;
	Clay_Database_Factory::close();
	Clay_Logger::writeDebug("TEMPLATE_PAGE : ".$_SERVER["TEMPLATE_NAME"]." Finished.");
}catch(Exception $ex){
	ob_end_clean();
	// キャッシュ無効にするヘッダを送信
	echo $ex->getMessage();
}
?>
