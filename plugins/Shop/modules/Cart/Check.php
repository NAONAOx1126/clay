<?php
// ショッピングカートの設定を取得
LoadModel("ShoppingSettings", "Shopping");

class Shopping_Cart_Check extends FrameworkModule{
	function execute($param){
		// パラメータを取得する。
		$mode = $param->get("mode", "check");
		$cartSessionKey = $param->get("session", "Shopping_Cart");
		
		// カートに商品を追加する。
		if(!empty($_POST[$mode])){
			if(is_array($_SESSION[$cartSessionKey])){
				$existSameProduct = false;
				foreach($_SESSION[$cartSessionKey] as $index => $data){
					// 商品IDやオプションIDがわずかでも違えば、違う商品として追加する。
					if($data["product_id"] != $_POST["product_id"]){
						continue;
					}elseif($data["option1_id"] != $_POST["option1_id"]){
						continue;
					}elseif($data["option2_id"] != $_POST["option2_id"]){
						continue;
					}elseif($data["option3_id"] != $_POST["option3_id"]){
						continue;
					}elseif($data["option4_id"] != $_POST["option4_id"]){
						continue;
					}elseif($data["option5_id"] != $_POST["option5_id"]){
						continue;
					}elseif($data["option6_id"] != $_POST["option6_id"]){
						continue;
					}elseif($data["option7_id"] != $_POST["option7_id"]){
						continue;
					}elseif($data["option8_id"] != $_POST["option8_id"]){
						continue;
					}elseif($data["option9_id"] != $_POST["option9_id"]){
						continue;
					}
					$existSameProduct = true;
					break;
				}
				if($existSameProduct){
					unset($_POST[$mode]);
					throw new InvalidException(array(SHOPPING_MESSAGE_ALREADY_CART));
				}
			}
		}
	}
}
?>
