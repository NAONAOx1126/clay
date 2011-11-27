<?php
class Shop_OrdersTable extends DatabaseTable{
	function __construct(){
		$this->db = DBFactory::getConnection("shop");
		parent::__construct("shop_orders", "shop");
	}
}
?>
