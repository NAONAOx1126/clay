<?php
// 共通処理を呼び出し。
LoadModel("Setting", "Shopping");
LoadModel("PrefModel");
LoadModel("MailTemplateModel");
LoadModel("CustomerModel", "Members");
LoadModel("CustomerTypeModel", "Members");
LoadModel("PaymentModel", "Shopping");
LoadModel("TempOrderModel", "Shopping");
LoadModel("TempOrderDetailModel", "Shopping");
LoadModel("OrderModel", "Shopping");
LoadModel("OrderDetailModel", "Shopping");
LoadModel("ProductModel", "Shopping");
LoadModel("ProductOptionModel", "Shopping");

class Shopping_Shopping_CheckContents extends FrameworkModule{
	function execute($params){
		// コンテンツ商品用に一度購入した商品を除外する処理。
		$orderDetails = OrderDetailModel::getCustomerOrders($_SESSION[CUSTOMER_SESSION_KEY]->customer_id);
		foreach($orderDetails as $detail){
			foreach($_SESSION[CART_SESSION_KEY] as $index => $cart){
				if(!empty($detail->product_id) && $detail->product_id != $cart["product_id"]){
					continue;
				}
				if(!empty($detail->option1_id) && $detail->product_id != $cart["option1_id"]){
					continue;
				}
				if(!empty($detail->option2_id) && $detail->product_id != $cart["option2_id"]){
					continue;
				}
				if(!empty($detail->option3_id) && $detail->product_id != $cart["option3_id"]){
					continue;
				}
				if(!empty($detail->option4_id) && $detail->product_id != $cart["option4_id"]){
					continue;
				}
				if(!empty($detail->option5_id) && $detail->product_id != $cart["option5_id"]){
					continue;
				}
				if(!empty($detail->option6_id) && $detail->product_id != $cart["option6_id"]){
					continue;
				}
				if(!empty($detail->option7_id) && $detail->product_id != $cart["option7_id"]){
					continue;
				}
				if(!empty($detail->option8_id) && $detail->product_id != $cart["option8_id"]){
					continue;
				}
				if(!empty($detail->option9_id) && $detail->product_id != $cart["option9_id"]){
					continue;
				}
				unset($_SESSION[CART_SESSION_KEY][$index]);
				$_SESSION[CART_SESSION_KEY] = array_values($_SESSION[CART_SESSION_KEY]);
			}
		}
	}
}
?>
