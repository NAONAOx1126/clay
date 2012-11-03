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
 * PHP Array形式によるデータの取得を行うためのメインPHPです。
 */

// 共通のライブラリの呼び出し。
require(dirname(__FILE__)."/libs/require.php");

list($requestUri, $dummy) = explode("&callback=", $_SERVER["REQUEST_URI"]);
// JSONキャッシュディレクトリが無い場合は自動的に作成
if(!is_dir(MINES_HOME."/JSONP/cache")){
	mkdir(MINES_HOME."/JSONP/cache");
}
$cache = MINES_HOME."/JSONP/cache/".sha1($requestUri).".phparray";
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
	$data = var_export($result, true);
	if(($fp = fopen($cache, "w+")) !== FALSE){
		fwrite($fp, $data);
		fclose($fp);
	}
}else{
	$data = file_get_contents($cache);
}		

header("Content-Type: application/json; charset=utf-8");

echo "$".$_GET["callback"]." = ".$data.";";
?>
