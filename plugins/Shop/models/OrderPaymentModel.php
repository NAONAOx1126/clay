<?php
/**
 * 受注決済のデータモデルです。
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
class Shop_OrderPaymentModel extends DatabaseModel{
	function __construct($values = array()){
		$loader = new PluginLoader("Shop");
		parent::__construct($loader->loadTable("OrderPaymentsTable"), $values);
	}
	
	function findByPrimaryKey($order_payment_id){
		$this->findBy(array("order_payment_id" => $order_payment_id));
	}
	
	function findAllByOrder($order_id){
		return $this->findAllBy(array("order_id" => $order_id));
	}
	
	function payment(){
		$loader = new PluginLoader("Shop");
		$payment = $loader->loadModel("PaymentModel");
		$payment->findByPrimaryKey($this->payment_id);
		return $payment;
	}
}
?>