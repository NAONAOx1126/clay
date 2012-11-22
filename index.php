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
	
	foreach($_SERVER as $name =>$value){
		$_SERVER["TEMPLATE"]->assign($name, $value);
	}
	try{
		$_SERVER["TEMPLATE"]->display(substr($_SERVER["TEMPLATE_NAME"], 1));
	}catch(Exception $ex){
		if($_SERVER["CONFIGURE"]->USE_ACTIVE_PAGE){
			$path = $_SERVER["TEMPLATE_NAME"];
			$loader = new Clay_Plugin("Content");
			$loader->LoadSetting();
			$activePage = $loader->loadModel("ActivePageModel");
			if(preg_match("/^\\/([\\/]+)\\/([\\/]+)\\/([\\/]+)\\/([\\/]+)\\.html$/", $path, $params) > 0){
				$activePage->findByProductCode($params[1], $params[2], $params[3], $params[4]);
				if($activePage->entry_id > 0){
					$_POST["entry_id"] = $activePage->entry_id;
					$_SERVER["TEMPLATE"]->display("__active_page/detail.html");
					exit;
				}
			}elseif(preg_match("/^\\/([\\/]+)\\/([\\/]+)\\/([\\/]+)/", $path, $params) > 0){
				$activePages = $activePage->findAllByCategory3($params[1], $params[2], $params[3]);
				if(count($activePages) > 0){
					$_POST["category1"] = $params[1];
					$_POST["category2"] = $params[2];
					$_POST["category3"] = $params[3];
					$_SERVER["TEMPLATE"]->display("__active_page/category.html");
					exit;
				}
			}elseif(preg_match("/^\\/([\\/]+)\\/([\\/]+)/", $path, $params) > 0){
				$activePages = $activePage->findAllByCategory2($params[1], $params[2]);
				if(count($activePages) > 0){
					$_POST["category1"] = $params[1];
					$_POST["category2"] = $params[2];
					$_SERVER["TEMPLATE"]->display("__active_page/category.html");
					exit;
				}
			}elseif(preg_match("/^\\/([\\/]+)/", $path, $params) > 0){
				$activePages = $activePage->findAllByCategory1($params[1]);
				if(count($activePages) > 0){
					$_POST["category1"] = $params[1];
					$_SERVER["TEMPLATE"]->display("__active_page/category.html");
					exit;
				}elseif($params[1] == "search.html"){
					$_SERVER["TEMPLATE"]->display("__active_page/search.html");
				}
			}elseif($path == "/"){
				$_SERVER["TEMPLATE"]->display("__active_page/index.html");
				exit;
			}
		}
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
