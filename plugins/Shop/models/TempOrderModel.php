<?php
// この処理で使用するテーブルモデルをインクルード
LoadTable("TempOrdersTable", "Shopping");

/**
 * 顧客情報のモデルクラス
 */
class TempOrderModel extends DatabaseModel{
	function __construct($values = array()){
		parent::__construct(new TempOrdersTable(), $values);
	}
	
	function findByPrimaryKey($order_id){
		$this->findBy(array("order_id" => $order_id));
	}
	
	function findByOrderCode($order_code){
		$this->findBy(array("order_code" => $order_code));
	}
}
?>