<?php
// ショッピングカートの設定を取得
LoadModel("ShoppingSettings", "Shopping");

class Shopping_Customer_ShowAll extends FrameworkModule{
	function execute($param){
		$copyKey = $param->get("copy", "copy");
		$customerSessionKey = $params->get("session", "Shopping_Customer");
		$customerResultKey = $param->get("result", "customer");
		
		// 注文者と同じチェックボックス用配列
		$_SERVER["ATTRIBUTES"]["CHECBOX"][$copyKey]["1"] = "配送先は注文者と同じ";
		
		// セッションの顧客情報をパラメータに設定
		$_SERVER["ATTRIBUTES"][$customerResultKey] = $_SESSION[$customerSessionKey];
	}
}
?>
