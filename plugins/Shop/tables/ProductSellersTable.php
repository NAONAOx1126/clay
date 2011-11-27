<?php
class Shop_ProductSellersTable extends DatabaseTable{
	function __construct(){
		$this->db = DBFactory::getConnection("shop");
		parent::__construct("shop_product_sellers", "shop");
	}
}
?>
