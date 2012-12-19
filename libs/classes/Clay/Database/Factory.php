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
 * データベースのインスタンスを生成するためのファクトリクラスです。
 *
 * @package Common
 * @author Naohisa Minagawa <info@clay-system.jp>
 */
class Clay_Database_Factory{
	/**
	 * @var array[string] データベースの接続情報を保持するインスタンス属性
	 */
	private static $configures;
	
	/**
	 * @var array[PDOConnection] データベースの接続を保持するインスタンス属性で
	 */
	private static $connections;
	
	/**
	 * データベースファクトリクラスを初期化します。
	 * @param array[string] $configures データベースの接続情報
	 */
	public static function initialize($configures){
		Clay_Database_Factory::$configures = $configures;
		Clay_Database_Factory::refresh();
	}
	
	public static function refresh(){
		Clay_Database_Factory::$connections = array();
	}
	
	/**
	 * データベースの設定情報を取得します。
	 * @param string $code データベース設定の元となるキー
	 * @return array[string] データベースの接続情報
	 */
	public static function getConfigure($code = "default"){
		return Clay_Database_Factory::$configures[$code];
	}
	
	/**
	 * データベースの接続を取得します。
	 * @param string $code データベース設定の元となるキー
	 * @return array[PDOConnection] データベースの接続
	 */
	public static function getConnection($code = "default", $readonly = false){
		// 指定された設定が無い場合はデフォルトの設定を有効にする。
		if(!isset(Clay_Database_Factory::$configures[$code])){
			$code = "default";
		}
		
		// 読み込み専用で読み込み用の定義がある場合には、読み込み定義に変更
		if($readonly && isset(Clay_Database_Factory::$configures["read:".$code])){
			$code = "read:".$code;
		}
		
		// DBのコネクションが設定されていない場合は接続する。
		if(!isset(Clay_Database_Factory::$connections[$code])){
			$conf = Clay_Database_Factory::$configures[$code];

			try{
				// 設定に応じてDBに接続
				switch($conf["dbtype"]){
					case "pgsql":
						Clay_Database_Factory::$connections[$code] = new Clay_Database_Postgresql_Connection($conf);
						break;
					case "mysql":
						Clay_Database_Factory::$connections[$code] = new Clay_Database_Mysql_Connection($conf);
						break;
				}
			}catch(PDOException $e){
				// 接続に失敗した場合にはデータベース例外を発行
				throw new Clay_Exception_Database($e);
			}
		}
		return Clay_Database_Factory::$connections[$code];
	}
	
	public static function begin($code = "default"){
		$connection = Clay_Database_Factory::getConnection($code);
		if($connection != null){
			$connection->begin();
		}
	}	
	
	public static function commit($code = "default"){
		$connection = Clay_Database_Factory::getConnection($code);
		if($connection != null){
			$connection->commit();
		}
	}	
	
	public static function rollback($code = "default"){
		$connection = Clay_Database_Factory::getConnection($code);
		if($connection != null){
			$connection->rollback();
		}
	}	
	
	public static function close(){
		foreach(Clay_Database_Factory::$connections as $code => $connection){
			$connection->close();
			unset(Clay_Database_Factory::$connections[$code]);
		}
	}
}
 