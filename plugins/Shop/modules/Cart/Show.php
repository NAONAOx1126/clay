<?php
// ショッピングカートの設定を取得
LoadModel("ShoppingSettings", "Shopping");

// 共通処理を呼び出し
LoadModel("Calculations", "Shopping");

class Shopping_Cart_Show extends FrameworkModule{
	function execute($param){
		// パラメータを取得する。
		$cartSessionKey = $param->get("session_cart", "Shopping_Cart");
		$customerSessionKey = $params->get("session_customer", "Shopping_Customer");
		$cartResultKey = $param->get("result_cart", "cart");
		$customerResultKey = $param->get("result_customer", "customer");
		
		// カートが空の場合は例外をスローする。
		if(empty($_SESSION[$cartSessionKey])){
			throw new InvalidException(array(SHOPPING_MESSAGE_EMPTY_CART));
		}

		// customerの配列が空の場合は、same_orderのフラグをたてる。
		if(empty($_SESSION[$customerSessionKey])){
			$_SESSION[$customerSessionKey] = array("same_order" => "1");
		}
		
		// 商品合計金額の計算
		$_SESSION[$customerSessionKey] = Calculations::Total($_SESSION[$cartSessionKey], $_SESSION[$customerSessionKey]);
		
		// カートの中身を取得して、変数に格納する。
		$_SERVER["ATTRIBUTES"][$cartResultKey] = $_SESSION[$cartSessionKey];
		// セッションの顧客情報をパラメータに設定
		$_SERVER["ATTRIBUTES"][$customerResultKey] = $_SESSION[$customerSessionKey];
	}
}
?>
