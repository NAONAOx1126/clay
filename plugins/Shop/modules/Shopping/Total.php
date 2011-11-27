<?php
// ショッピングカートの設定を取得
LoadModel("Setting", "Members");
LoadModel("Setting", "Shopping");
LoadModel("DeliveryModel", "Shopping");
LoadModel("PaymentModel", "Shopping");

// 共通処理を呼び出し。
class Shopping_Shopping_Total extends FrameworkModule{
	function execute($params){
		// 商品の合計金額／個別配送料を計算
		$useCommonDelivFee = false;
		$_SESSION[CUSTOMER_SESSION_KEY]->subtotal = 0;
		$_SESSION[CUSTOMER_SESSION_KEY]->deliv_fee = 0;
		$_SESSION[CUSTOMER_SESSION_KEY]->add_point = 0;
		foreach($_SESSION[CART_SESSION_KEY] as $index => $item){
			$_SESSION[CUSTOMER_SESSION_KEY]->subtotal += $item["sale_price"] * $item["quantity"];
			$_SESSION[CUSTOMER_SESSION_KEY]->add_point += floor($item["sale_price"] * $item["quantity"] * $item["point_rate"] / 100);
			if($item["deliv_fee"] != ""){
				$_SESSION[CUSTOMER_SESSION_KEY]->deliv_fee += $item["deliv_fee"] * $item["quantity"];
			}else{
				$useCommonDelivFee = true;
			}
		}
		$_SESSION[CUSTOMER_SESSION_KEY]->product_deliv_fee = $_SESSION[CUSTOMER_SESSION_KEY]->deliv_fee;
	
		// 商品共通配送料を適用する商品が一つでもある場合には、商品共通配送料を加算。
		if($useCommonDelivFee && !empty($_SESSION[CUSTOMER_SESSION_KEY]->delivery_id)){
			// 配送料を計算
			$delivery = new DeliveryModel();
			$delivery->findByDeliveryArea($_SESSION[CUSTOMER_SESSION_KEY]->delivery_id, $_SESSION[CUSTOMER_SESSION_KEY]->pref);
			$_SESSION[CUSTOMER_SESSION_KEY]->delivery_id = $delivery->delivery_id;
			$_SESSION[CUSTOMER_SESSION_KEY]->deliv_fee += $delivery->deliv_fee;
		}

		// 手数料を計算
		if(!empty($_SESSION[CUSTOMER_SESSION_KEY]->delivery_id)){
			$payment = new PaymentModel();
			$payment->findByPaymentTotal($_SESSION[CUSTOMER_SESSION_KEY]->payment_id, $_SESSION[CUSTOMER_SESSION_KEY]->subtotal + $_SESSION[CUSTOMER_SESSION_KEY]->deliv_fee);
			$_SESSION[CUSTOMER_SESSION_KEY]->charge = $payment->charge;
		}
		
		// 購入金額の計算
		$_SESSION[CUSTOMER_SESSION_KEY]->total = $_SESSION[CUSTOMER_SESSION_KEY]->subtotal + $_SESSION[CUSTOMER_SESSION_KEY]->deliv_fee + $_SESSION[CUSTOMER_SESSION_KEY]->charge;
		
		// 全額ポイント支払いの設定がされている場合は利用ポイントを上書き
		if($params->get("point", "0") == "1" || $payment->point_flg == "1"){
			// 全額ポイント支払い設定 or 全額ポイント支払い決済方法の場合、購入費用分ポイントで充当する。足りなければエラー
			if($_SESSION[CUSTOMER_SESSION_KEY]->total <= $_SESSION[CUSTOMER_SESSION_KEY]->point){
				$_POST["use_point"] = $_SESSION[CUSTOMER_SESSION_KEY]->total;
			}else{
				throw new InvalidException(array("購入に必要なポイントが不足しています。"));
			}
		}
		
		// 利用ポイントを設定
		if(!isset($_POST["use_point"])){
			$_POST["use_point"] = 0;
		}
		$_SESSION[CUSTOMER_SESSION_KEY]->use_point = $_POST["use_point"];
		
		// 支払い合計金額を計算
		$_SESSION[CUSTOMER_SESSION_KEY]->payment_total = $_SESSION[CUSTOMER_SESSION_KEY]->total - $_SESSION[CUSTOMER_SESSION_KEY]->use_point;
	}
}
?>
