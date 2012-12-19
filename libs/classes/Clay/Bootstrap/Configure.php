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
 * ローカル設定ファイル読み込み処理の起動処理です。
 * 
 * @package Bootstrap
 * @author Naohisa Minagawa <info@clay-system.jp>
 */
class Clay_Bootstrap_Configure{
	public static function start(){
		// サイトの設定を取得
		$configure = Clay_Cache_Factory::create("site_configure");
		
		// サイトIDが取れない場合は基本設定を再取得
		if($configure->site_id == ""){
			// グローバルの設定を読み込みデータを反映
			$configure->import($_SERVER["CONFIGURE"]);
			$_SERVER["CONFIGURE"] = $configure;
			
			// データベースファクトリクラスを初期化
			$base_connections = $_SERVER["CONFIGURE"]->connection;
			$defaultDatabase = $base_connections["default"];
			Clay_Database_Factory::initialize(array("default" => $defaultDatabase));
		
			// サイト情報を取得する。
			$loader = new Clay_Plugin();
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
		}else{
			$_SERVER["CONFIGURE"] = $configure;
		}
		
		// サイトIDを定数にする。
		define("SITE_ID", $_SERVER["CONFIGURE"]->site_id);
		
		// データベースの設定をリロード
		Clay_Database_Factory::initialize($_SERVER["CONFIGURE"]->connection);
	}
}
 