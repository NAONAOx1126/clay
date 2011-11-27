<?php
class Shop_CategoryTypesTable extends DatabaseTable{
	function __construct(){
		$this->db = DBFactory::getConnection("shop");
		parent::__construct("shop_category_types", "shop");
	}
}
?>
