<?php
/**
 * 商品プロモーションのデータモデルです。
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
class Shop_ProductPromotionModel extends DatabaseModel{
	function __construct($values = array()){
		$loader = new PluginLoader("Shop");
		parent::__construct($loader->loadTable("ProductPromotionsTable"), $values);
	}
	
	function findByPrimaryKey($promotion_id){
		$this->findBy(array("promotion_id" => $promotion_id));
	}
	
	function findAllByCode($product_code){
		return $this->findAllBy(array("product_code" => $product_code));
	}
	
	function findAllByPromotionCode($promotion_product_code){
		return $this->findAllBy(array("promotion_product_code" => promotion_product_code));
	}
}
?>