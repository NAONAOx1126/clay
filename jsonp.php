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
 * JSONP形式によるデータの取得を行うためのメインPHPです。
 */

// 共通のライブラリの呼び出し。
require(dirname(__FILE__)."/libs/require.php");

if($_SERVER["CONFIGURE"]->JSON_API_KEY == "" || isset($_POST["k"]) && $_SERVER["CONFIGURE"]->JSON_API_KEY == $_POST["k"]){
	ini_set("memory_limit", -1);
	
	if(strpos($_SERVER["REQUEST_URI"], "&callback=") !== FALSE){
		list($requestUri, $dummy) = explode("&callback=", $_SERVER["REQUEST_URI"]);
	}else{
		$requestUri = $_SERVER["REQUEST_URI"];
	}
	
	// コールバックを取得
	if(isset($_POST["callback"])){
		$callback = $_POST["callback"];
		unset($_POST["callback"]);
	}
	unset($_POST["_"]);
	
	// JSONのキャッシュを初期化
	$jsonCache = Clay_Cache_Factory::create("json_".sha1($requestUri));
	
	if(isset($_POST["c"]) && !empty($_POST["c"]) && isset($_POST["p"]) && !empty($_POST["p"])){
		$loader = new Clay_Plugin($_POST["c"]);
		$json = $loader->loadJson($_POST["p"]);
		unset($_POST["c"]);
		unset($_POST["p"]);
		
		if($jsonCache->json == "" || isset($json->disable_cache) && $json->disable_cache){
			try{
				if($json != null){
					// バッチのモジュールの呼び出し
					$result = $json->execute();
		
					// キャッシュファイルを作成
					$jsonCache->import(array("json" => $result));
				}
					
			}catch(Exception $ex){
				$result = array("ERROR" => $ex->getMessage());
			}
		
		}
		
		$result = $jsonCache->json;
		$data = json_encode($result);
		
		header("Content-Type: application/json; charset=utf-8");
		
		if(!empty($callback)){
			echo $callback."(".$data.");";
		}else{
			echo $data;
		}
	}else{
		header("HTTP/1.0 404 Not Found");
		exit;
	}
}else{
	header("HTTP/1.0 404 Not Found");
	exit;
}
