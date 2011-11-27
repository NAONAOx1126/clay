<?php
LoadModel("PaymentSelectItem");

LoadTable("PaymentsTable");

class Shopping_Selections_Deliveries extends FrameworkModule{
	function execute($params){
		if(empty($_SERVER["ATTRIBUTES"]["SELECTION"]["payments"])){
			// 決済方法のプルダウン用リストを生成
			$payments = new PaymentsTable();

			// データベースSELECTモデルの読み込み
			$select = new DatabaseSelect($payments);
			$select->addColumn($payments->_W);
			$result = $select->execute();
		
			// キーがpayment_idの連想配列に格納
			foreach($payments as $row){
				$_SERVER["ATTRIBUTES"]["SELECTION"]["payments"][$row["payment_id"]] = new PaymentSelectItem($row);
			}
		}
	}
}
?>
