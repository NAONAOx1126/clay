<?php
class Shop_CategoriesTable extends DatabaseTable{
	function __construct(){
		$this->db = DBFactory::getConnection("shop");
		parent::__construct("shop_categories", "shop");
	}
}
?>
