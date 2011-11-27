<?php
// この機能で使用するモデルクラス
LoadModel("Setting", "Members");
LoadModel("TypeModel", "Members");

class Members_Types extends FrameworkModule{
	function execute($params){
		// 顧客種別のリストを取得
		$result = TypeModel::getSelectable($_SESSION[CUSTOMER_SESSION_KEY]->customer_id);
	
		// 結果として渡す。
		$_SERVER["ATTRIBUTES"][$params->get("result", "customer_types")] = $result;
	}
}
?>