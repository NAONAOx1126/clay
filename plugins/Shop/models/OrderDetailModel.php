<?php
/**
 * 受注詳細のデータモデルです。
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
class Shop_OrderDetailModel extends DatabaseModel{
	function __construct($values = array()){
		$loader = new PluginLoader("Shop");
		parent::__construct($loader->loadTable("OrderDetailsTable"), $values);
	}
	
	function findByPrimaryKey($order_package_id, $product_id, $option1_id = null, $option2_id = null, $option3_id = null, $option4_id = null){
		$this->findBy(array("order_package_id" => $order_package_id, "product_id" => $product_id, "option1_id" => $option1_id, "option2_id" => $option2_id, "option3_id" => $option3_id, "option4_id" => $option4_id));
	}
	
	function findAllByOrderPackage($order_package_id){
		return $this->findAllBy(array("order_package_id" => $order_package_id));
	}
}
?>