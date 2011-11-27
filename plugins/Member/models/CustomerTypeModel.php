<?php
// この処理で使用するテーブルモデルをインクルード
LoadTable("CustomerTypesTable", "Members");
LoadTable("TypesTable", "Members");

/**
 * 顧客種別情報のモデルクラス
 */
class CustomerTypeModel extends DatabaseModel{
	function __construct($values = array()){
		parent::__construct(new CustomerTypesTable(), $values);
	}
	
	function findByPrimaryKey($customer_id, $type_id){
		$this->findBy(array("customer_id" => $customer_id, "type_id" => $type_id));
	}
	
	function findAllByCustomer($customer_id){
		$result = $this->findAllBy(array("customer_id" => $customer_id));
		$types = array();
		if(is_array($result)){
			foreach($result as $data){
				$types[$data->type_id] = $data;
			}
		}
		return $types;
	}
}
?>