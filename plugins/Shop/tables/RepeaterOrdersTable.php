<?php
class Shop_RepeaterOrdersTable extends DatabaseTable{
	function __construct(){
		$this->db = DBFactory::getConnection("shop");
		parent::__construct("shop_repeater_orders", "shop");
	}
}
?>
