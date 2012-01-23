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
		$result_new = array();
		$result_repeat = array();
		foreach($_POST["time"] as $i => $t){
			if($t > 23){
				unset($_POST["time"][$i]);
			}
		}
		if(!isset($_POST["time"]) || !is_array($_POST["time"]) || empty($_POST["time"])){
			$_POST["time"] = array("6", "9", "12", "15", "18", "21");
		}
		if($_POST["time"][0] != "0"){
			array_unshift($_POST["time"], "0");
		}
		$_POST["time"][count($_POST["time"])] = "24";
		foreach($summerys as $summery){
			if(in_array($summery->order_time, $_POST["time"])){
				foreach($_POST["time"] as $i => $time){
					if($time == $summery->order_time){
						if(!isset($result[$i])){
							$result[$i] = $result_new[$i] = $result_repeat[$i] = array("order_time" => $time, "order_time_text" => $time.":00〜".($_POST["time"][$i + 1] - 1).":59", "count" => 0, "subtotal" => 0, "total" => 0);
						}
						if($summery->order_repeat == 0){
							$result_new[$i]["count"] += $summery->count;
							$result_new[$i]["subtotal"] += $summery->subtotal;
							$result_new[$i]["total"] += $summery->total;
						}else{
							$result_repeat[$i]["count"] += $summery->count;
							$result_repeat[$i]["subtotal"] += $summery->subtotal;
							$result_repeat[$i]["total"] += $summery->total;
						}
						$result[$i]["count"] += $summery->count;
						$result[$i]["subtotal"] += $summery->subtotal;
						$result[$i]["total"] += $summery->total;
					}
				}
			}else{
				foreach($_POST["time"] as $i => $time){
					if($time < $summery->order_time && $summery->order_time < $_POST["time"][$i+1]){
						if(!isset($result[$i])){
							$result[$i] = $result_new[$i] = $result_repeat[$i] = array("order_time" => $time, "order_time_text" => $time.":00〜".($_POST["time"][$i + 1] - 1).":59", "count" => 0, "subtotal" => 0, "total" => 0);
						}
						if($summery->order_repeat == 0){
							$result_new[$i]["count"] += $summery->count;
							$result_new[$i]["subtotal"] += $summery->subtotal;
							$result_new[$i]["total"] += $summery->total;
						}else{
							$result_repeat[$i]["count"] += $summery->count;
							$result_repeat[$i]["subtotal"] += $summery->subtotal;
							$result_repeat[$i]["total"] += $summery->total;
						}
						$result[$i]["count"] += $summery->count;
						$result[$i]["subtotal"] += $summery->subtotal;
						$result[$i]["total"] += $summery->total;
					}
				}
			}
		}
		$_SERVER["ATTRIBUTES"]["times"] = $_POST["time"];
		$_SERVER["ATTRIBUTES"][$params->get("result", "orders")] = $result;
		$_SERVER["ATTRIBUTES"][$params->get("result", "orders")."_new"] = $result_new;
		$_SERVER["ATTRIBUTES"][$params->get("result", "orders")."_repeat"] = $result_repeat;
	}
}
?>
