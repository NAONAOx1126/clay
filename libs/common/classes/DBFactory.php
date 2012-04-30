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
 * データベースのインスタンスを生成するためのファクトリクラスです。
 *
 * @package Common
 * @author Naohisa Minagawa <info@sweetberry.jp>
 */
class DBFactory{
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
		DBFactory::$configures = $configures;
		DBFactory::refresh();
	}
	
	public static function refresh(){
		DBFactory::$connections = array();
	}
	
	/**
	 * データベースの設定情報を取得します。
	 * @param string $code データベース設定の元となるキー
	 * @return array[string] データベースの接続情報
	 */
	public static function getConfigure($code = "default"){
		return DBFactory::$configures[$code];
	}
	
	/**
	 * データベースの接続を取得します。
	 * @param string $code データベース設定の元となるキー
	 * @return array[PDOConnection] データベースの接続
	 */
	public static function getConnection($code = "default", $readonly = false){
		// 指定された設定が無い場合はデフォルトの設定を有効にする。
		if(!isset(DBFactory::$configures[$code])){
			$code = "default";
		}
		
		// 読み込み専用で読み込み用の定義がある場合には、読み込み定義に変更
		if($readonly && isset(DBFactory::$configures["read:".$code])){
			$code = "read:".$code;
		}
		
		// DBのコネクションが設定されていない場合は接続する。
		if(!isset(DBFactory::$connections[$code])){
			$conf = DBFactory::$configures[$code];

			// 未設定の場合のデフォルト設定
			if(!isset($conf["host"])){
				$conf["host"] = "localhost";
			}
			if(!isset($conf["database"])){
				$conf["database"] = $conf["user"];
			}
			
			// DB接続前の設定
			$options = array();
			$options[PDO::ATTR_TIMEOUT] = 1;
			$options[PDO::ATTR_PERSISTENT] = false;
			$options[PDO::ATTR_ERRMODE] = PDO::ERRMODE_EXCEPTION;
			
			try{
				// 設定に応じてDBに接続
				switch($conf["dbtype"]){
					case "oracle":
						if(!isset($conf["port"])){
							$conf["port"] = "1521";
						}
						$dsn = "oci:dbname=//".$conf["host"].":".$conf["port"]."/".$conf["database"];
						DBFactory::$connections[$code] = new PDO($dsn, $conf["user"], $conf["password"], $options);
						break;
					case "mssql":
						$dsn = "mssql:host=".$conf["host"].";dbname=".$conf["database"];
						DBFactory::$connections[$code] = new PDO($dsn, $conf["user"], $conf["password"], $options);
						break;
					case "pgsql":
						if(!isset($conf["port"])){
							$conf["port"] = "5432";
						}
						$dsn = "pgsql:dbname=".$conf["database"]." host=".$conf["host"]." port=".$conf["port"];
						DBFactory::$connections[$code] = new PDO($dsn, $conf["user"], $conf["password"], $options);
						break;
					case "mysql":
						if(!isset($conf["port"])){
							$conf["port"] = "3306";
						}
						$dsn = "mysql:dbname=".$conf["database"].";host=".$conf["host"].";port=".$conf["port"];
						DBFactory::$connections[$code] = new PDO($dsn, $conf["user"], $conf["password"], $options);
						break;
					default:
						break;
				}
	
				// 接続に成功したら次回以降の接続を変更してオートコミットを無効にする。
				DBFactory::$connections[$code]->setAttribute(PDO::ATTR_TIMEOUT, 30);
				DBFactory::$connections[$code]->setAttribute(PDO::ATTR_AUTOCOMMIT, false);
				DBFactory::$connections[$code]->setAttribute(PDO::ATTR_CASE, PDO::CASE_LOWER);
						
				// DBの初期化クエリを実行
				DBFactory::$connections[$code]->query($conf["query"]);
			}catch(PDOException $e){
				// 接続に失敗した場合にはデータベース例外を発行
				throw new DatabaseException($e);
			}
		}
		return DBFactory::$connections[$code];
	}
	
	public static function close(){
		foreach(DBFactory::$connections as $code => $connection){
			DBFactory::$connections[$code] = null;
		}
	}
}
?>
