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
 * データベースモデルラッパー用のクラスです。
 *
 * @package Models
 * @author Naohisa Minagawa <info@sweetberry.jp>
 */
class DatabaseModel{
	// ベースのデータベースアクセスオブジェクト
	protected $access;

	// カラムリスト
	protected $columns;
	
	//  主キーのリスト
	protected $primary_keys;
	
	// 元設定値リスト
	protected $values_org;

	// 設定値リスト
	protected $values;
	
	// 出力レコード数
	protected $limit;
	
	// 出力レコードオフセット
	protected $offset;
	
	/**
	 * データベースモデルを初期化する。
	 * 初期の値を配列で渡すことで、その値でモデルを構築する。
	 */
	public function __construct($accessTable, $values = array()){
		$this->access = $accessTable;
		$this->columns = array();
		$this->primary_keys = $this->access->getPrimaryKeys();
		$this->values_org = array();
		$this->values = array();
		foreach($this->access->getColumns() as $column){
			$this->columns[] = $column;
			$this->values_org[$column] = "";
			$this->values[$column] = "";
		}
		$this->setValues($values);
		$this->limit();
	}
	
	/**
	 * データベースのカラムのデータを取得する。
	 */
	public function __get($name){
		if(isset($this->values[$name])){
			return $this->values[$name];
		}
		return null;
	}
	
	/**
	 * データベースのカラムを主キー以外についてのみ登録する。
	 * また、レコード作成日は未設定の場合のみ設定可能。
	 */
	public function __set($name, $value){
		// 主キー以外のカラムとして存在した場合は変更を行う。
		if(!in_array($name, $this->primary_keys)){
			if($name == "create_time"){
				if(empty($this->values[$name])){
					// データ登録日は未設定の場合のみ設定する。
					$this->values[$name] = $value;
				}
			}else{
				$this->values[$name] = $value;
			}
		}
	}
	
	/**
	 * そのカラムが設定されているかどうかをチェックする。
	 */
	public function __isset($name){
		return isset($this->values[$name]);
	}
	
	/**
	 * オブジェクトを文字列として出力する。
	 */
	public function __toString(){
		return var_export($this->values, true);
	}
	
	/**
	 * 出力されるデータを制限する。
	 */
	public function limit($limit = null, $offset = null){
		$this->limit = $limit;
		$this->offset = $offset;
	}
	
	/**
	 * 出力されるデータのオフセットを設定する。
	 */
	public function offset($offset = null){
		$this->offset = $offset;
	}
		/**
	 * レコードを特定のキーで検索する。
	 * 複数件ヒットした場合は、最初の１件をデータとして取得する。
	 */
	public function findBy($values){
		$result = $this->findAllBy($values);

		if(count($result) > 0){
			$this->setValues($result[0]);
			return true;
		}
		return false;
	}
	
	/**
	 * レコードを特定のキーで検索する。
	 */
	public function findAllBy($values, $order = ""){
		$select = new DatabaseSelect($this->access);
		$select->addColumn($this->access->_W);
		foreach($values as $key => $value){
			$select = $this->appendWhere($select, $key, $value);
		}
		if(!empty($order)){
			$select->addOrder($order);
		}
		$result = $select->execute($this->limit, $this->offset);
		
		$thisClass = get_class($this);
		foreach($result as $i => $data){
			$result[$i] = new $thisClass($data);
		}
		
		return $result;
	}
	
	/**
	 * レコードの件数を取得する。
	 */
	public function countBy($values){
		$select = new DatabaseSelect($this->access);
		$select->addColumn("COUNT(*) AS count");
		foreach($values as $key => $value){
			$select = $this->appendWhere($select, $key, $value);
		}
		$result = $select->execute();
		
		if(count($result) > 0){
			return $result[0]["count"];
		}else{
			return "0";
		}
	}
	
	public function summeryBy($groups, $targets, $values = array(), $order = ""){
		$select = new DatabaseSelect($this->access);
		foreach($groups as $group){
			if(!empty($group)){
				if(substr($group, 0, 1) == ":" && substr($group, -1) == ":"){
					$group = str_replace(";",",", $group);
					$arrGroup = explode(":", substr($group, 1, -1));
					$group_name = array_pop($arrGroup);
					$group = "";
					for($i = 0; $i < count($arrGroup); $i ++){
						if($i % 2 == 0){
							$group .= $arrGroup[$i];
						}else{
							$name = $arrGroup[$i];
							$group .= $this->access->$name;
						}
					}
					$select->addColumn($group, $group_name);
					$select->addGroupBy($group);				
				}else{
					$select->addColumn($this->access->$group);
					$select->addGroupBy($this->access->$group);
				}
			}
		}
		if(is_array($this->primary_keys) && !empty($this->primary_keys)){
			$primary_key = $this->primary_keys[0];
			$select->addColumn("COUNT(".$this->access->$primary_key.")", "count");
		}else{
			$select->addColumn("COUNT(*)", "count");
		}
		foreach($targets as $target){
			$select->addColumn("SUM(".$this->access->$target.")", $target);
		}
		foreach($values as $key => $value){
			$select = $this->appendWhere($select, $key, $value);
		}
		if(!empty($order)){
			$select->addOrder($order);
		}else{
			$select->addOrder("count", true);
		}
		$result = $select->execute($this->limit, $this->offset);
		
		$thisClass = get_class($this);
		foreach($result as $i => $data){
			$result[$i] = new $thisClass($data);
		}
		
		return $result;
	}
	
	/**
	 * パラメータの値により、WHERE句を構築する。
	 * 
	 * @param $select SELECTオブジェクト
	 * @param $key 追加するキー
	 * @param $value 追加する値
	 * @return SELECTオブジェクト
	 */
	protected function appendWhere($select, $key, $value){
		if(strpos($key, ":") > 0){
			list($op, $key) = explode(":", $key);
		}else{
			$op = "eq";
		}
		if(in_array($key, $this->columns)){
			switch($op){
				case "eq":
					if($value == null){
						$select->addWhere($this->access->$key." IS NULL");
					}else{
						$select->addWhere($this->access->$key." = ?", array($value));
					}
					break;
				case "ne":
					if($value == null){
						$select->addWhere($this->access->$key." IS NOT NULL");
					}else{
						$select->addWhere($this->access->$key." != ?", array($value));
					}
					break;
				case "gt":
					$select->addWhere($this->access->$key." > ?", array($value));
					break;
				case "ge":
					$select->addWhere($this->access->$key." >= ?", array($value));
					break;
				case "lt":
					$select->addWhere($this->access->$key." < ?", array($value));
					break;
				case "le":
					$select->addWhere($this->access->$key." <= ?", array($value));
					break;
				case "like":
					$select->addWhere($this->access->$key." LIKE ?", array($value));
					break;
				case "nlike":
					$select->addWhere($this->access->$key." NOT LIKE ?", array($value));
					break;
				case "in":
					if(!is_array($value)){
						$value = array($value);
					}
					$select->addWhere($this->access->$key." in (?)", array($value));
					break;
				case "nin":
					if(!is_array($value)){
						$value = array($value);
					}
					$select->addWhere($this->access->$key." NOT IN (?)", array($value));
					break;
				default:
					break;
			}
		}
		return $select;
	}
	
	/**
	 * 配列になっているデータを一括でモデルに設定する。
	 * 元データも設定しなおすため、実質的にデータの初期化処理と同じ扱いとなる。
	 */
	protected function setValues($values){
		$this->values_org = array();
		$this->values = array();
		if($values instanceof DatabaseModel){
			$values = $values->values;
		}
		foreach($values as $key => $value){
			$this->values[$key] = $this->values_org[$key] = $value;
		}
	}
	
	/**
	 * 指定したトランザクション内にて主キーベースでデータの保存を行う。
	 * 主キーが存在しない場合は何もしない。
	 * また、モデル内のカラムがDBに無い場合はスキップする。
	 * データ作成日／更新日は自動的に設定される。
	 */
	public function save($db){
		if(!empty($this->primary_keys)){
			// 現在該当のデータが登録されているか調べる。
			$pkset = false;
			$select = new DatabaseSelect($this->access, $db);
			$select->addColumn($this->access->_W);
			foreach($this->primary_keys as $key){
				$select->addWhere($key." = ?", array($this->values[$key]));
				$pkset = true;
			}
			if($pkset){
				$result = $select->execute();
			}else{
				$result = array();
			}
			
			// データ作成日／更新日は自動的に設定する。
			$this->create_time = $this->update_time = date("Y-m-d H:i:s");
			
			if(!is_array($result) || empty($result)){
				// 主キーのデータが無かった場合はInsert
				$insert = new DatabaseInsert($this->access, $db);
				$sqlvals = array();
				foreach($this->columns as $column){
					if(isset($this->values[$column]) && $this->values[$column] != ""){
						$sqlvals[$column] = $this->values[$column];
					}
				}
				// 何かしらの情報が登録されている場合のみ登録処理を実行する。
				if(!empty($sqlvals)){
					$insert->execute($sqlvals);
					foreach($this->primary_keys as $key){
						if(empty($this->values[$key])){
							$this->values[$key] = $this->values_org[$key] = $db->lastInsertId();
						}
					}
				}
			}else{
				// 主キーのデータがあった場合は更新する。
				$update = new DatabaseUpdate($this->access, $db);
				$updateSet = false;
				$updateWhere = false;
				foreach($this->columns as $column){
					if(in_array($column, $this->primary_keys)){
						// 主キーは更新条件
						$update->addWhere($this->access->$column." = ?", array($this->values[$column]));
						$updateWhere = true;
					}elseif($this->values[$column] != $this->values_org[$column]){
						$update->addSets($this->access->$column." = ?", array($this->values[$column]));
						$updateSet = true;
					}
				}
				// WHERE句とSET句の両方が設定されている場合のみ更新処理を実行
				if($updateSet && $updateWhere){
					$update->execute();
				}
			}
		}
	}

	/**
	 * 指定したトランザクション内にて主キーベースでデータの削除を行う。
	 * 主キーが存在しない場合は何もしない。
	 */
	public function delete($db){
		if(!empty($this->primary_keys)){
			$delete = new DatabaseDelete($this->access, $db);
			$deleteWhere = false;
			foreach($this->columns as $column){
				if(in_array($column, $this->primary_keys)){
					// 主キーは削除条件
					$delete->addWhere($this->access->$column." = ?", array($this->values[$column]));
					$deleteWhere = true;
				}
			}
			// WHERE句が設定されている場合のみ削除処理を実行
			if($deleteWhere){
				$delete->execute();
			}
		}
	}
	
	/**
	 * モデルの配列表現を返す。
	 */
	public function toArray(){
		return $this->values;
	}
	
	/**
	 * インスタンスのコピーを新規作成する。
	 */
	public function copy(){
		$thisClass = get_class($this);
		$copy = new $thisClass(array());
		foreach($this->values as $key => $value){
			$copy->$key = $value;
		}
		return $copy;
	}
}
?>
