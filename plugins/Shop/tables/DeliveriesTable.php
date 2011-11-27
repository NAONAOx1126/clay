<?php
class Shop_DeliveriesTable extends DatabaseTable{
	function __construct(){
		$this->db = DBFactory::getConnection("shop");
		parent::__construct("shop_deliveries", "shop");
	}
}
?>
