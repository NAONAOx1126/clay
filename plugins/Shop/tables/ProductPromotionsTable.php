<?php
class Shop_ProductPromotionsTable extends DatabaseTable{
	function __construct(){
		$this->db = DBFactory::getConnection("shop");
		parent::__construct("shop_product_promotions", "shop");
	}
}
?>
