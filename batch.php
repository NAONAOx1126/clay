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
	$argv = array_slice($argv, 3);
}

ini_set("memory_limit", -1);

set_time_limit(0);

try{
	// 共通のライブラリの呼び出し。
	include(dirname(__FILE__)."/libs/common/require.php");
	
	if(file_exists(MINES_HOME."/batch/".$batch.".php")){
		// バッチのモジュールの呼び出し
		include(MINES_HOME."/batch/".$batch.".php");
	}
}catch(Exception $ex){
	echo $ex->getMessage();
}
?>
