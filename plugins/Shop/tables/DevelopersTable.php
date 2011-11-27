<?php
class Shop_DevelopersTable extends DatabaseTable{
	function __construct(){
		$this->db = DBFactory::getConnection("shop");
		parent::__construct("shop_developers", "shop");
	}
}
?>
