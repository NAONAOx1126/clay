<?php
// ショッピングカートの設定を取得
LoadModel("Setting", "Shopping");

class Shopping_Shopping_PaymentAdd extends FrameworkModule{
	function execute($params){
		// 決済方法のチェックが無い場合にはパラメータとして渡ってこないため、強制的に設定する。
		if(isset($_POST["payment_id"])){
			$_SESSION[CUSTOMER_SESSION_KEY]->payment_id = $_POST["payment_id"];
		}elseif($params->check("payment")){
			$_SESSION[CUSTOMER_SESSION_KEY]->payment_id = $params->get("payment");
		}	
	}
}
?>
