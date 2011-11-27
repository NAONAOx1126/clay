<?php
// ショッピングカートの設定を取得
LoadModel("Setting", "Members");
LoadModel("Setting", "Shopping");
LoadModel("DeliveryModel", "Shopping");
LoadModel("PaymentModel", "Shopping");

// 共通処理を呼び出し。

class Shopping_Customer_Total extends FrameworkModule{
	function execute(){
		// 商品の合計金額／個別配送料を計算
		$useCommonDelivFee = false;
		$_SESSION[CUSTOMER_SESSION_KEY]->subtotal = 0;
		$_SESSION[CUSTOMER_SESSION_KEY]->deliv_fee = 0;
		foreach($_SESSION[CART_SESSION_KEY] as $index => $item){
			$_SESSION[CUSTOMER_SESSION_KEY]->subtotal += $item["sale_price"] * $item["quantity"];
			if($item["deliv_fee"] != ""){
				$_SESSION[CUSTOMER_SESSION_KEY]->deliv_fee += $item["deliv_fee"] * $item["quantity"];
			}else{
				$useCommonDelivFee = true;
			}
		}
		$_SESSION[CUSTOMER_SESSION_KEY]->product_deliv_fee = $_SESSION[CUSTOMER_SESSION_KEY]->deliv_fee;
	
		// 商品共通配送料を適用する商品が一つでもある場合には、商品共通配送料を加算。
		if($useCommonDelivFee){
			// 配送料を計算
			$delivery = new DeliveryModel();
			$delivery->findByDeliveryArea($_SESSION[CUSTOMER_SESSION_KEY]->delivery_id, $_SESSION[CUSTOMER_SESSION_KEY]->pref);
			$_SESSION[CUSTOMER_SESSION_KEY]->delivery_id = $delivery->delivery_id;
			$_SESSION[CUSTOMER_SESSION_KEY]->deliv_fee += $delivery->deliv_fee;
		}

		// 手数料を計算
		$payment = new PaymentModel();
		$payment->findByPaymentTotal($_SESSION[CUSTOMER_SESSION_KEY]->payment_id, $_SESSION[CUSTOMER_SESSION_KEY]->subtotal + $_SESSION[CUSTOMER_SESSION_KEY]->deliv_fee);
		$_SESSION[CUSTOMER_SESSION_KEY]->charge = $payment->charge;
		
		// 購入金額の計算
		$_SESSION["customer"]->total = $_SESSION[CUSTOMER_SESSION_KEY]->subtotal + $_SESSION[CUSTOMER_SESSION_KEY]->deliv_fee + $_SESSION[CUSTOMER_SESSION_KEY]->charge;
	}
}
?>
