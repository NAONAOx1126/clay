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
 * データベースにセッション情報を持たせるためのハンドラです。
 *
 * @package Session
 * @author Naohisa Minagawa <info@clay-system.jp>
 */
class Clay_Session_Handler_Database extends Clay_Session_Handler{
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
		$loader = new Clay_Plugin($module);
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
		$select = new Clay_Query_Select($this->table);
		$select->addColumn($this->table->_W);
		$select->addWhere($this->table->$id_key." = ?", array($id));
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
		
		// セッションに値を設定
		try{
			$insert = new Clay_Query_Replace($this->table);
			$sqlval = array($id_key => $id, $data_key => $sess_data);
			$sqlval["create_time"] = $sqlval["update_time"] = date("Y-m-d H:i:s");
			Clay_Logger::writeDebug($insert->showQuery($sqlval));
			$insert->execute($sqlval);
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
		
		// セッションに値を設定
		try{
			$delete = new Clay_Query_Delete($this->table);
			$delete->addWhere($this->table->$id_key." = ?", array($id));
			Clay_Logger::writeDebug($delete->showQuery());
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
		
		// セッションに値を設定
		try{
			$delete = new Clay_Query_Delete($this->table);
			$delete->addWhere($this->table->update_time." < ?", array($limit));
			Clay_Logger::writeDebug($delete->showQuery());
			$delete->execute();
			return true;
		}catch(Exception $e){
			return false;
		}
	}
}
 
