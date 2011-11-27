<?php
/**
 * 受注パッケージのデータモデルです。
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
class Shop_OrderPackageModel extends DatabaseModel{
	function __construct($values = array()){
		$loader = new PluginLoader("Shop");
		parent::__construct($loader->loadTable("OrderPackagesTable"), $values);
	}
	
	function findByPrimaryKey($order_package_id){
		$this->findBy(array("order_package_id" => $order_package_id));
	}
	
	function findAllByOrder($order_id){
		return $this->findAllBy(array("order_id" => $order_id));
	}
	
	function details(){
		$loader = new PluginLoader("Shop");
		$orderDetail = $loader->loadModel("OrderDetailModel");
		return $orderDetail->findAllByOrderPackage($this->order_package_id);		
	}
	
	function delivery($pref_id = ""){
		$loader = new PluginLoader("Shop");
		$delivery = $loader->loadModel("DeliveryModel");
		$delivery->findByDeliveryArea($this->delivery_id, $pref_id);
		return $delivery;	
	}
}
?>