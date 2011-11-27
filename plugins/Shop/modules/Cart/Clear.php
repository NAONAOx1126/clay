<?php
// ショッピングカートの設定を取得
LoadModel("Setting", "Shopping");

class Shopping_Cart_Clear extends FrameworkModule{
	function execute($param){
		// パラメータを取得する。
		$mode = $param->get("mode", "clear_cart");
		
		// カートから商品をクリアする。
		if(!empty($_POST[$mode])){
			$_SESSION[CART_SESSION_KEY] = array();
		}
	}
}
?>
