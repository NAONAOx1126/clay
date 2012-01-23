<?php
/**
 * ### Shop.Summery.OrderDetailRepeat
 * 受注明細データでのサマリを取得する。
 * @param title タイトルに相当するカラムを指定
 * @param summery サマリーに相当するカラムを指定
 * @param result 結果を設定する配列のキーワード
 */
class Shop_Summery_OrderDetailRepeat extends FrameworkModule{
	function execute($params){
		// ローダーを初期化
		$loader = new PluginLoader("Shop");
		
		$order = $loader->loadModel("RepeaterOrderDetailModel");
		
		// ターゲットの前月と次月を取得
		if(!isset($_POST["target"]) || empty($_POST["target"])){
			$_POST["target"] = date("Y-m");
		}
		$_POST["last_target"] = date("Y-m", strtotime("-1 month", strtotime($_POST["target"]."-01 00:00:00")));
		$_POST["next_target"] = date("Y-m", strtotime("+1 month", strtotime($_POST["target"]."-01 00:00:00")));
		
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
		$targets[] = "quantity";
		$targets[] = "price";
		$summerys = $order->summeryByArray($titles, $targets, $conditions, "price");
		
		// リピート回数の入力の先頭に0回〜を加算
		if(!isset($_POST["repeat"]) || !is_array($_POST["repeat"])){
			$_POST["repeat"] = array();
		}
		if(!isset($_POST["repeat"][0]) || $_POST["repeat"][0] > 0){
			array_unshift($_POST["repeat"], "0");
		}
		
		// マージキーを取得
		$merges = explode(",", $params->get("merge"));
		if(!is_array($merges) || empty($merges)){
			$merges = array();
		}
		
		// 集計データをキーに合わせて整理する。
		$resultAll = array();
		foreach($summerys as $summery){
			// 集計用のリピート関数を取得
			$order_repeat = 0;
			foreach($_POST["repeat"] as $i => $repeat){
				if(isset($_POST["repeat"][$i + 1])){
					// 次のリピート回数がある場合、その間にリピート回数があるかチェック
					if($summery["order_repeat"] >= $repeat && $summery["order_repeat"] < $_POST["repeat"][$i + 1]){
						if($repeat + 1 < $_POST["repeat"][$i + 1]){
							if($repeat == 0){
								$order_repeat = "新規〜".$_POST["repeat"][$i + 1]."回";
							}else{
								$order_repeat = $repeat."回〜".($_POST["repeat"][$i + 1] - 1)."回";
							}
						}else{
							if($repeat == 0){
								$order_repeat = "新規";
							}else{
								$order_repeat = $repeat."回";
							}
						}
					}
				}else{
					// 次のリピート回数がない場合は、それより大きいかチェック
					if($summery["order_repeat"] >= $repeat){
						if($repeat == 0){
							$order_repeat = "新規〜";
						}else{
							$order_repeat = $repeat."回〜";
						}
					}
				}
			}
			switch(count($merges)){
				case 1:
					$merges0 = $merges[0];
					if(!isset($resultAll[$summery[$merges0]])){
						$resultAll[$summery[$merges0]][$order_repeat] = array("count" => 0, "quantity" => 0, "price" => 0);
					}
					foreach($titles as $title){
						$resultAll[$summery[$merges0]][$order_repeat][$title] = $summery[$title];
					}
					$resultAll[$summery[$merges0]][$order_repeat]["order_repeat_text"] = $order_repeat;
					$resultAll[$summery[$merges0]][$order_repeat]["count"] += $summery["count"];
					$resultAll[$summery[$merges0]][$order_repeat]["quantity"] += $summery["quantity"];
					$resultAll[$summery[$merges0]][$order_repeat]["price"] += $summery["price"];
					break;
				case 2:
					$merges0 = $merges[0];
					$merges1 = $merges[1];
					if(!isset($resultAll[$summery[$merges0]][$summery[$merges1]])){
						$resultAll[$summery[$merges0]][$summery[$merges1]][$order_repeat] = array("count" => 0, "quantity" => 0, "price" => 0);
					}
					foreach($titles as $title){
						$resultAll[$summery[$merges0]][$summery[$merges1]][$order_repeat][$title] = $summery[$title];
					}
					$resultAll[$summery[$merges0]][$summery[$merges1]][$order_repeat]["order_repeat_text"] = $order_repeat;
					$resultAll[$summery[$merges0]][$summery[$merges1]][$order_repeat]["count"] += $summery["count"];
					$resultAll[$summery[$merges0]][$summery[$merges1]][$order_repeat]["quantity"] += $summery["quantity"];
					$resultAll[$summery[$merges0]][$summery[$merges1]][$order_repeat]["price"] += $summery["price"];
					break;
				case 3:
					$merges0 = $merges[0];
					$merges1 = $merges[1];
					$merges2 = $merges[2];
					if(!isset($resultAll[$summery[$merges0]][$summery[$merges1]][$summery[$merges2]])){
						$resultAll[$summery[$merges0]][$summery[$merges1]][$summery[$merges2]][$order_repeat] = array("count" => 0, "quantity" => 0, "price" => 0);
					}
					foreach($titles as $title){
						$resultAll[$summery[$merges0]][$summery[$merges1]][$summery[$merges2]][$order_repeat][$title] = $summery[$title];
					}
					$resultAll[$summery[$merges0]][$summery[$merges1]][$summery[$merges2]][$order_repeat]["order_repeat_text"] = $order_repeat;
					$resultAll[$summery[$merges0]][$summery[$merges1]][$summery[$merges2]][$order_repeat]["count"] += $summery["count"];
					$resultAll[$summery[$merges0]][$summery[$merges1]][$summery[$merges2]][$order_repeat]["quantity"] += $summery["quantity"];
					$resultAll[$summery[$merges0]][$summery[$merges1]][$summery[$merges2]][$order_repeat]["price"] += $summery["price"];
					break;
			}
		}
		unset($summerys);
		$_SERVER["ATTRIBUTES"]["repeats"] = $_POST["repeat"];
		$_SERVER["ATTRIBUTES"][$params->get("result", "orders")] = $resultAll;
	}
}
?>
