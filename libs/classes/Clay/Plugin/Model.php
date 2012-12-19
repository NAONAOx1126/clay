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
 * データベースモデルラッパー用のクラスです。
 *
 * @package Models
 * @author Naohisa Minagawa <info@clay-system.jp>
 */
class Clay_Plugin_Model{
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
		$this->access =& $accessTable;
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
		return $this;
	}
	
	/**
	 * 出力されるデータのオフセットを設定する。
	 */
	public function offset($offset = null){
		$this->offset = $offset;
		return $this;
	}
	
	/**
	 * レコードが作成可能な場合に、レコードを作成します。
	 */
	public function create(){
		$insert = new Clay_Query_InsertIgnore($this->access);
		$sqlvals = array();
		foreach($this->columns as $column){
			if(array_key_exists($column, $this->values) && $this->values[$column] !== null){
				$sqlvals[$column] = $this->values[$column];
			}
		}
		// 何かしらの情報が登録されている場合のみ登録処理を実行する。
		if(!empty($sqlvals)){
			// データ作成日／更新日は自動的に設定する。
			$sqlvals["create_time"] = $sqlvals["update_time"] = date("Y-m-d H:i:s");
			$insert->execute($sqlvals);
			foreach($this->primary_keys as $key){
				if(empty($this->values[$key])){
					$this->values[$key] = $this->values_org[$key] = $insert->lastInsertId();
				}
			}
		}
		return $this;
	}
	
	/**
	 * レコードを特定のキーで検索する。
	 * 複数件ヒットした場合は、最初の１件をデータとして取得する。
	 */
	public function findBy($values = array()){
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
	public function findAllBy($values = array(), $order = "", $reverse = false){
		$select = new Clay_Query_Select($this->access);
		$select->addColumn($this->access->_W);
		if(is_array($values)){
			foreach($values as $key => $value){
				$select = $this->appendWhere($select, $key, $value);
			}
		}
		if(!empty($order)){
			if(is_array($order)){
				foreach($order as $index => $ord){
					if(is_array($reverse)){
						if(isset($reverse[$index])){
							$select->addOrder($ord, $reverse[$index]);
						}else{
							$select->addOrder($ord, false);
						}
					}else{
						$select->addOrder($ord, $reverse);
					}
				}
			}else{
				if(is_array($reverse)){
					if(isset($reverse[0])){
						$select->addOrder($order, $reverse[0]);
					}else{
						$select->addOrder($order, false);
					}
				}else{
					$select->addOrder($order, $reverse);
				}
			}
		}
		$select->setLimit($this->limit, $this->offset);
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
	public function countBy($values = array()){
		$select = new Clay_Query_Select($this->access);
		$select->addColumn("COUNT(*) AS count");
		if(is_array($values)){
			foreach($values as $key => $value){
				$select = $this->appendWhere($select, $key, $value);
			}
		}
		$result = $select->execute();
		
		if(count($result) > 0){
			return $result[0]["count"];
		}else{
			return "0";
		}
	}
	
	public function summeryByArray($groups, $targets, $values = array(), $order = "", $columns = array()){
		$select = new Clay_Query_Select($this->access);
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
		foreach($columns as $column){
			if(!empty($column)){
				if(substr($column, 0, 1) == ":" && substr($column, -1) == ":"){
					$column = str_replace(";",",", $column);
					$arrColumn = explode(":", substr($column, 1, -1));
					$column_name = array_pop($arrColumn);
					$column = "";
					for($i = 0; $i < count($arrColumn); $i ++){
						if($i % 2 == 0){
							$column .= $arrColumn[$i];
						}else{
							$name = $arrColumn[$i];
							$column .= $this->access->$name;
						}
					}
					$select->addColumn($column, $column_name);
				}else{
					$select->addColumn($this->access->$column);
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
			if(!empty($target)){
				if(substr($target, 0, 1) == ":" && substr($target, -1) == ":"){
					$target = str_replace(";",",", $target);
					$arrTarget = explode(":", substr($target, 1, -1));
					$target_name = array_pop($arrTarget);
					$target = "";
					for($i = 0; $i < count($arrTarget); $i ++){
						if($i % 2 == 0){
							$target .= $arrTarget[$i];
						}else{
							$name = $arrTarget[$i];
							$target .= $this->access->$name;
						}
					}
					$select->addColumn("SUM(".$target.")", $target_name);
				}else{
					$select->addColumn("SUM(".$this->access->$target.")", $target);
				}
			}
		}
		foreach($values as $key => $value){
			$select = $this->appendWhere($select, $key, $value);
		}
		if(!empty($order)){
			if(preg_match("/^rev@/", $order) > 0){
				$select->addOrder(substr($order, 4), true);
			}else{
				$select->addOrder($order, false);			
			}
		}else{
			$select->addOrder("count", true);
		}
		$result = $select->execute($this->limit, $this->offset);
		
		return $result;
	}
	
	public function summeryBy($groups, $targets, $values = array(), $order = "", $columns = array()){
		$result = $this->summeryByArray($groups, $targets, $values, $order, $columns);
		
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
			list($op, $key, $default) = explode(":", $key, 3);
		}else{
			$op = "eq";
		}
		if(strpos($key, "+") > 0){
			$keys = explode("+", $key);
			foreach($keys as $index => $key){
				$keys[$index] = $this->access->$key;
			}
			$fullkey = "CONCAT(".implode(", ", $keys).")";
		}else{
			$fullkey = $this->access->$key;
			if(isset($default)){
				if(is_numeric($default) && (substr($default, 0, 1) != "0" || strlen($default) == 1)){
					// 全て数字で先頭が0でない、もしくは1桁のみの場合は数値データとして扱う
					$fullkey = "COALESCE(".$fullkey.", ".$default.")";
				}else{
					$fullkey = "COALESCE(".$fullkey.", '".$default."')";
				}
			}
		}
		if(in_array($key, $this->columns)){
			switch($op){
				case "eq":
					if($value == null){
						$select->addWhere($fullkey." IS NULL");
					}else{
						$select->addWhere($fullkey." = ?", array($value));
					}
					break;
				case "ne":
					if($value == null){
						$select->addWhere($fullkey." IS NOT NULL");
					}else{
						$select->addWhere($fullkey." != ?", array($value));
					}
					break;
				case "gt":
					$select->addWhere($fullkey." > ?", array($value));
					break;
				case "ge":
					$select->addWhere($fullkey." >= ?", array($value));
					break;
				case "lt":
					$select->addWhere($fullkey." < ?", array($value));
					break;
				case "le":
					$select->addWhere($fullkey." <= ?", array($value));
					break;
				case "like":
					$select->addWhere($fullkey." LIKE ?", array($value));
					break;
				case "part":
					$select->addWhere($fullkey." LIKE ?", array("%".$value."%"));
					break;
				case "for":
					$select->addWhere($fullkey." LIKE ?", array("%".$value));
					break;
				case "back":
					$select->addWhere($fullkey." LIKE ?", array($value."%"));
					break;
				case "nlike":
					$select->addWhere($fullkey." NOT LIKE ?", array($value));
					break;
				case "in":
					if(!is_array($value)){
						$value = array($value);
					}
					$placeholders = "";
					foreach($value as $v){
						if(!empty($placeholders)){
							$placeholders .= ",";
						}
						$placeholders .= "?";
					}
					$select->addWhere($fullkey." in (".$placeholders.")", $value);
					break;
				case "nin":
					if(!is_array($value)){
						$value = array($value);
					}
					$placeholders = "";
					foreach($value as $v){
						if(!empty($placeholders)){
							$placeholders .= ",";
						}
						$placeholders .= "?";
					}
					$select->addWhere($fullkey." NOT IN (".$placeholders.")", $value);
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
		if($values instanceof Clay_Plugin_Model){
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
	public function save(){
		if(!empty($this->primary_keys)){
			// 現在該当のデータが登録されているか調べる。
			$pkset = false;
			$select = new Clay_Query_Select($this->access);
			$select->addColumn($this->access->_W);
			foreach($this->primary_keys as $key){
				if(isset($this->values[$key])){
					$select->addWhere($key." = ?", array($this->values[$key]));
				}else{
					$select->addWhere($key." IS NULL", array());
				}
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
				// 主キーのデータが無かった場合はデータを作成する。
				$this->create();
			}else{
				// 主キーのデータがあった場合は更新する。
				$update = new Clay_Query_Update($this->access);
				$updateSet = false;
				$updateWhere = false;
				foreach($this->columns as $column){
					if(in_array($column, $this->primary_keys)){
						// 主キーは更新条件
						$update->addWhere($this->access->$column." = ?", array($this->values[$column]));
						$updateWhere = true;
					}elseif(array_key_exists($column, $this->values) && (!array_key_exists($column, $this->values_org) || $this->values[$column] != $this->values_org[$column])){
						if(array_key_exists($column, $this->values) && $this->values[$column] !== null){
							$update->addSets($this->access->$column." = ?", array($this->values[$column]));
						}else{
							$update->addSets($this->access->$column." = NULL", array());
						}
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
	 * 指定したトランザクション内にて主キーベースでデータの保存を行う。
	 * 主キーが存在しない場合は何もしない。
	 * また、モデル内のカラムがDBに無い場合はスキップする。
	 * データ作成日／更新日は自動的に設定される。
	 */
	public function saveAll($list){
		// 主キーのデータが無かった場合はInsert
		$insert = new Clay_Query_InsertIgnore($this->access);
		foreach($list as $index => $data){
			// データ作成日／更新日は自動的に設定する。
			$data["create_time"] = $data["update_time"] = date("Y-m-d H:i:s");
			$insert->execute($data);
			foreach($this->primary_keys as $key){
				if(empty($data[$key])){
					$list[$index][$key] = $insert->lastInsertId();
				}
			}
		}
		return $list;
	}

	/**
	 * 指定したトランザクション内にて主キーベースでデータの削除を行う。
	 * 主キーが存在しない場合は何もしない。
	 */
	public function delete(){
		if(!empty($this->primary_keys)){
			$delete = new Clay_Query_Delete($this->access);
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
