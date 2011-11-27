<?php
// ショッピングカートの設定を取得
LoadModel("Setting", "Shopping");

class Shopping_Cart_Add extends FrameworkModule{
	function execute($param){
		// パラメータを取得する。
		$mode = $param->get("mode", "add_cart");
		
		// カートに商品を追加する。
		if(!empty($_POST[$mode])){
			if(!empty($_POST["product_id"])){
				// 販売金額が未設定の場合は定価を販売金額に設定
				if($_POST["sale_price"] == ""){
					$_POST["sale_price"] = $_POST["price"];
				}
				// 購入数量が渡されなかった場合はデフォルトで１を設定する。
				if(empty($_POST["quantity"])){
					$_POST["quantity"] = "1";
				}
				//  購入可能数量のリストを生成する。
				$_POST["quantity_list"] = array();
				if((empty($_POST["sale_limit"]) || $_POST["stock"] < $_POST["sale_limit"]) && $_POST["stock_unlimited"] == "0"){
					$_POST["sale_limit"] = $_POST["stock"];
				}
				// 購入可能数量が100以上の場合は99に制限する。
				if($_POST["sale_limit"] >= 100){
					$_POST["sale_limit"] = "99";
				}
				for($i = 1; $i <= $_POST["sale_limit"]; $i ++){
					$_POST["quantity_list"][$i] = $i;
				}
				// カートに入れた商品をセッションに保存する。ただし、既にカートに入っている場合には追加処理を行わない。
				if(!is_array($_SESSION[CART_SESSION_KEY])){
					$_SESSION[CART_SESSION_KEY] = array();
				}
				if($_POST["quantity"] <= $_POST["stock"] || $_POST["stock_unlimited"] == "1"){
					$_SESSION[CART_SESSION_KEY][] = $_POST;
				}else{
					// 在庫が０の場合はエラー画面に遷移する。
					unset($_POST[$mode]);
					throw new InvalidException(array("該当の商品は在庫がありません。"));
				}
			}else{
				// 販売中の商品情報が取得できなかった場合はエラー画面に遷移する。
				unset($_POST[$mode]);
				throw new InvalidException(array("該当の商品は存在しません。"));
			}
		}
	}
}
?>
