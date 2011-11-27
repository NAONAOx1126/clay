<?php
class Shop_ProductOptionsTable extends DatabaseTable{
	function __construct(){
		$this->db = DBFactory::getConnection("shop");
		parent::__construct("shop_product_options", "shop");
	}
}
?>
