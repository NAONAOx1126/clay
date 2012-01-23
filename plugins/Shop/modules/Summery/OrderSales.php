<?php
/**
 * ### Shop.Summery.OrderSales
 * 受注データでのサマリを取得する。
 * @param title タイトルに相当するカラムを指定
 * @param summery サマリーに相当するカラムを指定
 * @param result 結果を設定する配列のキーワード
 */
class Shop_Summery_OrderSales extends FrameworkModule{
	function execute($params){
		// ローダーを初期化
		$loader = new PluginLoader("Shop");
		
		$order = $loader->loadModel("RepeaterOrderModel");
		
		// パラメータのsortを並び順変更のキーとして利用
		$sortKey = $_POST[$params->get("order", "order")];
		unset($_POST[$params->get("order", "order")]);
		$conditions = array();
		foreach($_POST as $key => $value){
			if(!empty($value)){
				$conditions[$key] = $value;
			}
		}
		
		// 取得する件数の上限をページャのオプションに追加
		$groups = explode(",", $params->get("title"));
		$targets = array("subtotal", "total");
		$summerys = $order->summeryByArray($groups, $targets, $conditions);
		
		$result = array();
		foreach($summerys as $summery){
			if(($summery["subtotal"] >= (isset($_POST["subtotal_min"])?$_POST["subtotal_min"]:"0") && ($summery["total"] >= (isset($_POST["total_min"])?$_POST["total_min"]:"0")))){
				$result[] = $summery;
			}
		}
		$_SERVER["ATTRIBUTES"][$params->get("result", "orders")] = $result;
	}
}
?>
