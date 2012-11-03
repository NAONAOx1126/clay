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
 * JSONP形式によるデータの取得を行うためのメインPHPです。
 */

// 共通のライブラリの呼び出し。
require(dirname(__FILE__)."/libs/require.php");

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
?>
