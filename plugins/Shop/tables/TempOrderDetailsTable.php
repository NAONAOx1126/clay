<?php
class Shop_TempOrderDetailsTable extends DatabaseTable{
	function __construct(){
		$this->db = DBFactory::getConnection("shop");
		parent::__construct("shop_temp_order_details", "shop");
	}
}
?>
