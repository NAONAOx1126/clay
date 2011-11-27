<?php
// この処理で使用するテーブルモデルをインクルード
LoadTable("CustomerDeliversTable", "Members");

/**
 * 顧客種別情報のモデルクラス
 */
class CustomerDeliverModel extends DatabaseModel{
	function __construct($values = array()){
		parent::__construct(new CustomerDeliversTable(), $values);
	}
	
	function findByPrimaryKey($customer_deliver_id){
		$this->findBy(array("customer_deliver_id" => $customer_deliver_id));
	}
	
	function findAllByCustomer($customer_id){
		return $this->findAllBy(array("customer_id" => $customer_id));
	}
}
?>