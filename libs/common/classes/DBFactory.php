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

			try{
				// 設定に応じてDBに接続
				switch($conf["dbtype"]){
					case "pgsql":
						DBFactory::$connections[$code] = new PostgresqlConnection($conf);
						break;
					case "mysql":
						DBFactory::$connections[$code] = new MysqlConnection($conf);
						break;
				}
			}catch(PDOException $e){
				// 接続に失敗した場合にはデータベース例外を発行
				throw new DatabaseException($e);
			}
		}
		return DBFactory::$connections[$code];
	}
	
	public static function begin($code = "default"){
		$connection = DBFactory::getConnection($code);
		if($connection != null){
			$connection->begin();
		}
	}	
	
	public static function commit($code = "default"){
		$connection = DBFactory::getConnection($code);
		if($connection != null){
			$connection->commit();
		}
	}	
	
	public static function rollback($code = "default"){
		$connection = DBFactory::getConnection($code);
		if($connection != null){
			$connection->rollback();
		}
	}	
	
	public static function close(){
		foreach(DBFactory::$connections as $code => $connection){
			$connection->close();
			unset(DBFactory::$connections[$code]);
		}
	}
}

class MysqlConnection{
	private $connection;
	
	public function __construct($configure){
		if(!isset($configure["port"])){
			$configure["port"] = "3306";
		}
		$this->connection = mysql_connect($configure["host"].":".$configure["port"], $configure["user"], $configure["password"], true);
		mysql_select_db($configure["database"], $this->connection);
		mysql_set_charset("UTF-8", $this->connection);
		mysql_query($configure["query"], $this->connection);
	}
	
	public function columns($table){
		// テーブルの定義を取得
		$result = $this->query("SHOW COLUMNS FROM ".$table);
		$columns = array();
		while($column = $result->fetch()){
			$columns[] = $column;
		}
		$result->close();
		return $columns;
	}
	
	public function keys($table){
		$result = $this->query("SHOW INDEXES FROM ".$table." WHERE Key_name = 'PRIMARY'");
		$keys = array();
		while($key = $result->fetch()){
			$keys[] = $key["Column_name"];
		}
		$result->close();
		return $keys;
	}
	
	public function begin(){
		$this->query("BEGIN");
	}
	
	public function commit(){
		$this->query("COMMIT");
	}
	
	public function rollback(){
		$this->query("ROLLBACK");
	}
	
	public function escape($value){
		if($this->connection != null){
			return mysql_real_escape_string($value, $this->connection);
		}
		return null;
	}
	
	public function escape_identifier($identifier){
		return "`".$identifier."`";
	}
	
	public function query($query){
		if($this->connection != null){
			mysql_ping($this->connection);
			$result = mysql_query($query, $this->connection);
			if($result === FALSE){
				return FALSE;
			}elseif($result !== TRUE){
				return new MysqlResult($result);
			}else{
				return mysql_affected_rows($this->connection);
			}
		}
		return null;
	}
	
	public function auto_increment(){
		return mysql_insert_id($this->connection);
	}
	
	public function close(){
		if($this->connection != null){
			mysql_close($this->connection);
			$this->connection = null;
		}
	}
}

class MysqlResult{
	private $resource;
	
	public function __construct($resource){
		$this->resource = $resource;
	}
	
	public function fetch(){
		if($this->resource != null){
			return mysql_fetch_assoc($this->resource);
		}
		return FALSE;
	}
	
	public function fetchAll(){
		$result = array();
		while(($data = $this->fetch()) !== FALSE){
			$result[] = $data;
		}
		return $result;
	}
	
	public function count(){
		return mysql_num_rows($this->resource);
	}
	
	public function close(){
		mysql_free_result($this->resource);
		$this->resource = null;
	}
}

class PostgresqlConnection{
	private $connection;
	
	public function __construct($configure){
		if(!isset($configure["port"])){
			$configure["port"] = "5432";
		}
		$this->connection = pg_connect("host=".$configure["host"]." port=".$configure["port"]." dbname=".$configure["database"]." user=".$configure["user"]." password=".$configure["password"]);
		pg_set_client_encoding($this->connection, "UTF-8");
		pg_query($this->connection, $configure["query"]);
	}
	
	public function escape($value){
		if($this->connection != null){
			return pg_escape_literal($this->connection, $value);
		}
		return null;
	}
	
	public function query($query){
		if($this->connection != null){
			return new PostgresqlResult(pg_query($this->connection, $query));
		}
		return null;
	}
	
	public function close(){
		if($this->connection != null){
			pg_close($this->connection);
			$this->connection = null;
		}
	}
}

class PostgresqlResult{
	private $resource;
	
	public function __constrcut($resource){
		$this->resource = $resource;
	}
	
	public function fetch(){
		if($this->resource != null){
			return pg_fetch_assoc($this->resource);
		}
		return array();
	}
	
	public function fetchAll(){
		$result = array();
		while(($data = $this->fetch()) !== FALSE){
			$result[] = $data;
		}
		$this->close();
		return $result;
	}
	
	public function close(){
		pg_free_result($this->resource);
		$this->resource = null;
	}
}
?>
