<?php
class Shop_OrderPaymentsTable extends DatabaseTable{
	function __construct(){
		$this->db = DBFactory::getConnection("shop");
		parent::__construct("shop_order_payments", "shop");
	}
}
?>
