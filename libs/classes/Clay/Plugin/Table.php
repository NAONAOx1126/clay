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
 * データベーステーブルラッパー用のクラスです。
 *
 * @package Plugin
 * @author Naohisa Minagawa <info@clay-system.jp>
 */
class Clay_Plugin_Table{
	/** 
	 * @var string DB接続に使用するモジュール名
	 */
	protected $module;
	
	/**
	 * @var string DB接続に使用するテーブル名
	 */
	protected $tableName;

	/**
	 * @var array[Clay_Plugin_Table_Column] カラムリスト 
	 */
	private $_COLUMNS;

	/**
	 * @var array[string] 主キーのリスト 
	 */
	private $_PKEYS;

	/**
	 * @var array[string] カラムフル名リスト 
	 */
	private $_FIELDS;

	/** 
	 * @var string 元テーブル名 
	 */
	public $_B;

	/** 
	 * @var string テーブル名
	 */
	public $_T;

	/**
	 * @var string カラム用テーブル名
	 */
	public $_C;

	/** 
	 * @var string テーブルワイルドカード
	 */
	public $_W;

	/**
	 * データベーステーブルモデルを初期化
	 * @param string $tableName テーブル名
	 * @param string $module モジュール名
	 */
	public function __construct($tableName, $module = DEFAULT_PACKAGE_NAME){
		// モジュール名を保存
		$this->module = strtolower($module);
		
		// テーブル名を保存
		$this->tableName = $tableName;
		
		// 初期化処理
		$this->initialize();
	}

	protected function initialize(){
		// DBの接続を取得する。
		$connection = DBFactory::getConnection($this->module, true);
		
		// 構成されたカラム情報を元に設定値を生成
		$this->_B = $this->_T = $this->_C = $connection->escape_identifier($this->tableName);
		$this->_W = $this->_C.".*";

		// テーブル構成のキャッシュがある場合にはキャッシュからテーブル情報を取得
		$tableConfigure = Clay_Cache_Factory::create("table_".$this->tableName);
		if($tableConfigure->options == ""){
			// テーブルの定義を取得
			$options = $connection->columns($this->_T);

			// テーブルの主キーを取得
			$keys = $connection->keys($this->_T);
			
			// テーブルの設定をデータキャッシュに登録する。
			$tableConfigure->import(array("options" => $options, "keys" => $keys));
		}

		// カラム情報を設定
		$this->_COLUMNS = array();
		$this->_FIELDS = array();
		foreach($tableConfigure->options as $option){
			$column = new Clay_Plugin_Table_Column($this, $option);
			$field = $column->field;
			$this->_COLUMNS[] = $field;
			$this->_FIELDS[$field] = $column;
		}
		// 主キー情報を設定
		$this->_PKEYS = array();
		if(is_array($tableConfigure->keys)){
			foreach($tableConfigure->keys as $key){
				$this->_PKEYS[] = $key;
			}
		}
	}

	/**
	 * テーブルの接続に使用しているモジュールの名前を取得する。
	 * @return string モジュール名
	 */
	public function getModuleName(){
		return $this->module;
	}

	/**
	 * テーブルのカラム情報を取得
	 * @param string $name カラム情報
	 * @return string カラム名
	 */
	public function __get($name){
		return $this->_FIELDS[$name];
	}

	/**
	 * テーブルのカラム情報が設定されているかチェックする。
	 * @param string $name カラム情報
	 * @return boolean カラムが存在している場合にはtrue
	 */
	public function __isset($name){
		return isset($this->_FIELDS[$name]);
	}

	/**
	 * テーブルエイリアス名を設定する。
	 * @param string $tableName エイリアス名
	 * @return Clay_Plugin_Table テーブルオブジェクト
	 */
	public function setAlias($tableName){
		// DBの接続を取得する。
		$connection = DBFactory::getConnection($this->module, true);
		
		// エイリアスの設定に応じて、テーブル内の各変数を調整
		$this->_C = $connection->escape_identifier($tableName);
		$this->_T = $this->_B." AS ".$connection->escape_identifier($tableName);
		$this->_W = $this->_C.".*";
		return $this;
	}

	/**
	 * 主キーのリストを取得する。
	 * @return array[string] 主キーのリスト
	 */
	public function getPrimaryKeys(){
		return $this->_PKEYS;
	}
	
	public function getColumns(){
		return $this->_COLUMNS;
	}

	/**
	 * テーブルクラスを文字列として扱った場合にテーブル名として扱われるようにする。
	 * @return string テーブルクラスの文字列表現
	 */
	public function __toString(){
		return $this->_T;
	}
	
	public function __sleep(){
		return array("tableName", "module");
	}
	
	public function __wakeup(){
		$this->initialize();
	}
}
 