<?php
/**
 * ### Shop.Summery.PromotionRepeats
 * リピートサンプル商品でのサマリを取得する。
 * @param title タイトルに相当するカラムを指定
 * @param summery サマリーに相当するカラムを指定
 * @param result 結果を設定する配列のキーワード
 */
class Shop_Summery_PromotionRepeats extends FrameworkModule{
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
		
		// ターゲットの前月と次月を取得
		if(isset($_POST["target"])){
			$_POST["last_target"] = date("Y-m", strtotime("-1 month", strtotime($_POST["target"]."-01 00:00:00")));
			$_POST["next_target"] = date("Y-m", strtotime("+1 month", strtotime($_POST["target"]."-01 00:00:00")));
		}
		
		$summery = array();
		
		// 当月のデータを取得する。
		$select = new DatabaseSelect($promoOrderDetail);
		$select->addColumn($promoOrderDetail->product_code, "promotion_product_code");
		$select->addColumn($promoOrderDetail->parent_name, "promotion_parent_name")->addColumn($promoOrderDetail->product_name, "promotion_product_name");
		$select->joinInner($promoOrderPackage, array($promoOrderDetail->order_package_id." = ".$promoOrderPackage->order_package_id));
		$select->joinInner($promoOrder, array($promoOrderPackage->order_id." = ".$promoOrder->order_id));
		$select->joinInner($promotion, array($promoOrderDetail->product_code." = ".$promotion->promotion_product_code));
		$select->joinLeft($orderDetail, array($orderDetail->product_code." = ".$promotion->product_code, $promoOrder->order_email." = ".$orderDetail->order_email, $promoOrder->order_time." < ".$orderDetail->order_time));
		$select->addColumn("SUM(CASE WHEN ".$orderDetail->order_id." IS NOT NULL THEN 1 ELSE 0 END)", "product_repeats");
		$select->addColumn("SUM(".$orderDetail->quantity.") + ".$promoOrderDetail->quantity, "quantity");
		$select->addColumn("SUM(".$orderDetail->price.") + ".$promoOrderDetail->price, "price");
		$select->addWhere($promoOrder->order_time." < ?", array($_POST["next_target"]."-01 00:00:00"));
		$select->addGroupBy($promoOrderDetail->parent_name)->addGroupBy($promoOrderDetail->product_name);
		$select->addGroupBy($promoOrder->order_email);
		$select->addOrder($promoOrder->order_code);
		$result = $select->execute();
		foreach($result as $data){
			if(!isset($summery[$data["promotion_product_code"]])){
				$summery[$data["promotion_product_code"]] = array("product_name" => $data["promotion_product_name"], $_POST["last_target"] => array(), $_POST["target"] => array());
			}
			if(!isset($summery[$data["promotion_product_code"]][$_POST["target"]][$data["product_repeats"]])){
				$summery[$data["promotion_product_code"]][$_POST["target"]][$data["product_repeats"]] = array("count" => 0, "quantity" => 0, "price" => 0);
			}
			for($i = 0; $i <= $data["product_repeats"]; $i ++){
				$summery[$data["promotion_product_code"]][$_POST["target"]][$i]["count"] ++;
				$summery[$data["promotion_product_code"]][$_POST["target"]][$i]["quantity"] += $data["quantity"];
				$summery[$data["promotion_product_code"]][$_POST["target"]][$i]["price"] += $data["price"];
			}
		}
		
		// 前月のデータを取得する。
		$select = new DatabaseSelect($promoOrderDetail);
		$select->addColumn($promoOrderDetail->product_code, "promotion_product_code");
		$select->addColumn($promoOrderDetail->parent_name, "promotion_parent_name")->addColumn($promoOrderDetail->product_name, "promotion_product_name");
		$select->joinInner($promoOrderPackage, array($promoOrderDetail->order_package_id." = ".$promoOrderPackage->order_package_id));
		$select->joinInner($promoOrder, array($promoOrderPackage->order_id." = ".$promoOrder->order_id));
		$select->joinInner($promotion, array($promoOrderDetail->product_code." = ".$promotion->promotion_product_code));
		$select->joinLeft($orderDetail, array($orderDetail->product_code." = ".$promotion->product_code, $promoOrder->order_email." = ".$orderDetail->order_email, $promoOrder->order_time." < ".$orderDetail->order_time));
		$select->addColumn("SUM(CASE WHEN ".$orderDetail->order_id." IS NOT NULL THEN 1 ELSE 0 END)", "product_repeats");
		$select->addColumn("SUM(".$orderDetail->quantity.") + ".$promoOrderDetail->quantity, "quantity");
		$select->addColumn("SUM(".$orderDetail->price.") + ".$promoOrderDetail->price, "price");
		$select->addWhere($promoOrder->order_time." < ?", array($_POST["target"]."-01 00:00:00"));
		$select->addGroupBy($promoOrderDetail->parent_name)->addGroupBy($promoOrderDetail->product_name);
		$select->addGroupBy($promoOrder->order_email);
		$select->addOrder($promoOrder->order_code);
		$result = $select->execute();
		foreach($result as $data){
			if(!isset($summery[$data["promotion_product_code"]])){
				$summery[$data["promotion_product_code"]] = array("product_name" => $data["promotion_product_name"], $_POST["last_target"] => array(), $_POST["target"] => array());
			}
			if(!isset($summery[$data["promotion_product_code"]][$_POST["last_target"]][$data["product_repeats"]])){
				$summery[$data["promotion_product_code"]][$_POST["last_target"]][$data["product_repeats"]] = array("count" => 0, "quantity" => 0, "price" => 0);
			}
			for($i = 0; $i <= $data["product_repeats"]; $i ++){
				$summery[$data["promotion_product_code"]][$_POST["last_target"]][$i]["count"] ++;
				$summery[$data["promotion_product_code"]][$_POST["last_target"]][$i]["quantity"] += $data["quantity"];
				$summery[$data["promotion_product_code"]][$_POST["last_target"]][$i]["price"] += $data["price"];
			}
		}
		
		// 結果を変数に格納
		$_SERVER["ATTRIBUTES"][$params->get("result", "orders")] = $summery;
	}
}
?>
