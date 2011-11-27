<?php
class Shop_ProductFlagsTable extends DatabaseTable{
	function __construct(){
		$this->db = DBFactory::getConnection("shop");
		parent::__construct("shop_product_flags", "shop");
	}
}
?>
