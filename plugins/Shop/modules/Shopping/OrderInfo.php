<?php
// ショッピングカートの設定を取得
LoadModel("Setting", "Shopping");

class Shopping_Shopping_OrderInfo extends FrameworkModule{
	function execute($param){
		$order = new TempOrderModel();
		$order->findByPrimaryKey($_POST["order_id"]);
		if(empty($order->order_id)){
			$order = new OrderModel();
			$order->findByPrimaryKey($_POST["order_id"]);
		}
		
		$_SERVER["ATTRIBUTES"]["order"] = $order;
	}
}
?>
