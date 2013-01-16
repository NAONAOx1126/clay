<?php
/**
 * Copyright (C) 2012 Clay System All Rights Reserved.
 * 
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 * 
 * http://www.apache.org/licenses/LICENSE-2.0
 * 
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 * @author    Naohisa Minagawa <info@clay-system.jp>
 * @copyright Copyright (c) 2010, Clay System
 * @license http://www.apache.org/licenses/LICENSE-2.0.html Apache License, Version 2.0
 * @since PHP 5.3
 * @version   4.0.0
 */
 
/**
 * WEBからのアクセスを受け取るためのメインPHPです。
 */

// 出力バッファをフィルタするための関数です。
function filterOutputBuffer($content){
	return trim($content);
}

// 出力を抑制
ob_start("filterOutputBuffer");

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
		// 短縮URLの設定を取得し、存在している場合はリダイレクト
		$loader = new Clay_Plugin("Content");
		$loader->LoadSetting();
		$shortcut = $loader->LoadModel("ShortcutModel");
		$shortcut->findByCode(substr($_SERVER["TEMPLATE_NAME"], 1));
		if($shortcut->shortcut_id > 0){
			header("Location: ".$shortcut->redirect_url);
			exit;
		}
		
		if($_SERVER["CONFIGURE"]->USE_ACTIVE_PAGE && $_SERVER["TEMPLATE_NAME"] != "favicon.ico"){
			$path = urldecode($_SERVER["TEMPLATE_NAME"]);
			$loader = new Clay_Plugin("Content");
			$loader->LoadSetting();
			if($_SERVER["CLIENT_DEVICE"]->isFuturePhone()){
				$activePage = $loader->LoadModel("ActiveMobilePageModel");
			}else{
				$activePage = $loader->LoadModel("ActivePageModel");
			}
			if(preg_match("/^\\/([^\\/]+)\\/([^\\/]+)\\/([^\\/]+)\\/([^\\/]+)\\.html$/", $path, $params) > 0){
				$activePage->findByProductCode(mb_convert_kana($params[1], "KV"), mb_convert_kana($params[2], "KV"), mb_convert_kana($params[3], "KV"), mb_convert_kana($params[4], "KV"));
				if($activePage->entry_id > 0){
					$_POST["entry_id"] = $activePage->entry_id;
					$_SERVER["TEMPLATE"]->display("__active_page/detail.html");
					exit;
				}
			}elseif(preg_match("/^\\/([^\\/]+)\\/([^\\/]+)\\/([^\\/]+)\\.html$/", $path, $params) > 0){
				$activePage->findByProductCode(mb_convert_kana($params[1], "KV"), mb_convert_kana($params[2], "KV"), "", mb_convert_kana($params[3], "KV"));
				if($activePage->entry_id > 0){
					$_POST["entry_id"] = $activePage->entry_id;
					$_SERVER["TEMPLATE"]->display("__active_page/detail.html");
					exit;
				}
			}elseif(preg_match("/^\\/([^\\/]+)\\/([^\\/]+)\\.html$/", $path, $params) > 0){
				$activePage->findByProductCode(mb_convert_kana($params[1], "KV"), "", "", mb_convert_kana($params[2], "KV"));
				if($activePage->entry_id > 0){
					$_POST["entry_id"] = $activePage->entry_id;
					$_SERVER["TEMPLATE"]->display("__active_page/detail.html");
					exit;
				}
			}elseif(preg_match("/^\\/([^\\/]+)\\.html$/", $path, $params) > 0){
				$activePage->findByProductCode("", "", "", mb_convert_kana($params[1], "KV"));
				if($activePage->entry_id > 0){
					$_POST["entry_id"] = $activePage->entry_id;
					$_SERVER["TEMPLATE"]->display("__active_page/detail.html");
					exit;
				}
			}elseif(preg_match("/^\\/([^\\/]+)\\/([^\\/]+)\\/([^\\/]+)/", $path, $params) > 0){
				$activePages = $activePage->countBy(array("category1" => mb_convert_kana($params[1], "KV"), "category2" => mb_convert_kana($params[2], "KV"), "category3" => mb_convert_kana($params[3], "KV")));
				if($activePages > 0){
					$_POST["category1"] = $params[1];
					$_POST["category2"] = $params[2];
					$_POST["category3"] = $params[3];
					$_SERVER["TEMPLATE"]->display("__active_page/category.html");
					exit;
				}
			}elseif(preg_match("/^\\/([^\\/]+)\\/([^\\/]+)/", $path, $params) > 0){
				$activePages = $activePage->countBy(array("category1" => mb_convert_kana($params[1], "KV"), "category2" => mb_convert_kana($params[2], "KV")));
				if($activePages > 0){
					$_POST["category1"] = $params[1];
					$_POST["category2"] = $params[2];
					$_SERVER["TEMPLATE"]->display("__active_page/category.html");
					exit;
				}
			}elseif(preg_match("/^\\/([^\\/]+)/", $path, $params) > 0){
				$activePages = $activePage->countBy(array("category1" => mb_convert_kana($params[1], "KV")));
				if($activePages > 0){
					$_POST["category1"] = $params[1];
					$_POST["category2"] = "";
					$_POST["category3"] = "";
					$_POST["product_code"] = "";
					$_SERVER["TEMPLATE"]->display("__active_page/category.html");
					exit;
				}elseif(preg_match("/^[0-9]{8}$/", $params[1]) > 0){
					$_POST["date"] = "";
					$_POST["category2"] = "";
					$_POST["category3"] = "";
					$_POST["product_code"] = "";
					$_SERVER["TEMPLATE"]->display("__active_page/article.html");
				}elseif($params[1] == "search.html"){
					$_POST["category1"] = "";
					$_POST["category2"] = "";
					$_POST["category3"] = "";
					$_POST["product_code"] = "";
					$_SERVER["TEMPLATE"]->display("__active_page/search.html");
				}
			}elseif($path == "/"){
				$_POST["category1"] = "";
				$_POST["category2"] = "";
				$_POST["category3"] = "";
				$_POST["product_code"] = "";
				$_SERVER["TEMPLATE"]->display("__active_page/index.html");
				exit;
			}
		}
		showHttpError("404", "Not Found", $ex);
	}

	// 出力対象のコンテンツを取得	
	ob_end_flush();
	Clay_Database_Factory::close();
	Clay_Logger::writeDebug("TEMPLATE_PAGE : ".$_SERVER["TEMPLATE_NAME"]." Finished.");
}catch(Exception $ex){
	ob_end_clean();
	// キャッシュ無効にするヘッダを送信
	echo $ex->getMessage();
}
