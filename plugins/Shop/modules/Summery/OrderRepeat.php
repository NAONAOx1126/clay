<?php
/**
 * ### Shop.Summery.OrderRepeat
 * 受注データでのサマリを取得する。
 * @param title タイトルに相当するカラムを指定
 * @param summery サマリーに相当するカラムを指定
 * @param result 結果を設定する配列のキーワード
 */
class Shop_Summery_OrderRepeat extends FrameworkModule{
	function execute($params){
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
		$summerys = $order->summeryBy(array(":(SELECT COUNT(order_id) FROM shop_orders AS counter WHERE order_email = :order_email: AND order_time > :order_time:):order_count:"), array("subtotal", "total"), $conditions);

		$result = array();
		array_unshift($_POST["repeat"], "0");
		foreach($summerys as $summery){
			if(in_array($summery->order_count, $_POST["repeat"])){
				foreach($_POST["repeat"] as $i => $repeat){
					if($repeat == $summery->order_count){
						if(isset($_POST["repeat"][$i + 1])){
							if($summery->order_count != $_POST["repeat"][$i + 1] - 1){
								$summery->order_count_text = $summery->order_count."回〜".($_POST["repeat"][$i + 1] - 1)."回";
							}else{
								$summery->order_count_text = $summery->order_count."回";
							}
						}else{
							$summery->order_count_text = $summery->order_count."回〜";							
						}
						$summery->order_count_text = str_replace("0回", "新規", $summery->order_count_text);
						$result[$i] = $summery;
					}
				}
			}else{
				foreach($_POST["repeat"] as $i => $repeat){
					if(isset($_POST["repeat"][$i + 1])){
						if($repeat < $summery->order_count && $summery->order_count < $_POST["repeat"][$i+1]){
							if(!isset($result[$i])){
								$summery->order_count = $repeat;
								if($summery->order_count != $_POST["repeat"][$i + 1] - 1){
									$summery->order_count_text = $summery->order_count."回〜".($_POST["repeat"][$i + 1] - 1)."回";
								}else{
									$summery->order_count_text = $summery->order_count."回";
								}
								$summery->order_count_text = str_replace("0回", "新規", $summery->order_count_text);
								$result[$i] = $summery;
							}else{
								$result[$i]->count += $summery->count;
								$result[$i]->subtotal += $summery->subtotal;
								$result[$i]->total += $summery->total;
							}
						}
					}else{
						if($repeat < $summery->order_count){
							if(!isset($result[$i])){
								$summery->order_count = $repeat;
								$summery->order_count_text = $summery->order_time."回〜";
								$summery->order_count_text = str_replace("0回", "新規", $summery->order_count_text);
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
		}
		$_SERVER["ATTRIBUTES"][$params->get("result", "orders")] = $result;
	}
}
?>
