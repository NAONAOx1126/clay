<?php
class Shop_ProductsTable extends DatabaseTable{
	function __construct(){
		$this->db = DBFactory::getConnection("shop");
		parent::__construct("shop_products", "shop");
	}
}
?>
