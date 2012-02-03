<?php
/**
 * ### Shop.Summery.Promotion
 * 受注データでのサマリを取得する。
 * @param title タイトルに相当するカラムを指定
 * @param summery サマリーに相当するカラムを指定
 * @param result 結果を設定する配列のキーワード
 */
class Shop_Summery_Promotion extends FrameworkModule{
	function execute($params){
		// ローダーを初期化
		$loader = new PluginLoader("Shop");
		
		// テーブルのインスタンスを作成する。
		$promoOrder = $loader->loadTable("OrdersTable");
		$promoOrder->setAlias("promo_orders");
		$promoOrderPackage = $loader->loadTable("OrderPackagesTable");
		$promoOrderPackage->setAlias("promo_order_packages");
		$promoOrderDetail = $loader->loadTable("OrderDetailsTable");
		$promoOrderDetail->setAlias("promo_order_details");
		$orderDetail = $loader->loadTable("RepeaterOrderDetailsTable");
		$promotion = $loader->loadTable("ProductPromotionsTable");
		
		// SELECT文を構築する。
		$select = new DatabaseSelect($promoOrderDetail);
		$select->addColumn($promoOrderDetail->product_code, "promotion_product_code");
		$select->addColumn($promoOrderDetail->parent_name, "promotion_parent_name")->addColumn($promoOrderDetail->product_name, "promotion_product_name");
		$select->joinInner($promoOrderPackage, array($promoOrderDetail->order_package_id." = ".$promoOrderPackage->order_package_id));
		$select->joinInner($promoOrder, array($promoOrderPackage->order_id." = ".$promoOrder->order_id));
		$select->joinInner($promotion, array($promoOrderDetail->product_code." = ".$promotion->promotion_product_code));
		$select->joinLeft($orderDetail, array($orderDetail->product_code." = ".$promotion->product_code, $promoOrder->order_email." = ".$orderDetail->order_email, $promoOrder->order_time." < ".$orderDetail->order_time));
		$select->addColumn($orderDetail->product_code);
		$select->addColumn($orderDetail->parent_name)->addColumn($orderDetail->product_name);
		$select->addColumn("SUM(CASE WHEN ".$orderDetail->order_time." IS NOT NULL THEN UNIX_TIMESTAMP(".$orderDetail->order_time.") - UNIX_TIMESTAMP(".$promoOrder->order_time.") ELSE 0 END)", "order_interval");
		$select->addColumn("SUM(CASE WHEN ".$orderDetail->product_code." IS NOT NULL THEN 1 ELSE 0 END)", "order_success");
		$select->addColumn("SUM(1)", "order_all");
		//$select->addColumn("CASE WHEN ".$order->order_time." > ".$promoOrder->order_time." THEN UNIX_TIMESTAMP(".$order->order_time.") - UNIX_TIMESTAMP(".$promoOrder->order_time.") ELSE 0 END", "order_interval");
		//$select->addColumn("CASE WHEN ".$order->order_time." > ".$promoOrder->order_time." THEN 1 ELSE 0 END", "order_success");
		//$select->addColumn("1", "order_all");
		$select->addGroupBy($promoOrderDetail->parent_name)->addGroupBy($promoOrderDetail->product_name);
		$select->addGroupBy($orderDetail->parent_name)->addGroupBy($orderDetail->product_name);
		$select->addOrder($promoOrder->order_code);
		$result = $select->execute();
		
		// 結果を変数に格納
		foreach($result as $baseOrder){
			if(empty($baseOrder["product_code"])){
				foreach($result as $index => $order){
					if(!empty($order["product_code"])){
						$result[$index]["order_all"] += $baseOrder["order_all"];
					}
				}
			}
		}
		$_SERVER["ATTRIBUTES"][$params->get("result", "orders")] = $result;
	}
}
?>
