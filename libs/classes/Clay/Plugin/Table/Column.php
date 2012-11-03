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
 * データベースカラムラッパー用のクラスです。
 *
 * @package Plugin
 * @author Naohisa Minagawa <info@clay-system.jp>
 */
class Clay_Plugin_Table_Column{
	/**
	 * @var DatabaseTable テーブルのインスタンス 
	 */
	private $table;

	/** 
	 * @var string フィールド名 
	 */
	private $field;

	/**
	 * @var boolean NULL可能かどうかのフラグ
	 */
	private $canNull;

	/** 
	 * @var boolean 主キーかどうかのフラグ
	 */
	private $isKey;

	/** 
	 * @var boolean 自動採番かどうかのフラグ
	 */
	private $isAutoIncrement;

	/**
	 * データベースのフィールドインスタンスを生成する。
	 * @param DatabaseTable $table フィールドを保有しているテーブルのインスタンス
	 * @param string $column フィールドのカラム名
	 */
	public function __construct(&$table, $column){
		$this->table =& $table;
		$this->field = $column["Field"];
		$this->canNull = (($column["Null"] == "YES")?true:false);
		$this->isKey = (($column["Key"] == "PRI")?true:false);
		$this->isAutoIncrement = (($column["Extra"] == "auto_increment")?true:false);
	}
	
	/**
	 * テーブルのカラムの詳細情報を取得
	 * @param string $name カラム種別
	 * @return string カラム詳細
	 */
	public function __get($name){
		if(isset($this->$name)){
			return $this->$name;
		}
		return null;
	}
	
	/**
	 * フィールドを文字列として扱った場合にフィールド名となるようにする。
	 * @return string クラスの文字列表現
	 */
	public function __toString(){
		// DBの接続を取得する。
		$connection = DBFactory::getConnection($this->table->getModuleName(), true);
		
		// カラム名をエスケープする。
		return $this->table->_C.".".$connection->escape_identifier($this->field);
	}
}
 