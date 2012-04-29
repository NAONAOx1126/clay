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

class SessionManager{
    public static function create($handler){
        session_set_save_handler(
            array($handler, "open"),
            array($handler, "close"),
            array($handler, "read"),
            array($handler, "write"),
            array($handler, "destroy"),
            array($handler, "clean")
        );
    }
}

abstract class SessionHandler{
	abstract public function open($savePath, $sesionName);
	
	abstract public function close();
	
	abstract public function read($id);
	
	abstract public function write($id, $data);
	
	abstract public function destroy($id);
	
	abstract public function clean($maxlifetime);
}

class DatabaseSessionHandler extends SessionHandler{
	private $table;
	
	private $id_key;
	
	private $data_key;
	
	/**
	 * コンストラクタ
	 */
	public function __construct($table = "session_stores", $id_key = "session_id", $data_key = "session_data"){
		list($module, $name) = explode("_", $table, 2);
		$names = explode("_", $name);
		$name = "";
		$module = strtoupper(substr($module, 0, 1)).strtolower(substr($module, 1));
		foreach($names as $part){
			$name .= strtoupper(substr($part, 0, 1)).strtolower(substr($part, 1));
		}
		$name .= "Table";
		$loader = new PluginLoader($module);
		$this->table = $loader->loadTable($name);
		$this->id_key = $id_key;
		$this->data_key = $data_key;
	}
	
	/**
	* セッションを開始する.
	*
	* @param string $save_path セッションを保存するパス(使用しない)
	* @param string $session_name セッション名(使用しない)
	* @return bool セッションが正常に開始された場合 true
	*/
	public function open($savePath, $sesionName){
		return true;
	}

	/**
	 * セッションを閉じる.
	 * 
	 * @return bool セッションが正常に終了した場合 true
	 */
	function close() {
		return true;
	}

	/**
	 * セッションのデータををDBから読み込む.
	 *
	 * @param string $id セッションID
	 * @return string セッションデータの値
	 */
	function read($id) {
		$id_key = $this->id_key;
		$data_key = $this->data_key;
		
		// セッションデータを取得する。
		$select = new DatabaseSelect($this->table);
		$select->addColumn($this->table->_W);
		$select->addWhere($this->table->$id_key." = ?", array($id));
		Logger::writeDebug($select->showQuery());
		$result = $select->execute();
		return $result[0][$data_key];
	}

	/**
	* セッションのデータをDBに書き込む.
	*
	* @param string $id セッションID
	* @param string $sess_data セッションデータの値
	* @return bool セッションの書き込みに成功した場合 true
	*/
	function write($id, $sess_data){
		$id_key = $this->id_key;
		$data_key = $this->data_key;
		
		// セッションデータが登録済みか調べる。
		$select = new DatabaseSelect($this->table);
		$select->addColumn($this->table->_W);
		$select->addWhere($this->table->$id_key." = ?", array($id));
		Logger::writeDebug($select->showQuery());
		$result = $select->execute();
		
		// トランザクションデータベースの取得
		$db = DBFactory::getConnection();
		
		// セッションに値を設定
		try{
			if(count($result) == 0){
				$insert = new DatabaseInsert($this->table, $db);
				$sqlval = array($id_key => $id, $data_key => $sess_data);
				$sqlval["create_time"] = $sqlval["update_time"] = date("Y-m-d H:i:s");
				Logger::writeDebug($insert->showQuery($sqlval));
				$insert->execute($sqlval);
			}else{
				$update = new DatabaseUpdate($this->table, $db);
				$update->addSets($this->table->$data_key." = ?", array($sess_data));
				$update->addSets($this->table->update_time." = ?", array(date("Y-m-d H:i:s")));
				$update->addWhere($this->table->$id_key." = ?", array($id));
				Logger::writeDebug($update->showQuery());
				$update->execute();
			}
			return true;
		}catch(Exception $e){
			return false;
		}
	}

	/**
	* セッションを破棄する.
	*
	* @param string $id セッションID
	* @return bool セッションを正常に破棄した場合 true
	*/
	function destroy($id) {
		$id_key = $this->id_key;
		
		// トランザクションデータベースの取得
		$db = DBFactory::getConnection();
		
		// セッションに値を設定
		try{
			$delete = new DatabaseDelete($this->table, $db);
			$delete->addWhere($this->table->$id_key." = ?", array($id));
			Logger::writeDebug($delete->showQuery());
			$delete->execute();
			return true;
		}catch(Exception $e){
			return false;
		}
	}

	/**
	* ガーベジコレクションを実行する.
	*
	* 引数 $maxlifetime の代りに 定数 MAX_LIFETIME を使用する.
	*
	* @param integer $maxlifetime セッションの有効期限
	*/
	function clean($maxlifetime) {
		$limit = date("Y-m-d H:i:s", strtotime("-".$maxlifetime." secs"));
		
		// トランザクションデータベースの取得
		$db = DBFactory::getConnection();
		
		// セッションに値を設定
		try{
			$delete = new DatabaseDelete($this->table, $db);
			$delete->addWhere($this->table->update_time." < ?", array($limit));
			Logger::writeDebug($delete->showQuery());
			$delete->execute();
			return true;
		}catch(Exception $e){
			return false;
		}
	}
}
?>