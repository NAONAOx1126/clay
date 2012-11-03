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

// サイトの設定を取得
$_SERVER["CONFIGURE"] = Clay_Cache_Factory::create("site_configure");

// SERVER_NAMEが未設定の場合はlocalhostを割当
if(!isset($_SERVER["SERVER_NAME"])){
	$_SERVER["SERVER_NAME"] = "localhost";
}

// サイトIDが取れない場合は基本設定を再取得
if($_SERVER["CONFIGURE"]->site_id == ""){
	// グローバル設定の取得
	if(file_exists(FRAMEWORK_CONFIGURE_HOME."/configure_".$_SERVER["SERVER_NAME"].".php")){
		require(FRAMEWORK_CONFIGURE_HOME."/configure_".$_SERVER["SERVER_NAME"].".php");
	}else{
		require(FRAMEWORK_CONFIGURE_HOME."/configure.php");
	}

	// データベースファクトリクラスを初期化
	$base_connections = $_SERVER["CONFIGURE"]->connection;
	$defaultDatabase = $base_connections["default"];
	DBFactory::initialize(array("default" => $defaultDatabase));

	// サイト情報を取得する。
	$loader = new PluginLoader();
	$site = $loader->loadModel("SiteModel");
	if($site->findByHostName()){
		$siteArray = $site->toArray();
		// サイトの接続設定を取得する。
		$connections = $site->connections();
		$siteArray["connection"] = $_SERVER["CONFIGURE"]->connection;
		foreach($connections as $connection){
			if($connection->connection_code != "default"){
				$dbconf = array();
				if($connection->dbtype != ""){ $dbconf["dbtype"] = $connection->dbtype; }
				if($connection->host != ""){ $dbconf["host"] = $connection->host; }
				if($connection->port != ""){ $dbconf["port"] = $connection->port; }
				if($connection->user != ""){ $dbconf["user"] = $connection->user; }
				if($connection->password != ""){ $dbconf["password"] = $connection->password; }
				if($connection->database != ""){ $dbconf["database"] = $connection->database; }
				if($connection->query != ""){ $dbconf["query"] = $connection->query; }
				$siteArray["connection"][$connection->connection_code] = $dbconf;
			}
		}

		// サイトオプション設定を取得する。
		$configures = $site->configures();
		foreach($configures as $configure){
			if($configure->name != "connection"){
				$siteArray[$configure->name] = $configure->value;
			}
		}
		
		// サイトデータをキャッシュに保存
		$_SERVER["CONFIGURE"]->import($siteArray);
	}
}

// サイトIDを定数にする。
define("SITE_ID", $_SERVER["CONFIGURE"]->site_id);

// データベースの設定をリロード
DBFactory::initialize($_SERVER["CONFIGURE"]->connection);
?>
