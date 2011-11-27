<?php
class Shop_ProductTypesTable extends DatabaseTable{
	function __construct(){
		$this->db = DBFactory::getConnection("shop");
		parent::__construct("shop_product_types", "shop");
	}
}
?>
