<?php
class Shop_OptionTypesTable extends DatabaseTable{
	function __construct(){
		$this->db = DBFactory::getConnection("shop");
		parent::__construct("shop_option_types", "shop");
	}
}
?>
