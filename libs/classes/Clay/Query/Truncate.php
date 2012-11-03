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
 * データベースクリーンアップ処理用のクラスです。
 *
 * @package Query
 * @author Naohisa Minagawa <info@clay-system.jp>
 */
class Clay_Query_Truncate{
	/** 
	 * @var string 接続に使用するモジュール名 
	 */
	private $module;

	private $tables;

	public function __construct($table){
		$this->module = $table->getModuleName();
		$this->tables = $table->_T;
	}

	public function buildQuery(){
		// クエリのビルド
		$sql = "TRUNCATE ".$this->tables;

		return $sql;
	}

	public function execute(){
		// クエリのビルド
		$sql = $this->buildQuery();

		// クエリを実行する。
		try{
			$connection = Clay_Database_Factory::getConnection($this->module);
			Clay_Logger::writeDebug($sql);
			$result = $connection->query($sql);
		}catch(Exception $e){
			Clay_Logger::writeError($sql, $e);
			throw new Clay_Exception_Database($e);
		}

		return $result;
	}
}
 