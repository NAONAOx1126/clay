<?php
/**
 * ### Shop.Order.List
 * 商品のリストを取得する。
 * @param item １ページあたりの件数
 * @param delta 現在ページの前後に表示するページ数
 * @param category【カテゴリタイプ】 商品に紐付けするカテゴリ（条件にしない場合は空文字を設定）
 * @param result 結果を設定する配列のキーワード
 */
class Shop_Order_Export extends FrameworkModule{
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
		$totalOrders = $order->countBy($conditions);
		
		// 1000件ずつデータを取得する。
		$list = array();
		for($i = 0; $i < $totalOrders; $i += 1000){
			// 取得する件数を絞り込み
			$order->limit(1000, $i);
			$orders = $order->findAllBy($conditions, $sortKey);
			foreach($orders as $order){
				$item = array();
				$orderPayments = $order->payments();
				$payment = $orderPayments[0]->payment();
				foreach($payment->toArray() as $key => $value){
					$item[$key] = $value;
				}
				foreach($orderPayments[0]->toArray() as $key => $value){
					$item[$key] = $value;
				}
				$orderPackages = $order->packages();
				foreach($orderPackages as $orderPackage){
					$delivery = $orderPackage->delivery();
					foreach($delivery->toArray() as $key => $value){
						$item[$key] = $value;
					}
					$details = $orderPackage->details();
					foreach($details as $detail){
						foreach($detail->toArray() as $key => $value){
							$item[$key] = $value;
						}
						foreach($orderPackage->toArray() as $key => $value){
							$item[$key] = $value;
						}
						foreach($order->toArray() as $key => $value){
							$item[$key] = $value;
						}
						$list[] = $item;
					}
				}
			}
		}
		$_SERVER["ATTRIBUTES"][$params->get("result", "orders")] = $list;
	}
}
?>
