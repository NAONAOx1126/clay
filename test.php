<?php
/**
 * ユニットテストを行うためのメインPHPです。
 *
 * @category  Test
 * @package   Main
 * @author    Naohisa Minagawa <info@sweetberry.jp>
 * @copyright 2010-2012 Naohisa Minagawa
 * @license http://www.apache.org/licenses/LICENSE-2.0.html Apache License, Version 2.0
 * @version   1.0.0
 */

// 共通のライブラリの呼び出し。
include(dirname(__FILE__)."/libs/common/require.php");

if($argc < 2){
	echo "実行するテストメソッドを指定してください。";
	exit;
}elseif($argc < 3){
	echo "設定を参照するサーバーを指定してください。";
	exit;
}else{
	$testKeys = explode(".", $argv[1]);
	$_SERVER["SERVER_NAME"] = $argv[2];
}

if(count($testKeys) < 2){
	echo "テストメソッドは最低でも２階層以上必要です。";
	exit;
}

$testFilePath = array_splice($testKeys, 0, -1);
$testFile = implode("/", $testFilePath).".php";
$testClass = "Test_".implode("_", $testFilePath);
$testMethod = $testKeys[count($testKeys) - 1];

if(file_exists(FRAMEWORK_BASE_TEST_MODULE_HOME."/".$testFile)){
	require_once(FRAMEWORK_BASE_TEST_MODULE_HOME."/".$testFile);
}elseif(file_exists(FRAMEWORK_TEST_MODULE_HOME."/".$testFile)){
	require_once(FRAMEWORK_TEST_MODULE_HOME."/".$testFile);
}else{
	echo "テストクラスファイルがありません。";
	exit;
}

try{
	$test = new $testClass();
	if($test->$testMethod()){
		echo $testClass." => ".$testMethod."：成功\r\n";
	}else{
		echo $testClass." => ".$testMethod."：失敗\r\n";
	}
}catch(Exception $e){
	echo $testClass." => ".$testMethod."：失敗\r\n";
	print_r($e);
}
?>