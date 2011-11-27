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
		// データ一括取得のため、処理期限を無効化
		ini_set("max_execution_time", 0);
		
		// ローダーを初期化
		$loader = new PluginLoader("Shop");
		
		// テーブルのインスタンスを作成する。
		$promoOrder = $loader->loadTable("OrdersTable");
		$promoOrder->setAlias("promo_orders");
		$promoOrderPackage = $loader->loadTable("OrderPackagesTable");
		$promoOrderPackage->setAlias("promo_order_packages");
		$promoOrderDetail = $loader->loadTable("OrderDetailsTable");
		$promoOrderDetail->setAlias("promo_order_details");
		$order = $loader->loadTable("OrdersTable");
		$orderPackage = $loader->loadTable("OrderPackagesTable");
		$orderDetail = $loader->loadTable("OrderDetailsTable");
		$promotion = $loader->loadTable("ProductPromotionsTable");
		
		// SELECT文を構築する。
		$select = new DatabaseSelect($promoOrderDetail);
		$select->addColumn($promoOrderDetail->parent_name, "promotion_parent_name")->addColumn($promoOrderDetail->product_name, "promotion_product_name");
		$select->joinInner($promoOrderPackage, array($promoOrderDetail->order_package_id." = ".$promoOrderPackage->order_package_id));
		$select->joinInner($promoOrder, array($promoOrderPackage->order_id." = ".$promoOrder->order_id));
		$select->joinInner($promotion, array($promoOrderDetail->product_code." = ".$promotion->promotion_product_code));
		$select->joinLeft($orderDetail, array($orderDetail->product_code." = ".$promotion->product_code));
		$select->addColumn($orderDetail->parent_name)->addColumn($orderDetail->product_name);
		$select->joinLeft($orderPackage, array($orderDetail->order_package_id." = ".$orderPackage->order_package_id));
		$select->joinLeft($order, array($orderPackage->order_id." = ".$order->order_id, $promoOrder->order_email." = ".$order->order_email));
		$select->addColumn("SUM(CASE WHEN ".$order->order_time." > ".$promoOrder->order_time." THEN UNIX_TIMESTAMP(".$order->order_time.") - UNIX_TIMESTAMP(".$promoOrder->order_time.") ELSE 0 END)", "order_interval");
		$select->addColumn("SUM(CASE WHEN ".$order->order_time." > ".$promoOrder->order_time." THEN 1 ELSE 0 END)", "order_success");
		$select->addColumn("SUM(1)", "order_all");
		//$select->addColumn("CASE WHEN ".$order->order_time." > ".$promoOrder->order_time." THEN UNIX_TIMESTAMP(".$order->order_time.") - UNIX_TIMESTAMP(".$promoOrder->order_time.") ELSE 0 END", "order_interval");
		//$select->addColumn("CASE WHEN ".$order->order_time." > ".$promoOrder->order_time." THEN 1 ELSE 0 END", "order_success");
		//$select->addColumn("1", "order_all");
		$select->addGroupBy($promoOrderDetail->parent_name)->addGroupBy($promoOrderDetail->product_name);
		$select->addGroupBy($orderDetail->parent_name)->addGroupBy($orderDetail->product_name);
		$select->addOrder($promoOrder->order_code);
		$result = $select->execute();
		
		// 結果を変数に格納
		$_SERVER["ATTRIBUTES"][$params->get("result", "orders")] = $result;
	}
}
?>
