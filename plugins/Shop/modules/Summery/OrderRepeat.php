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
		
		$order = $loader->loadModel("RepeaterOrderModel");
		
		// ターゲットの前月と次月を取得
		if(isset($_POST["target"])){
			$_POST["last_target"] = date("Y-m", strtotime("-1 month", strtotime($_POST["target"]."-01 00:00:00")));
			$_POST["next_target"] = date("Y-m", strtotime("+1 month", strtotime($_POST["target"]."-01 00:00:00")));
		}
		// パラメータのsortを並び順変更のキーとして利用
		$sortKey = $_POST[$params->get("order", "order")];
		unset($_POST[$params->get("order", "order")]);
		$conditions = array();
		foreach($_POST as $key => $value){
			if(!empty($value)){
				$conditions[$key] = $value;
			}
		}
		
		// 集計用のタイトルとターゲットを追加して、集計処理の実行
		$titles = explode(",", $params->get("title"));
		if(!is_array($titles) || empty($titles)){
			$titles = array();
		}
		$titles[] = "order_repeat";
		$targets = array();
		$targets[] = ":(SELECT SUM(quantity) FROM shop_order_details; shop_order_packages WHERE shop_order_details.order_package_id = shop_order_packages.order_package_id AND shop_order_packages.order_id = :order_id:):order_quantity:";
		$targets[] = "subtotal";
		$targets[] = "total";
		$summerys = $order->summeryBy($titles, $targets, $conditions);

		// リピート回数の入力の先頭に0回〜を加算
		array_unshift($_POST["repeat"], "0");
		
		// マージキーを取得
		$merges = explode(",", $params->get("merge"));
		if(!is_array($merges) || empty($merges)){
			$merges = array();
		}
		
		$resultAll = array();
		foreach($summerys as $summery){
			switch(count($merges)){
				case 1:
					$merges0 = $merges[0];
					$result = $resultAll[$summery->$merges0];
					break;
				case 2:
					$merges0 = $merges[0];
					$merges1 = $merges[1];
					$result = $resultAll[$summery->$merges0][$summery->$merges1];
					break;
				case 3:
					$merges0 = $merges[0];
					$merges1 = $merges[1];
					$merges2 = $merges[2];
					$result = $resultAll[$summery->$merges0][$summery->$merges1][$summery->$merges2];
					break;
			}
			if(!is_array($result)){
				$result = array();
			}
			if(in_array($summery->order_repeat, $_POST["repeat"])){
				// 注文回数が指定されたリピート回数に含まれる場合
				foreach($_POST["repeat"] as $i => $repeat){
					if($repeat == $summery->order_repeat){
						// 一致したリピート回数の次の設定回数があるかどうか
						if(isset($_POST["repeat"][$i + 1])){
							// 次の設定回数が今の設定回数+1の場合は単独指定、それ以外の場合は範囲していとする。
							if($summery->order_repeat != $_POST["repeat"][$i + 1] - 1){
								$summery->order_repeat_text = $summery->order_repeat."回〜".($_POST["repeat"][$i + 1] - 1)."回";
							}else{
								$summery->order_repeat_text = $summery->order_repeat."回";
							}
						}else{
							// 次の設定回数が無い場合は上限無しの扱いとする。
							$summery->order_repeat_text = $summery->order_repeat."回〜";							
						}
						// ０回の項目を新規に差し替え
						$summery->order_repeat_text = str_replace("0回", "新規", $summery->order_repeat_text);
						$result[$i] = $summery;
					}
				}
			}else{
				// 注文回数が指定されたリピート回数に含まれない場合、指定リピート回数でループ
				foreach($_POST["repeat"] as $i => $repeat){
					if(isset($_POST["repeat"][$i + 1])){
						// 次の指定リピート回数が存在する場合には注文回数が指定リピート回数と次の指定リピート回数の間になっているかをチェック。
						if($repeat < $summery->order_repeat && $summery->order_repeat < $_POST["repeat"][$i+1]){
							if(!isset($result[$i])){
								
								$summery->order_repeat = $repeat;
								if($summery->order_repeat != $_POST["repeat"][$i + 1] - 1){
									$summery->order_repeat_text = $summery->order_repeat."回〜".($_POST["repeat"][$i + 1] - 1)."回";
								}else{
									$summery->order_repeat_text = $summery->order_repeat."回";
								}
								$summery->order_repeat_text = str_replace("0回", "新規", $summery->order_repeat_text);
								$result[$i] = $summery;
							}else{
								$result[$i]->count += $summery->count;
								$result[$i]->subtotal += $summery->subtotal;
								$result[$i]->total += $summery->total;
							}
						}
					}else{
						// 次のリピート回数が存在しない場合には注文回数が指定リピート回数以上かをチェック。
						if($repeat < $summery->order_repeat){
							if(!isset($result[$i])){
								$summery->order_repeat = $repeat;
								$summery->order_repeat_text = $summery->order_time."回〜";
								$summery->order_repeat_text = str_replace("0回", "新規", $summery->order_repeat_text);
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
			switch(count($merges)){
				case 1:
					$merges0 = $merges[0];
					$resultAll[$summery->$merges0] = $result;
					break;
				case 2:
					$merges0 = $merges[0];
					$merges1 = $merges[1];
					$resultAll[$summery->$merges0][$summery->$merges1] = $result;
					break;
				case 3:
					$merges0 = $merges[0];
					$merges1 = $merges[1];
					$merges2 = $merges[2];
					$resultAll[$summery->$merges0][$summery->$merges1][$summery->$merges2] = $result;
					break;
			}
		}
		$_SERVER["ATTRIBUTES"][$params->get("result", "orders")] = $resultAll;
	}
}
?>
