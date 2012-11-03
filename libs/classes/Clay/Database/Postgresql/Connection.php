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
 * PostgreSQLのコネクションを管理するためのクラスです。
 *
 * @package Database
 * @author Naohisa Minagawa <info@clay-system.jp>
 */
class Clay_Database_Postgresql_Connection{
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
			return new Clay_Database_Postgresql_Result(pg_query($this->connection, $query));
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
 