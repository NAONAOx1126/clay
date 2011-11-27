<?php
class Shop_RepeaterOrderPaymentsTable extends DatabaseTable{
	function __construct(){
		$this->db = DBFactory::getConnection("shop");
		parent::__construct("shop_repeater_order_payments", "shop");
	}
}
?>
