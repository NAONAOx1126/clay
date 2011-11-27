<?php
/**
 * 決済方法のデータモデルです。
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
class Shop_PaymentChargeModel extends DatabaseModel{
	function __construct($values = array()){
		$loader = new PluginLoader("Shop");
		parent::__construct($loader->loadTable("PaymentChargesTable"), $values);
	}
	
	function findByPrimaryKey($payment_id, $subtotal){
		$this->findBy(array("payment_id" => $payment_id, "subtotal" => $subtotal));
	}
}
?>