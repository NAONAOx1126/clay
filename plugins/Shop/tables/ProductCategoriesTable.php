<?php
class Shop_ProductCategoriesTable extends DatabaseTable{
	function __construct(){
		$this->db = DBFactory::getConnection("shop");
		parent::__construct("shop_product_categories", "shop");
	}
}
?>
