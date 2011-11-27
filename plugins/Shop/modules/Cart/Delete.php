<?php
// ショッピングカートの設定を取得
LoadModel("ShoppingSettings", "Shopping");

class Shopping_Cart_Delete extends FrameworkModule{
	function execute($param){
		// パラメータを取得する。
		$mode = $param->get("mode", "delete_cart");
		$cartSessionKey = $param->get("session", "Shopping_Cart");
		
		// カートから商品を削除する。
		if(!empty($_POST[$mode])){
			if(isset($_POST["index"])){
				unset($_SESSION[$cartSessionKey][$_POST["index"]]);
			}
		}
	}
}
?>
