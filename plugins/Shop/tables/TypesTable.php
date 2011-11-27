<?php
class Shop_TypesTable extends DatabaseTable{
	function __construct(){
		$this->db = DBFactory::getConnection("shop");
		parent::__construct("shop_types", "shop");
	}
}
?>
