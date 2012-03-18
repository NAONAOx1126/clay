<?php
/**
 * データベースのアクセスを制御するクラスです。
 *
 * @category  Common
 * @package   Models
 * @author    Naohisa Minagawa <info@sweetberry.jp>
 * @copyright 2010-2012 Naohisa Minagawa
 * @license http://www.apache.org/licenses/LICENSE-2.0.html Apache License, Version 2.0
 * @since PHP 5.3
 * @version   1.0.0
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
	private $module;

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

		// 構成されたカラム情報を元に設定値を生成
		$this->_B = $this->_T = $this->_C = "`".$tableName."`";
		$this->_W = $this->_C.".*";

		// テーブル構成のキャッシュがある場合にはキャッシュからテーブル情報を取得
		$tableConfigure = DataCacheFactory::create("table_".$tableName);
		if($tableConfigure->options == ""){
			// DBの接続を取得
			$connection = DBFactory::getConnection($this->module);
			// テーブルの定義を取得
			$prepare = $connection->prepare("DESC ".$this->_T);
			$prepare->execute();
			$options = array();
			while($option = $prepare->fetch(PDO::FETCH_ASSOC)){
				$options[] = $option;
			}
			$prepare->closeCursor();

			// テーブルの主キーを取得
			$prepare = $connection->prepare("SHOW INDEXES FROM ".$this->_T." WHERE Key_name = 'PRIMARY'");
			$prepare->execute();
			$keys = array();
			while($key = $prepare->fetch(PDO::FETCH_ASSOC)){
				$keys[] = $key["column_name"];
			}
			$prepare->closeCursor();
			$connection = null;

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
		$this->_C = "`".$tableName."`";
		$this->_T = $this->_B." AS `".$tableName."`";
		$this->_W = $this->_C.".*";
		return $this;
	}

	/**
	 * CSVファイルをテーブルに取り込む
	 * @param string $filename 取り込むCSVファイル名
	 * @param int $headers 先頭から無視する行数
	 * @return int 取り込んだ行数
	 */
	public function import($filename, $headers = 1){
		$connection = DBFactory::getConnection($this->module);
		$sql = "LOAD DATA LOCAL INFILE '".$filename."' REPLACE INTO TABLE ".$this->_B."";
		$sql .= " FIELDS TERMINATED BY ',' ENCLOSED BY '\"'";
		$sql .= " IGNORE ".$headers." LINES";
		$prepare = $connection->prepare($sql);
		$prepare->execute();
		$result = $prepare->rowCount();
		$prepare->closeCursor();
		$connection = null;

		return $result;
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
		$this->table = $table;
		$this->field = $column["field"];
		$this->canNull = (($column["null"] == "YES")?true:false);
		$this->isKey = (($column["key"] == "PRI")?true:false);
		$this->isAutoIncrement = (($column["extra"] == "auto_increment")?true:false);
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
		return $this->table->_C.".`".$this->field."`";
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
	 * @var PDOConnection データベース接続オブジェクト 
	 */
	private $db;

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

	public function __construct($table){
		$this->db = DBFactory::getConnection($table->getModuleName());
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

	public function addWhere($condition, $values = array()){
		$this->wheres[] = "(".$condition.")";
		foreach($values as $v){
			$this->whereValues[] = (is_string($v)?trim($v):$v);
		}
		return $this;
	}

	public function addGroupBy($column){
		$this->groups[] = $column;
		return $this;
	}

	public function addHaving($condition, $values = array()){
		$this->having[] = "(".$condition.")";
		foreach($values as $v){
			$this->havingValues[] = (is_string($v)?trim($v):$v);
		}
		return $this;
	}

	public function addOrder($column, $reverse = false){
		$this->orders[] = $column.($reverse?" DESC":" ASC");
		return $this;
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

		return $sql;
	}

	public function showQuery(){
		$sql = $this->buildQuery();

		$values = array_merge($this->tableValues, $this->whereValues, $this->havingValues);

		if(is_array($values) && count($values) > 0){
			$partSqls = explode("?", $sql);
			$sql = $partSqls[0];
	
			foreach($values as $index => $value){
				$sql .= "'".$value."'".$partSqls[$index + 1];
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
			Logger::writeDebug($this->showQuery());
			$prepare = $this->db->prepare($sql);
			$values = array_merge($this->tableValues, $this->whereValues, $this->havingValues);
			$prepare->execute($values);
		}catch(Exception $e){
			Logger::writeError($sql, $e);
			throw new DatabaseException($e);
		}

		return new DatabaseResult($prepare);
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

	public function executePager($options = array()){
		// 最終的に出力するページオブジェクトを生成
		$page = array();

		if(!empty($options)){
			// 件数カウント用クエリのビルド
			$sql = "SELECT COUNT(1) AS count FROM (".$this->buildQuery().") AS t1";

			// クエリを実行する。
			$prepare = $this->db->prepare($sql);
			$values = array_merge($this->tableValues, $this->whereValues, $this->havingValues);
			$prepare->execute($values);
			// 検索結果の取得
			$data = array();
			if($row = $prepare->fetch(PDO::FETCH_ASSOC)){
				$options["totalItems"] = $row["count"];
			}
			$prepare->closeCursor();


			$options["fileName"] = $_SERVER["TEMPLATE_NAME"];
			$options["fixFileName"] = false;

			// ページャーのインスタンスを作成
			$pager = AdvancedPager::factory($options);

			// ページャーからリンクをコピー
			$page["links"] = $pager->links;
			$page["links_object"] = $pager->getLinks();

			// オプションから該当件数を取得
			$page["totalItems"] = $options["totalItems"];

			// ページ数をページャーから取得
			$page["page_numbers"] = array(
				"current" => $pager->getCurrentPageID(),
				"total"   => $pager->numPages()
			);

			// 現在のページにおけるデータ全体に対するインデックスを取得
			list($page["from"], $page["to"]) = $pager->getOffsetByPageId();

			// 現在のページの実件数を取得
			$page["limit"] = $page["to"] - $page["from"] +1;

			// データを取得
			$page["data"] = $this->execute($options["perPage"], $page["from"]-1);
		}else{
			// データを取得
			$page["data"] = $this->execute();

			// ページャーからリンクをコピー
			$page["links"] = "";
			$page["links_object"] = array();

			// オプションから該当件数を取得
			$page["totalItems"] = count($page["data"]);

			// ページ数をページャーから取得
			$page["page_numbers"] = array(
				"current" => "1",
				"total"  => "1"
				);
					
				// 現在のページにおけるデータ全体に対するインデックスを取得
				$page["from"] = 1;
				$page["to"] = $page["totalItems"];
					
				// 現在のページの実件数を取得
				$page["limit"] = $page["to"] - $page["from"] +1;
		}

		// ページデータを返す。
		return $page;
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
	private $prepare;

	/**
	 * データベースの参照結果を初期化します。
	 *
	 * @params object $prepare クエリ実行に使ったPrepared Statementオブジェクト
	 * @params object $result クエリの実行結果オブジェクト
	 */

	public function __construct($prepare){
		$this->prepare = $prepare;
	}

	/**
	 * 次の実行結果レコードの連想配列を取得するメソッド
	 *
	 * @return array 次の実行結果レコードの連想配列、次のレコードが無い場合はFALSE
	 */
	public function next(){
		return $this->prepare->fetch(PDO::FETCH_ASSOC);
	}

	/**
	 * 次の実行結果レコードの連想配列を取得するメソッド
	 *
	 * @return array 次の実行結果レコードの連想配列、次のレコードが無い場合はFALSE
	 */
	public function all(){
		return $this->prepare->fetchAll(PDO::FETCH_ASSOC);
	}

	/**
	 * クエリの実行結果をクローズし、リソースを解放する
	 */
	public function close(){
		$this->prepare->closeCursor();
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
	 * データベースの接続
	 */
	private $db;

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
	 * @params object $db レコード挿入時に利用するデータベース接続
	 */
	public function __construct($table){
		$this->db = DBFactory::getConnection($table->getModuleName());
		$this->table = $table;
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
		foreach($values as $key => $value){
			if(isset($this->table->$key)){
				$cols[] = $key;
				$vals[] = trim($value);
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
	 * 現在の状態で挿入クエリを実行する。
	 *
	 * @params array $values 挿入データの連想配列
	 */
	public function execute($values){
		$sql = $this->buildQuery($values);
		if(!empty($sql)){
			// クエリを実行する。
			try{
				Logger::writeDebug($this->showQuery($this->vals));
				$prepare = $this->db->prepare($sql);
				$prepare->execute($this->vals);
				$result = $prepare->rowCount();
				$prepare->closeCursor();
			}catch(Exception $e){
				Logger::writeError($sql, $e);
				throw new DatabaseException($e);
			}
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
	private $db;

	private $tables;

	private $sets;

	private $wheres;

	private $setValues;

	private $tableValues;

	private $whereValues;

	public function __construct($table){
		$this->db = DBFactory::getConnection($table->getModuleName());
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
	
			foreach($values as $index => $value){
				$sql .= "'".$value."'".$partSqls[$index + 1];
			}
		}

		return $sql;
	}

	public function execute(){
		if(!empty($this->sets)){
			// クエリのビルド
			$sql = $this->buildQuery();

			// クエリを実行する。
			try{
				Logger::writeDebug($this->showQuery());
				$prepare = $this->db->prepare($sql);
				$values = array_merge($this->tableValues, $this->setValues, $this->whereValues);
				$prepare->execute($values);
				$result = $prepare->rowCount();
				$prepare->closeCursor();
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
	 * データベースの接続
	 */
	private $db;

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
	 * @params object $db レコード削除時に利用するデータベース接続
	 */
	public function __construct($table){
		$this->db = DBFactory::getConnection($table->getModuleName());
		$this->tables = $table->_T;
		$this->wheres = array();
		$this->values = array();
	}

	/**
	 * レコード削除条件を追加します。
	 *
	 * @params string $condition レコード削除条件式
	 * @params object $db レコード削除条件式に設定する変数
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

		if(is_array($values) && count($values) > 0){
			$partSqls = explode("?", $sql);
			$sql = $partSqls[0];
	
			foreach($values as $index => $value){
				$sql .= "'".$value."'".$partSqls[$index + 1];
			}
		}

		return $sql;
	}

	/**
	 * レコードの削除を実行する。
	 */
	public function execute(){
		// クエリのビルド
		$sql = $this->buildQuery();

		// クエリを実行する。
		try{
			Logger::writeDebug($this->showQuery());
			$prepare = $this->db->prepare($sql);
			$prepare->execute($this->values);
			$result = $prepare->rowCount();
			$prepare->closeCursor();
		}catch(Exception $e){
			Logger::writeError($sql, $e);
			throw new DatabaseException($e);
		}
	}
}

/**
 * データベースクリーンアップ処理用のクラスです。
 *
 * @package Models
 * @author Naohisa Minagawa <info@sweetberry.jp>
 */
class DatabaseTruncate{
	private $db;

	private $tables;

	public function __construct($table, $db = null){
		if($db == null){
			$db = DBFactory::getConnection($table->getModuleName());
		}
		$this->db = $db;
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
			Logger::writeDebug($this->buildQuery());
			$prepare = $this->db->prepare($sql);
			$prepare->execute(array());
			$result = $prepare->rowCount();
			$prepare->closeCursor();
			$prepare = null;
		}catch(Exception $e){
			Logger::writeError($sql, $e);
			throw new DatabaseException($e);
		}

		return $result;
	}
}
?>
