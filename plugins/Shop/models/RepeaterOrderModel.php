<?php
/**
 * 受注のデータモデルです。
 * PHP5.3以上での動作のみ保証しています。
 * 動作自体はPHP5.2以上から動作します。
 *
 * @category  Plugins
 * @package   Shop
 * @author    Naohisa Minagawa <info@sweetberry.jp>
 * @copyright 2010-2012 Naohisa Minagawa
 * @license http://www.apache.org/licenses/LICENSE-2.0.html Apache License, Version 2.0
 * @version   1.0.0
 */
class Shop_RepeaterOrderModel extends DatabaseModel{
	function __construct($values = array()){
		$loader = new PluginLoader("Shop");
		parent::__construct($loader->loadTable("RepeaterOrdersTable"), $values);
	}
	
	public function reconstruct(){
		// データを再構築する。
		$connection = DBFactory::getConnection("shop");
		$connection->beginTransaction();
		$prepare = $connection->prepare("TRUNCATE `shop_repeater_orders`");
		$prepare->execute();
		$sql = "INSERT INTO `shop_repeater_orders` SELECT `shop_orders`.*, count(`counter`.`order_id`) AS `order_repeat`";
		$sql .= " FROM `shop_orders` LEFT JOIN `shop_orders` AS `counter` ON `shop_orders`.`order_email` = `counter`.`order_email` AND `shop_orders`.`order_time` > `counter`.`order_time`";
		$sql .= " GROUP BY `shop_orders`.`order_id` ORDER BY count(`counter`.`order_id`)";
		$prepare = $connection->prepare($sql);
		$prepare->execute();
		$connection->commit();
	}
	
	public function save($db){
		throw new SystemException("This Table is not writable");
	}
	
	function findByPrimaryKey($order_id){
		$this->findBy(array("order_id" => $order_id));
	}
	
	function findByCode($order_code){
		$this->findBy(array("order_code" => $order_code));
	}
	
	function findAllByCustomer($customer_id){
		return $this->findAllBy(array("customer_id" => $customer_id));
	}
	
	function packages(){
		$loader = new PluginLoader("Shop");
		$orderPackage = $loader->loadModel("OrderPackageModel");
		return $orderPackage->findAllByOrder($this->order_id);
	}
	
	function payments(){
		$loader = new PluginLoader("Shop");
		$orderPayment = $loader->loadModel("OrderPaymentModel");
		return $orderPayment->findAllByOrder($this->order_id);
	}
}
?>