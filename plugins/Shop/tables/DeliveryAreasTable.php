<?php
class Shop_DeliveryAreasTable extends DatabaseTable{
	function __construct(){
		$this->db = DBFactory::getConnection("shop");
		parent::__construct("shop_delivery_areas", "shop");
	}
}
?>
