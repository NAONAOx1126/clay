<?php
/**
 * JSONP形式によるデータの取得を行うためのメインPHPです。
 *
 * @category  Batch
 * @package   Main
 * @author    Naohisa Minagawa <info@sweetberry.jp>
 * @copyright 2010-2012 Naohisa Minagawa
 * @license http://www.apache.org/licenses/LICENSE-2.0.html Apache License, Version 2.0
 * @version   1.0.0
 */

// 共通のライブラリの呼び出し。
include(dirname(__FILE__)."/libs/common/require.php");

ini_set("memory_limit", -1);

list($requestUri, $dummy) = explode("&callback=", $_SERVER["REQUEST_URI"]);
// JSONキャッシュディレクトリが無い場合は自動的に作成
if(!is_dir(MINES_HOME."/JSONP/cache")){
	mkdir(MINES_HOME."/JSONP/cache");
}
$cache = MINES_HOME."/JSONP/cache/".sha1($requestUri).".json";
if(!($_GET["interval"] > 0) || !file_exists($cache) || filemtime($cache) < strtotime("-".$_GET["interval"]." second")){
	try{
		$path = MINES_HOME."/JSONP/".str_replace(".", "/", $_POST["p"]).".php";
		
		$result = array();
		
		if(file_exists($path)){
			// バッチのモジュールの呼び出し
			include($path);
		}
		
	}catch(Exception $ex){
		$result = array("ERROR" => $ex->getMessage());
	}
	
	// キャッシュファイルを作成
	$data = json_encode($result);
	if(($fp = fopen($cache, "w+")) !== FALSE){
		fwrite($fp, $data);
		fclose($fp);
	}
}else{
	$data = file_get_contents($cache);
}		

header("Content-Type: application/json; charset=utf-8");

echo $_GET["callback"]."(".$data.");";
?>
