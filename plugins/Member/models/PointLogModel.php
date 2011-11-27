<?php
// この処理で使用するテーブルモデルをインクルード
LoadModel("Setting", "Members");
LoadTable("PointLogsTable", "Members");

/**
 * 顧客種別情報のモデルクラス
 */
class PointLogModel extends DatabaseModel{
	function __construct($values = array()){
		parent::__construct(new PointLogsTable(), $values);
	}
	
	function findByPrimaryKey($point_log_id){
		$this->findBy(array("point_log_id" => $point_log_id));
	}
	
	function save($db, $point){
		$this->log_time = date("Y-m-d H:i:s");
		$this->customer_id = $_SESSION[CUSTOMER_SESSION_KEY]->customer_id;
		$this->point = $point;
		parent::save($db);
	}
}
?>