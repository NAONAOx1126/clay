<?php
/**
 * ### Shop.Summery.OrderTime
 * 受注データでのサマリを取得する。
 * @param title タイトルに相当するカラムを指定
 * @param summery サマリーに相当するカラムを指定
 * @param result 結果を設定する配列のキーワード
 */
class Shop_Summery_OrderTime extends FrameworkModule{
	function execute($params){
		// データ一括取得のため、処理期限を無効化
		ini_set("max_execution_time", 0);
		
		// ローダーを初期化
		$loader = new PluginLoader("Shop");
		
		$order = $loader->loadModel("OrderModel");
		
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
		$targets = explode(",", $params->get("summery"));
		$summerys = $order->summeryBy(array(":HOUR(:order_time:):order_time:"), array("subtotal", "total"), $conditions, "order_time");
		$result = array();
		$_POST["time"][] = "24";
		foreach($summerys as $summery){
			if(in_array($summery->order_time, $_POST["time"])){
				foreach($_POST["time"] as $i => $time){
					if($time == $summery->order_time){
						$summery->order_time_text = $summery->order_time.":00〜".($_POST["time"][$i + 1] - 1).":59";
						$result[$i] = $summery;
					}
				}
			}else{
				foreach($_POST["time"] as $i => $time){
					if($time < $summery->order_time && $summery->order_time < $_POST["time"][$i+1]){
						if(!isset($result[$i])){
							$summery->order_time = $time;
							$summery->order_time_text = $summery->order_time.":00〜".($_POST["time"][$i + 1] - 1).":59";
							$result[$i] = $summery;
						}else{
							$result[$i]->count += $summery->count;
							$result[$i]->subtotal += $summery->subtotal;
							$result[$i]->total += $summery->total;
						}
					}
				}
			}
		}
		$_SERVER["ATTRIBUTES"][$params->get("result", "orders")] = $result;
	}
}
?>
