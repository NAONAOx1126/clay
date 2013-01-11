#!/usr/bin/php
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
	$_SERVER["HTTP_USER_AGENT"] = "PHP/Clay-Batch Engine/1.0";
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
