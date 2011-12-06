<?php
/**
 * ### Shop.Summery.RepeaterOrderTime
 * 受注データでのサマリを取得する。
 * @param title タイトルに相当するカラムを指定
 * @param summery サマリーに相当するカラムを指定
 * @param result 結果を設定する配列のキーワード
 */
class Shop_Summery_RepeaterOrderTime extends FrameworkModule{
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
		$summerys = $order->summeryBy(array(":HOUR(:order_time:):order_time:", ":CASE WHEN :order_repeat: > 1 THEN 1 ELSE 0 END:order_repeat:"), array("subtotal", "total"), $conditions, "order_time");
		$result = array();
		$_POST["time"][] = "24";
		foreach($summerys as $summery){
			if(in_array($summery->order_time, $_POST["time"])){
				foreach($_POST["time"] as $i => $time){
					if($time == $summery->order_time){
						$summery->order_time_text = $summery->order_time.":00〜".($_POST["time"][$i + 1] - 1).":59";
						if(!isset($result[$i])){
							$result[$i] = array($loader->loadModel("RepeaterOrderModel"), $loader->loadModel("RepeaterOrderModel"));
							$result[$i][0]->order_time = $result[$i][1]->order_time = $time;
							$result[$i][0]->order_time_text = $result[$i][1]->order_time_text = $time.":00〜".($_POST["time"][$i + 1] - 1).":59";
							$result[$i][0]->order_repeat = 0;
							$result[$i][1]->order_repeat = 1;
							$result[$i][0]->count = $result[$i][1]->count = 0;
							$result[$i][0]->subtotal = $result[$i][1]->subtotal = 0;
							$result[$i][0]->total = $result[$i][1]->total = 0;
						}
						$result[$i][$summery->order_repeat] = $summery;
					}
				}
			}else{
				foreach($_POST["time"] as $i => $time){
					if($time < $summery->order_time && $summery->order_time < $_POST["time"][$i+1]){
						if(!isset($result[$i])){
							$result[$i] = array($loader->loadModel("RepeaterOrderModel"), $loader->loadModel("RepeaterOrderModel"));
							$result[$i][0]->order_time = $result[$i][1]->order_time = $time;
							$result[$i][0]->order_time_text = $result[$i][1]->order_time_text = $time.":00〜".($_POST["time"][$i + 1] - 1).":59";
							$result[$i][0]->order_repeat = 0;
							$result[$i][1]->order_repeat = 1;
							$result[$i][0]->count = $result[$i][1]->count = 0;
							$result[$i][0]->subtotal = $result[$i][1]->subtotal = 0;
							$result[$i][0]->total = $result[$i][1]->total = 0;
						}
						if(!isset($result[$i][$summery->order_repeat])){
							$summery->order_time = $time;
							$summery->order_time_text = $summery->order_time.":00〜".($_POST["time"][$i + 1] - 1).":59";
							$result[$i][$summery->order_repeat] = $summery;
						}else{
							$result[$i][$summery->order_repeat]->count += $summery->count;
							$result[$i][$summery->order_repeat]->subtotal += $summery->subtotal;
							$result[$i][$summery->order_repeat]->total += $summery->total;
						}
					}
				}
			}
		}
		$_SERVER["ATTRIBUTES"][$params->get("result", "orders")] = $result;
	}
}
?>
