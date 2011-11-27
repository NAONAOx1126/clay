<?php
class Shop_OrderPackagesTable extends DatabaseTable{
	function __construct(){
		$this->db = DBFactory::getConnection("shop");
		parent::__construct("shop_order_packages", "shop");
	}
}
?>
