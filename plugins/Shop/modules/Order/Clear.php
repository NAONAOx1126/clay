<?php
/**
 * ### Shop.Order.Import
 * 注文情報をクリアするためのクラスです。
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
class Shop_Order_Clear extends FrameworkModule{
	function execute($params){
		// ローダーを初期化
		$loader = new PluginLoader("Shop");
		
		// 受注データをクリアする。
		$orders = $loader->loadTable("OrdersTable");
		$truncate = new DatabaseTruncate($orders);
		$truncate->execute();
		// 受注セットデータをクリアする。
		$orderPackages = $loader->loadTable("OrderPackagesTable");
		$truncate = new DatabaseTruncate($orderPackages);
		$truncate->execute();
		// 受注詳細データをクリアする。
		$orderDetails = $loader->loadTable("OrderDetailsTable");
		$truncate = new DatabaseTruncate($orderDetails);
		$truncate->execute();
		// 受注決済データをクリアする。
		$orderPayments = $loader->loadTable("OrderPaymentsTable");
		$truncate = new DatabaseTruncate($orderPayments);
		$truncate->execute();
		// 受注ステータスデータをクリアする。
		$orderStatuses = $loader->loadTable("OrderStatusesTable");
		$truncate = new DatabaseTruncate($orderStatuses);
		$truncate->execute();
		// 受注データをクリアする。
		$orders = $loader->loadTable("RepeaterOrdersTable");
		$truncate = new DatabaseTruncate($orders);
		$truncate->execute();
		// 受注詳細データをクリアする。
		$orderDetails = $loader->loadTable("RepeaterOrderDetailsTable");
		$truncate = new DatabaseTruncate($orderDetails);
		$truncate->execute();
		// 受注決済データをクリアする。
		$orderPayments = $loader->loadTable("RepeaterOrderPaymentsTable");
		$truncate = new DatabaseTruncate($orderPayments);
		$truncate->execute();
	}
}
?>
