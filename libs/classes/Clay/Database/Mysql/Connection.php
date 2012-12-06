<?php
/**
 * This file is part of CLAY Framework for view-module based system.
 *
 * @author    Naohisa Minagawa <info@clay-system.jp>
 * @copyright Copyright (c) 2010, Naohisa Minagawa
 * @license http://www.apache.org/licenses/LICENSE-2.0.html Apache License, Version 2.0
 * @since PHP 5.3
 * @version   4.0.0
 */
 
/**
 * MySQLのコネクションを管理するためのクラスです。
 *
 * @package Database
 * @author Naohisa Minagawa <info@clay-system.jp>
 */
class Clay_Database_Mysql_Connection{
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
	
	public function indexes($table){
		$result = $this->query("SHOW INDEXES FROM ".$table);
		$indexes = array();
		while($index = $result->fetch()){
			if(!is_array($indexes[$index["Key_name"]])){
				$indexes[$index["Key_name"]] = array();
			}
			$indexes[$index["Key_name"]][] = $index["Column_name"];
		}
		$result->close();
		return $indexes;
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
				return new Clay_Database_Mysql_Result($result);
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
 