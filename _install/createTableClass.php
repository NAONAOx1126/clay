<?php
// パラメータを変数に格納
if($argc > 0){
	$_SERVER["SERVER_NAME"] = $argv[1];
	$_SERVER["HTTP_USER_AGENT"] = "CLAY/1.0";
	$_SERVER["QUERY_STRING"] = "";
	$_SERVER["REQUEST_URI"] = "http://".$_SERVER["SERVER_NAME"]."/";
}

// 共通のライブラリの呼び出し。
$_SERVER["CONFIGURE"]->SESSION_MANAGER = "";
require_once(dirname(__FILE__)."/../libs/require.php");

// 自動生成対象のパッケージ名
$packages = array("address", "admin");

ini_set("memory_limit", -1);

set_time_limit(0);

foreach($packages as $package){
	// データベースに接続する。
	$connection = Clay_Database_Factory::getConnection($package);
	
	// データベースからテーブルのリストを取得する。
	$result = $connection->query("SHOW TABLES");
	$refactor = new Clay_Refactor_Table($connection);
	while($row = $result->fetch()){
		// テーブル名と対応するパッケージ名／クラス名を取得する。
		foreach(array_keys($row) as $key){
			$tableName = $row[$key];
		}
		if(preg_match("/^".$package."_/i", $tableName) > 0){
			echo $tableName."\r\n";
			$refactor->refactor($tableName);
		}
	}
	$result->close();
	$connection->close();
}

?>
