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
 * データベース削除処理用のクラスです。
 *
 * @package Query
 * @author Naohisa Minagawa <info@clay-system.jp>
 */
class Clay_Query_Delete{
	/** 
	 * @var string 接続に使用するモジュール名 
	 */
	private $module;

	/**
	 * 削除対象のテーブル
	 */
	private $tables;

	/**
	 * 削除対象のレコードの条件
	 */
	private $wheres;

	/**
	 * 削除対象のレコードの条件に設定するパラメータリスト
	 */
	private $values;

	/**
	 * レコード削除処理を初期化します。
	 *
	 * @params string $table レコード削除対象のテーブル
	 */
	public function __construct($table){
		$this->module = $table->getModuleName();
		$this->tables = $table->_T;
		$this->wheres = array();
		$this->values = array();
	}

	/**
	 * レコード削除条件を追加します。
	 *
	 * @params string $condition レコード削除条件式
	 */
	public function addWhere($condition, $values = array()){
		$this->wheres[] = "(".$condition.")";
		foreach($values as $v){
			$this->values[] = (is_string($v)?trim($v):$v);
		}
		return $this;
	}

	/**
	 * 現在の状態で発行する削除クエリを取得する。
	 *
	 * @return string レコード削除クエリ
	 */
	public function buildQuery(){
		// クエリのビルド
		$sql = "DELETE FROM ".$this->tables;
		$sql .= (!empty($this->wheres)?" WHERE ".implode(" AND ", $this->wheres):"");

		return $sql;
	}

	public function showQuery(){
		$sql = $this->buildQuery();

		if(is_array($this->values) && count($this->values) > 0){
			$partSqls = explode("?", $sql);
			$sql = $partSqls[0];
	
			$connection = Clay_Database_Factory::getConnection($this->module, true);
			foreach($this->values as $index => $value){
				$sql .= "'".$connection->escape($value)."'".$partSqls[$index + 1];
			}
		}

		return $sql;
	}

	/**
	 * レコードの削除を実行する。
	 */
	public function execute(){
		// クエリを実行する。
		try{
			$sql = $this->showQuery();
			$connection = Clay_Database_Factory::getConnection($this->module);
			Logger::writeDebug($sql);
			$result = $connection->query($sql);
		}catch(Exception $e){
			Logger::writeError($sql, $e);
			throw new Clay_Exception_Database($e);
		}
		return $result;
	}
}
 