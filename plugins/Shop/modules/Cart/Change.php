<?php
// ショッピングカートの設定を取得
LoadModel("ShoppingSettings", "Shopping");

class Shopping_Cart_Change extends FrameworkModule{
	function execute($param){
		// パラメータを取得する。
		$mode = $param->get("mode", "change_cart");
		$cartSessionKey = $param->get("session", "Shopping_Cart");
		
		// カートの商品の数量を変更する。
		if(!empty($_POST[$mode])){
			if(isset($_POST["index"]) && !empty($_POST["quantity"])){
				$_SESSION[$cartSessionKey][$_POST["index"]]["quantity"] = $_POST["quantity"];
			}
		}
	}
}
?>
