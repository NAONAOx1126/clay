<?php
// この処理で使用するテーブルモデルをインクルード
LoadTable("TempOrderDetailsTable", "Shopping");

/**
 * 顧客情報のモデルクラス
 */
class TempOrderDetailModel extends DatabaseModel{
	function __construct($values = array()){
		parent::__construct(new TempOrderDetailsTable(), $values);
	}
	
	function findByPrimaryKey($order_id, $product_id, $option_ids = null, $option2_id = null, $option3_id = null, $option4_id = null, $option5_id = null, $option6_id = null, $option7_id = null, $option8_id = null, $option9_id = null){
		$this->findBy(array("order_id" => $order_id, "product_id" => $product_id, "option1_id" => $option1_id, "option2_id" => $option2_id, "option3_id" => $option3_id, "option4_id" => $option4_id, "option5_id" => $option5_id, "option6_id" => $option6_id, "option7_id" => $option7_id, "option8_id" => $option8_id, "option9_id" => $option9_id));
	}
		
	function getOrderDetails($order_id){
		$orderDetails = new TempOrderDetailsTable();
		$select = new DatabaseSelect($orderDetails);
		$select->addColumn($orderDetails->_W);
		$select->addWhere($orderDetails->order_id." = ?", array($order_id));
		$result = $select->execute();
		$details = array();
		if(is_array($result)){
			foreach($result as $data){
				$details[] = new TempOrderDetailModel($data);
			}
		}
		return $details;
	}
}
?>