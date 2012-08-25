<?php
/**
 * This file is part of CLAY Framework for view-module based system.
 *
 * @author    Naohisa Minagawa <info@clay-system.jp>
 * @copyright Copyright (c) 2010, Naohisa Minagawa
 * @license http://www.apache.org/licenses/LICENSE-2.0.html Apache License, Version 2.0
 * @since PHP 5.3
 * @version   3.0.0
 */

/**
 * データベーステーブルラッパー用のクラスです。
 *
 * @package Models
 * @author Naohisa Minagawa <info@sweetberry.jp>
 */
class DatabaseTable{
	/** 
	 * @var string DB接続に使用するモジュール名
	 */
	protected $module;
	
	/**
	 * @var string DB接続に使用するテーブル名
	 */
	protected $tableName;

	/**
	 * @var array[DatabaseColumn] カラムリスト 
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
		$tableConfigure = DataCacheFactory::create("table_".$this->tableName);
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
			$column = new DatabaseColumn($this, $option);
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
	 * @return DatabaseTable テーブルオブジェクト
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

/**
 * データベースカラムラッパー用のクラスです。
 *
 * @package Models
 * @author Naohisa Minagawa <info@sweetberry.jp>
 */
class DatabaseColumn{
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
	public function __construct($table, $column){
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

/**
 * データベース参照処理用のクラスです。
 *
 * @package Models
 * @author Naohisa Minagawa <info@sweetberry.jp>
 */
class DatabaseSelect{
	/** 
	 * @var string 接続に使用するモジュール名 
	 */
	private $module;

	/** 
	 * @var boolean distinctするかどうかのフラグ 
	 */
	private $distinct;

	/** 
	 * @var array[DatabaseColumn] 表示対象のカラム 
	 */
	private $columns;

	/** 
	 * @var DatabaseTable 検索対象のテーブル 
	 */
	private $tables;

	/** 
	 * @var array[string] 検索抽出条件 
	 */
	private $wheres;

	/** 
	 * @var array[string] 検索結果グルーピング 
	 */
	private $groups;

	/** 
	 * @var array[string] グルーピング抽出条件 
	 */
	private $having;

	/** 
	 * @var array[string] 抽出結果並べ替え条件 */
	private $orders;

	/** テーブル結合用の変数 */
	private $tableValues;

	/**
	 * 
	 * Enter description here ...
	 * @var array
	 */
	private $whereValues;

	private $havingValues;
	
	private $limit;
	
	private $offset;

	public function __construct($table){
		$this->module = $table->getModuleName();
		$this->distinct = false;
		$this->columns = array();
		$this->tables = $table->_T;
		$this->wheres = array();
		$this->groups = array();
		$this->having = array();
		$this->orders = array();
		$this->tableValues = array();
		$this->whereValues = array();
		$this->havingValues = array();
	}

	public function distinct($distinct = true){
		$this->distinct = $distinct;
	}

	public function addColumn($column, $alias = ""){
		$this->columns[] = $column.(!empty($alias)?" AS `".$alias."`":"");
		return $this;
	}

	public function join($table, $conditions = array(), $values = array()){
		return $this->joinInner($table, $conditions, $values);
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
	
	public function where($condition, $values = array()){
		return $this->addWhere($condition, $values);
	}

	public function addWhere($condition, $values = array()){
		$this->wheres[] = "(".$condition.")";
		foreach($values as $v){
			$this->whereValues[] = (is_string($v)?trim($v):$v);
		}
		return $this;
	}
	
	public function group($column){
		return $this->addGroupBy($column);
	}

	public function addGroupBy($column){
		$this->groups[] = $column;
		return $this;
	}
	
	public function having($condition, $values = array()){
		return $this->addHaving($condition, $values);
	}

	public function addHaving($condition, $values = array()){
		$this->having[] = "(".$condition.")";
		foreach($values as $v){
			$this->havingValues[] = (is_string($v)?trim($v):$v);
		}
		return $this;
	}
	
	public function order($column, $reverse = false){
		return $this->addOrder($column, $reverse);
	}

	public function addOrder($column, $reverse = false){
		$this->orders[] = $column.($reverse?" DESC":" ASC");
		return $this;
	}
	
	public function setLimit($limit = null, $offset = null){
		$this->limit = $limit;
		$this->offset = $offset;
	}

	public function buildQuery(){
		// クエリのビルド
		$sql = "SELECT ";
		if($this->distinct){
			$sql .= " DISTINCT ";
		}
		$sql .= implode(", ", $this->columns)." FROM ".$this->tables;
		$sql .= (!empty($this->wheres)?" WHERE ".implode(" AND ", $this->wheres):"");
		$sql .= (!empty($this->groups)?" GROUP BY ".implode(", ", $this->groups):"");
		$sql .= (!empty($this->having)?" HAVING ".implode(", ", $this->having):"");
		$sql .= (!empty($this->orders)?" ORDER BY ".implode(", ", $this->orders):"");
		$sql .= (($this->limit !== null)?" LIMIT ".$this->limit:"");
		$sql .= (($this->offset !== null)?" OFFSET ".$this->offset:"");

		return $sql;
	}

	public function showQuery(){
		$sql = $this->buildQuery();

		$values = array_merge($this->tableValues, $this->whereValues, $this->havingValues);
		
		$connection = DBFactory::getConnection($this->module, true);

		if(is_array($values) && count($values) > 0){
			$partSqls = explode("?", $sql);
			$sql = $partSqls[0];
	
			foreach($values as $index => $value){
				$sql .= "'".$connection->escape($value)."'".$partSqls[$index + 1];
			}
		}
		return $sql;
	}

	public function fetch($limit = null, $offset = null){
		// クエリのビルド
		$sql = $this->buildQuery();

		// クエリの検索件数を制限する。
		if($limit != null){
			$sql .= " LIMIT ".$limit;
			if($offset != null){
				$sql .= " OFFSET ".$offset;
			}
		}

		// クエリを実行する。
		try{
			$sql = $this->showQuery();
			Logger::writeDebug($sql);
			$connection = DBFactory::getConnection($this->module, true);
			$result = $connection->query($sql);
		}catch(Exception $e){
			Logger::writeError($sql, $e);
			throw new DatabaseException($e);
		}

		return new DatabaseResult($result);
	}

	public function execute($limit = null, $offset = null){
		// 結果オブジェクトを取得
		$result = $this->fetch($limit, $offset);

		// 結果を全て取得
		$data = $result->all();

		// 結果オブジェクトを解放
		$result->close();
		$result = null;

		// クエリの実行結果を返す。
		return $data;
	}
}

/**
 * データベースSELECTの結果処理用のクラスです。
 *
 * @package Models
 * @author Naohisa Minagawa <info@sweetberry.jp>
 */
class DatabaseResult{
	/**
	 * クエリ実行に使ったプPrepared Statementオブジェクト
	 */
	private $result;

	/**
	 * データベースの参照結果を初期化します。
	 *
	 * @params object $prepare クエリ実行に使ったPrepared Statementオブジェクト
	 * @params object $result クエリの実行結果オブジェクト
	 */

	public function __construct($result){
		$this->result =& $result;
	}

	/**
	 * 次の実行結果レコードの連想配列を取得するメソッド
	 *
	 * @return array 次の実行結果レコードの連想配列、次のレコードが無い場合はFALSE
	 */
	public function next(){
		return $this->result->fetch();
	}

	/**
	 * 次の実行結果レコードの連想配列を取得するメソッド
	 *
	 * @return array 次の実行結果レコードの連想配列、次のレコードが無い場合はFALSE
	 */
	public function all(){
		return $this->result->fetchAll();
	}

	/**
	 * クエリの実行結果をクローズし、リソースを解放する
	 */
	public function close(){
		$this->result->close();
	}
}

/**
 * データベース挿入処理用のベースクラスです。
 *
 * @package Models
 * @author Naohisa Minagawa <info@sweetberry.jp>
 */
abstract class DatabaseInsertBase{
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
		foreach($values as $key => $value){
			if(isset($this->table->$key)){
				$cols[] = $key;
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
		$connection = DBFactory::getConnection($this->module, true);
		foreach($values as $key => $value){
			if(isset($this->table->$key)){
				$cols[] = $this->table->$key;
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
			$connection = DBFactory::getConnection($this->module);
			return $connection->auto_increment();
		}catch(Exception $e){
			Logger::writeError($e->getMessage(), $e);
			throw new DatabaseException($e);
		}
	}	

	/**
	 * 現在の状態で挿入クエリを実行する。
	 *
	 * @params array $values 挿入データの連想配列
	 */
	public function execute($values){
		try{
			$connection = DBFactory::getConnection($this->module);
			$sql = $this->showQuery($values);
			Logger::writeDebug($sql);
			$result = $connection->query($sql);
		}catch(Exception $e){
			Logger::writeError($sql, $e);
			throw new DatabaseException($e);
		}
		return $result;
	}	
}

/**
 * データベース挿入処理用のクラスです。
 *
 * @package Models
 * @author Naohisa Minagawa <info@sweetberry.jp>
 */
class DatabaseInsert extends DatabaseInsertBase{
	protected function getPrefix(){
		return "INSERT";
	}
}

/**
 * データベース排他挿入処理用のクラスです。
 *
 * @package Models
 * @author Naohisa Minagawa <info@sweetberry.jp>
 */
class DatabaseInsertIgnore extends DatabaseInsertBase{
	protected function getPrefix(){
		return "INSERT IGNORE";
	}
}

/**
 * データベース遅延挿入処理用のクラスです。
 *
 * @package Models
 * @author Naohisa Minagawa <info@sweetberry.jp>
 */
class DatabaseInsertDelayed extends DatabaseInsertBase{
	protected function getPrefix(){
		return "INSERT DELAYED";
	}
}

/**
 * データベース置換挿入処理用のクラスです。
 *
 * @package Models
 * @author Naohisa Minagawa <info@sweetberry.jp>
 */
class DatabaseReplace extends DatabaseInsertBase{
	protected function getPrefix(){
		return "REPLACE";
	}
}

/**
 * データベース更新処理用のクラスです。
 *
 * @package Models
 * @author Naohisa Minagawa <info@sweetberry.jp>
 */
class DatabaseUpdate{
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
	
			$connection = DBFactory::getConnection($this->module, true);
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
				$connection = DBFactory::getConnection($this->module);
				Logger::writeDebug($sql);
				$result = $connection->query($sql);
			}catch(Exception $e){
				Logger::writeError($sql, $e);
				throw new DatabaseException($e);
			}

			return $result;
		}else{
			return 0;
		}
	}
}

/**
 * データベース削除処理用のクラスです。
 *
 * @package Models
 * @author Naohisa Minagawa <info@sweetberry.jp>
 */
class DatabaseDelete{
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
	
			$connection = DBFactory::getConnection($this->module, true);
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
			$connection = DBFactory::getConnection($this->module);
			Logger::writeDebug($sql);
			$result = $connection->query($sql);
		}catch(Exception $e){
			Logger::writeError($sql, $e);
			throw new DatabaseException($e);
		}
		return $result;
	}
}

/**
 * データベースクリーンアップ処理用のクラスです。
 *
 * @package Models
 * @author Naohisa Minagawa <info@sweetberry.jp>
 */
class DatabaseTruncate{
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
			$connection = DBFactory::getConnection($this->module);
			Logger::writeDebug($sql);
			$result = $connection->query($sql);
		}catch(Exception $e){
			Logger::writeError($sql, $e);
			throw new DatabaseException($e);
		}

		return $result;
	}
}
?>
