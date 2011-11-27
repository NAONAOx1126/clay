<?php
class Shop_TempOrdersTable extends DatabaseTable{
	function __construct(){
		$this->db = DBFactory::getConnection("shop");
		parent::__construct("shop_temp_orders", "shop");
	}
}
?>
