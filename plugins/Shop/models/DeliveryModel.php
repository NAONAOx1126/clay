<?php
/**
 * 配送方法のデータモデルです。
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
class Shop_DeliveryModel extends DatabaseModel{
	function __construct($values = array()){
		$loader = new PluginLoader("Shop");
		parent::__construct($loader->loadTable("DeliveriesTable"), $values);
	}
	
	function findByPrimaryKey($delivery_id){
		$this->findByDeliveryArea($delivery_id);
	}
	
	/**
	 * 代引き手数料を補正したレコードを取得する。
	 */
	function findByDeliveryArea($delivery_id, $pref_id = ""){
		$this->findBy(array("delivery_id" => $delivery_id));
		if(!empty($pref_id)){
			$loader = new PluginLoader("Shop");
			$deliveryArea = $loader->loadModel("DeliveryAreaModel");
			$deliveryArea->findByPrimaryKey($delivery_id, $pref_id);
			if($deliveryArea->delivery_id == $delivery_id && $deliveryArea->pref == $pref_id){
				$this->deliv_fee = $deliveryArea->deliv_fee;
			}			
		}
	}
	
	function __toString(){
		return $this->delivery_name;
	}
}
?>