#!/usr/bin/php
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
 * バッチ処理を実行するためのメインPHPです。
 */

if($argc < 2){
	echo "実行するバッチプログラムを指定してください。";
	exit;
}elseif($argc < 3){
	echo "設定を参照するサーバーを指定してください。";
	exit;
}else{
	$batch = $argv[1];
	$_SERVER["SERVER_NAME"] = $argv[2];
	$_SERVER["REQUEST_URI"] = "/";
	$_SERVER["QUERY_STRING"] = "";
	$argv = array_slice($argv, 3);
}

ini_set("memory_limit", -1);

set_time_limit(0);

try{
	// 共通のライブラリの呼び出し。
	require(dirname(__FILE__)."/libs/require.php");
	
	$loader = new Clay_Plugin("");
	$object = $loader->loadBatch($batch);
	
	if(method_exists($object, "execute")){
		Clay_Logger::writeDebug("MODULE : ".$batch." start");
		$object->execute($argv);
		Clay_Logger::writeDebug("MODULE : ".$batch." end");
	}else{
		Clay_Logger::writeAlert($batch." is not batch module.");
		echo $batch." is not batch module.";
	}
}catch(Exception $ex){
	echo $ex->getMessage();
}
?>
