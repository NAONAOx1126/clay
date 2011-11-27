<?php
// この処理で使用するテーブルモデルをインクルード
LoadTable("SerialLogsTable", "Members");

/**
 * 顧客種別情報のモデルクラス
 */
class SerialLogModel extends DatabaseModel{
	function __construct($values = array()){
		parent::__construct(new SerialLogsTable(), $values);
	}
	
	function findAllBySerial($serial){
		$result = $this->findAllBy(array("serial" => $serial));
		return $result;
	}
	
	function findAllByCustomer($customer_id){
		$result = $this->findAllBy(array("customer_id" => $customer_id));
		return $result;
	}
}
?>