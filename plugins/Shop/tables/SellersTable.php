<?php
class Shop_SellersTable extends DatabaseTable{
	function __construct(){
		$this->db = DBFactory::getConnection("shop");
		parent::__construct("shop_sellers", "shop");
	}
}
?>
