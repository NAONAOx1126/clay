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
class Shop_PaymentModel extends DatabaseModel{
	function __construct($values = array()){
		$loader = new PluginLoader("Shop");
		parent::__construct($loader->loadTable("PaymentsTable"), $values);
	}
	
	function findByPrimaryKey($payment_id){
		$this->findBy(array("payment_id" => $payment_id));
	}
	
	/**
	 * 代引き手数料を補正したレコードを取得する。
	 */
	function findByPaymentTotal($payment_id, $subtotal){
		$this->findByPrimaryKey($payment_id);
		
		// 補正用モデルを読み込み
		$loader = new PluginLoader("Shop");
		$paymentCharge = $loader->loadModel("PaymentChargeModel");
		$paymentCharge->findByPrimaryKey($payment_id, $subtotal);
		if($paymentCharge->payment_id == $payment_id && $paymentCharge->subtotal == $subtotal){
			$this->charge = $paymentCharge->charge;
		}
	}
	
	function __toString(){
		return $this->payment_name;
	}
}
?>