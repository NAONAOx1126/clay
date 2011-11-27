<?php
/**
 * ### Shop.Summery.OrderPayment
 * 受注データでのサマリを取得する。
 * @param title タイトルに相当するカラムを指定
 * @param summery サマリーに相当するカラムを指定
 * @param result 結果を設定する配列のキーワード
 */
class Shop_Summery_OrderPayment extends FrameworkModule{
	function execute($params){
		// データ一括取得のため、処理期限を無効化
		ini_set("max_execution_time", 0);
		
		// ローダーを初期化
		$loader = new PluginLoader("Shop");
		
		$order = $loader->loadModel("OrderPaymentModel");
		
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
		$targets = explode(",", $params->get("summery"));
		$summerys = $order->summeryBy($groups, $targets, $conditions);
		$_SERVER["ATTRIBUTES"][$params->get("result", "orders")] = $summerys;
	}
}
?>
