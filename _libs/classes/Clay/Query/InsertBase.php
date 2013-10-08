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
 * データベース挿入処理用のベースクラスです。
 *
 * @package Query
 * @author Naohisa Minagawa <info@clay-system.jp>
 */
abstract class Clay_Query_InsertBase{
	/** 
	 * @var string 接続に使用するモジュール名 
	 */
	private $module;

	/**
	 * 挿入対象のテーブル
	 */
	private $table;

	/**
	 * 挿入するデータの連想配列
	 */
	private $vals;

	/**
	 * レコード挿入処理を初期化します。
	 *
	 * @params string $table レコード挿入対象のテーブル
	 */
	public function __construct($table){
		$this->module = $table->getModuleName();
		$this->table =& $table;
	}
	
	protected abstract function getPrefix();

	/**
	 * 現在の状態で発行する挿入クエリを取得する。
	 *
	 * @params array $values 挿入データの連想配列
	 * @return string レコード削除クエリ
	 */
	public function buildQuery($values){
		// パラメータを展開
		$cols = array();
		$phs = array();
		$this->vals = array();
		$connection = Clay_Database_Factory::getConnection($this->module, true);
		foreach($values as $key => $value){
			if(isset($this->table->$key)){
				$cols[] = $connection->escape_identifier($key);
				$phs[] = "?";
				$this->vals[] = trim($value);
			}
		}

		$sql = "";
		if(!empty($cols)){
			// クエリのビルド
			$sql = $this->getPrefix()." INTO ".$this->table->_T."(".implode(", ", $cols).") VALUES (".implode(", ", $phs).")";
		}
		return $sql;
	}

	public function showQuery($values){
		// パラメータを展開
		$cols = array();
		$vals = array();
		$connection = Clay_Database_Factory::getConnection($this->module, true);
		foreach($values as $key => $value){
			if(isset($this->table->$key)){
				$cols[] = $connection->escape_identifier($key);
				$vals[] = "'".$connection->escape(trim($value))."'";
			}
		}

		$sql = "";
		if(!empty($cols)){
			// クエリのビルド
			$sql = $this->getPrefix()." INTO ".$this->table->_T."(".implode(", ", $cols).") VALUES (".implode(", ", $vals).")";
		}
		return $sql;
	}

	/**
	 * 最後に挿入したレコードのIDを取得する。
	 */
	public function lastInsertId(){
		try{
			$connection = Clay_Database_Factory::getConnection($this->module);
			return $connection->auto_increment();
		}catch(Exception $e){
			Clay_Logger::writeError($e->getMessage(), $e);
			throw new Clay_Exception_Database($e);
		}
	}	

	/**
	 * 現在の状態で挿入クエリを実行する。
	 *
	 * @params array $values 挿入データの連想配列
	 */
	public function execute($values){
		try{
			$connection = Clay_Database_Factory::getConnection($this->module);
			$sql = $this->showQuery($values);
			Clay_Logger::writeDebug($sql);
			$result = $connection->query($sql);
		}catch(Exception $e){
			Clay_Logger::writeError($sql, $e);
			throw new Clay_Exception_Database($e);
		}
		return $result;
	}	
}
 