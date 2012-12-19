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
 * MySQLのクエリ実行結果を管理するためのクラスです。
 *
 * @package Database
 * @author Naohisa Minagawa <info@clay-system.jp>
 */
class Clay_Database_Mysql_Result{
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
	
	public function rewind(){
		if($this->count() > 0){
			mysql_data_seek($this->resource, 0);
		}
	}
	
	public function count(){
		return mysql_num_rows($this->resource);
	}
	
	public function close(){
		mysql_free_result($this->resource);
		$this->resource = null;
	}
}
 