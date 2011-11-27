<?php
// この処理で使用するテーブルモデルをインクルード
LoadModel("CustomerModel", "Members");
LoadTable("PointLogsTable", "Shopping");

/**
 * 顧客情報のモデルクラス
 */
class PointLogModel extends DatabaseModel{
	function __construct($values = array()){
		parent::__construct(new PointLogsTable(), $values);
	}
	
	function findByPrimaryKey($log_id){
		$this->findBy(array("point_log_id" => $log_id));
	}
	
	function getCustomerLog($cutomer_id){
		$this->findBy(array("customer_id" => $customer_id));
	}
	
	function addPoint($db, $customer_id, $point){
		$customer = new CustomerModel();
		$customer->findByPrimaryKey($customer_id);
		if($customer->point + $point >= 0){
			// データを登録する。
			$customer->point += $point;
			$customer->save($db);
			
			// ログを追加
			$this->log_time = date("Y-m-d H:i:s");
			$this->customer_id = $customer_id;
			$this->point = $point;
			$customer->save($db);
			
			return true;
		}
		return false;
	}
}
?>