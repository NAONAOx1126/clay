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
 * PostgreSQLのクエリ実行結果を管理するためのクラスです。
 *
 * @package Database
 * @author Naohisa Minagawa <info@clay-system.jp>
 */
class Clay_Database_Postgresql_Result{
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
 