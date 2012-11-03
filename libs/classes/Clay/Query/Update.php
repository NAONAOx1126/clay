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
 * データベース更新処理用のクラスです。
 *
 * @package Query
 * @author Naohisa Minagawa <info@clay-system.jp>
 */
class Clay_Query_Update{
	/** 
	 * @var string 接続に使用するモジュール名 
	 */
	private $module;

	private $tables;

	private $sets;

	private $wheres;

	private $setValues;

	private $tableValues;

	private $whereValues;

	public function __construct($table){
		$this->module = $table->getModuleName();
		$this->tables = $table->_T;
		$this->sets = array();
		$this->wheres = array();
		$this->setValues = array();
		$this->tableValues = array();
		$this->whereValues = array();
	}

	public function joinInner($table, $conditions = array(), $values = array()){
		$this->tables .= " INNER JOIN ".$table->_T.(!empty($conditions)?" ON ".implode(" AND ", $conditions):"");
		foreach($values as $v){
			$this->tableValues[] = (is_string($v)?trim($v):$v);
		}
		return $this;
	}

	public function joinLeft($table, $conditions = array(), $values = array()){
		$this->tables .= " LEFT JOIN ".$table->_T.(!empty($conditions)?" ON ".implode(" AND ", $conditions):"");
		foreach($values as $v){
			$this->tableValues[] = (is_string($v)?trim($v):$v);
		}
		return $this;
	}

	public function addSets($expression, $values = array()){
		$this->sets[] = $expression;
		foreach($values as $v){
			$this->setValues[] = (is_string($v)?trim($v):$v);
		}
		return $this;
	}

	public function addWhere($condition, $values = array()){
		$this->wheres[] = "(".$condition.")";
		foreach($values as $v){
			$this->whereValues[] = (is_string($v)?trim($v):$v);
		}
		return $this;
	}

	public function buildQuery(){
		// クエリのビルド
		$sql = "UPDATE ".$this->tables;
		$sql .= (!empty($this->sets)?" SET ".implode(", ", $this->sets):"");
		$sql .= (!empty($this->wheres)?" WHERE ".implode(" AND ", $this->wheres):"");

		return $sql;
	}

	public function showQuery(){
		$sql = $this->buildQuery();

		$values = array_merge($this->tableValues, $this->setValues, $this->whereValues);

		if(is_array($values) && count($values) > 0){
			$partSqls = explode("?", $sql);
			$sql = $partSqls[0];
	
			$connection = Clay_Database_Factory::getConnection($this->module, true);
			foreach($values as $index => $value){
				$sql .= "'".$connection->escape($value)."'".$partSqls[$index + 1];
			}
		}

		return $sql;
	}

	public function execute(){
		if(!empty($this->sets)){

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
		}else{
			return 0;
		}
	}
}
 