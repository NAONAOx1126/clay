<?php
class Shop_ProductDevelopersTable extends DatabaseTable{
	function __construct(){
		$this->db = DBFactory::getConnection("shop");
		parent::__construct("shop_product_developers", "shop");
	}
}
?>
