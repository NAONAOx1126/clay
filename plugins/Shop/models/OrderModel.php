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
class Shop_OrderModel extends DatabaseModel{
	function __construct($values = array()){
		$loader = new PluginLoader("Shop");
		parent::__construct($loader->loadTable("OrdersTable"), $values);
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