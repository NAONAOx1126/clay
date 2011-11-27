<?php
class Shop_OrderStatusesTable extends DatabaseTable{
	function __construct(){
		$this->db = DBFactory::getConnection("shop");
		parent::__construct("shop_order_statuses", "shop");
	}
}
?>
