<?php
// この処理で使用するテーブルモデルをインクルード
LoadTable("CustomerOptionsTable", "Members");

/**
 * 顧客情報のモデルクラス
 */
class CustomerOptionModel extends DatabaseModel{
	function __construct($values = array()){
		parent::__construct(new CustomerOptionsTable(), $values);
	}
	
	function findByPrimaryKey($customer_id, $option_name){
		$this->findBy(array("customer_id" => $customer_id, "option_name" => $option_name));
	}
	
	function findByKeyValue($option_name, $option_value){
		$this->findBy(array("option_name" => $option_name, "option_value" => $option_value));
	}
	
	function findAllByName($option_name){
		return $this->findAllBy(array("option_name" => $option_name));
	}
	
	function findAllByCustomer($customer_id){
		return $this->findAllBy(array("customer_id" => $customer_id));
	}
	
	function getOptionArrayByCustomer($customer_id){
		$result = $this->findAllBy(array("customer_id" => $customer_id));
		$options = array();
		if(is_array($result)){
			foreach($result as $data){
				$options[$data->option_name] = new CustomerOptionModel($data);
			}
		}
		return $options;
	}
}
?>