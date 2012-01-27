<?php
/**
 * ### Shop.Order.Range
 * 注文情報の日付の最大範囲を取得するためのクラスです。
 * PHP5.3以上での動作のみ保証しています。
 * 動作自体はPHP5.2以上から動作します。
 *
 * @category  Modules
 * @package   Shop
 * @author    Naohisa Minagawa <info@sweetberry.jp>
 * @copyright 2010-2012 Naohisa Minagawa
 * @license http://www.apache.org/licenses/LICENSE-2.0.html Apache License, Version 2.0
 * @version   1.0.0
 * @param key インポートするファイルの形式を特定するためのキー
 */
class Shop_Order_Range extends FrameworkModule{
	function execute($params){
		// ローダーを初期化
		$loader = new PluginLoader("Shop");
		
		// 受注データをクリアする。
		$orders = $loader->loadTable("OrdersTable");
		$select = new DatabaseSelect($orders);
		$select->addColumn("MAX(".$orders->order_time.")", "order_time_max")->addColumn("MIN(".$orders->order_time.")", "order_time_min");
		$result = $select->execute();
		$format = $params->get("format", "Y-m-d H:i:s");
		if(count($result) > 0){
			$_SERVER["ATTRIBUTES"]["ORDER_TIME_MAX"] = date($format, strtotime($result[0]["order_time_max"]));
			$_SERVER["ATTRIBUTES"]["ORDER_TIME_MIN"] = date($format, strtotime($result[0]["order_time_min"]));
		}else{
			$_SERVER["ATTRIBUTES"]["ORDER_TIME_MAX"] = date($format);
			$_SERVER["ATTRIBUTES"]["ORDER_TIME_MIN"] = date($format);
		}
	}
}
?>
