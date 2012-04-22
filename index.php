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
	$content = ob_get_contents();
	ob_end_clean();
	
	Logger::writeDebug("TEMPLATE_CONTENT : ".$content);
	// ページ出力用のヘッダを出力
	header("Content-Type: text/html; charset=utf-8");
	header("Cache-Control: no-cache, must-revalidate");
	header("P3P: CP='UNI CUR OUR'");
	header("Expires: Thu, 01 Dec 1994 16:00:00 GMT");
	header("Last-Modified: ". gmdate("D, d M Y H:i:s"). " GMT");
	header("Cache-Control: no-cache, must-revalidate");
	header("Cache-Control: post-check=0, pre-check=0", false);
	header("Pragma: no-cache");
	echo $content;
	Logger::writeDebug("TEMPLATE_PAGE : ".$_SERVER["TEMPLATE_NAME"]." Finished.");
}catch(Exception $ex){
	ob_end_clean();
	// キャッシュ無効にするヘッダを送信
	echo $ex->getMessage();
}
?>
