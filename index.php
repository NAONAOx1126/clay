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
	
	// プレフィルタを実行する。
	foreach($_SERVER["CONFIGURE"]->prefilters as $filter){
		$loader = new Clay_Plugin($filter["type"]);
		$loader->LoadSetting();
		$filter = $loader->loadFilter($filter["name"]);
		$filter->execute();
	}
	
	// テンプレートにサーバー変数を渡す。
	foreach($_SERVER as $name =>$value){
		$_SERVER["TEMPLATE"]->assign($name, $value);
	}
	
	// テンプレートの出力処理を実行
	try{
		$_SERVER["TEMPLATE"]->display(substr($_SERVER["TEMPLATE_NAME"], 1));
	}catch(Exception $ex){
		showHttpError("404", "Not Found", $ex);
	}

	// ポストフィルタを実行する。
	foreach($_SERVER["CONFIGURE"]->postfilters as $filter){
		$loader = new Clay_Plugin($filter["type"]);
		$loader->LoadSetting();
		$filter = $loader->loadFilter($filter["name"]);
		$filter->execute();
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
